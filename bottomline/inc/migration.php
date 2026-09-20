<?php
/** Explicit, create-only migration. Never invoked by activation or frontend requests. */
defined( 'ABSPATH' ) || exit();
add_action(
	'admin_menu',
	function () {
		add_theme_page(
			__( 'Bottom Line Setup', 'bottomline' ),
			__( 'Bottom Line Setup', 'bottomline' ),
			'manage_options',
			'bl-setup',
			function () {
				if ( ! current_user_can( 'manage_options' ) ) {
					return;
				}
				echo '<div class="wrap"><h1>' .
					esc_html__( 'Bottom Line Setup', 'bottomline' ) .
					'</h1><p>' .
					esc_html__(
						'Import the original pages, shared settings, menus and team. Images are not bundled; upload and assign them through the Media Library and ACF fields. Existing pages and fields, including cleared fields, are preserved. Back up the site before the first import. This only runs when you press the button.',
						'bottomline',
					) .
					'</p>';
				if ( isset( $_GET['imported'] ) ) {
					echo '<div class="notice notice-success"><p>' .
						esc_html__( 'Import finished. Review the report below.', 'bottomline' ) .
						'</p></div>';
				}
				$report = get_option( 'bl_import_report', array() );
				if ( $report ) {
					echo '<ul>';
					foreach ( $report as $line ) {
						echo '<li>' . esc_html( $line ) . '</li>';
					}
					echo '</ul>';
				}
				if ( bl_acf_ready() ) {
					echo '<form method="post" action="' .
						esc_url( admin_url( 'admin-post.php' ) ) .
						'"><input type="hidden" name="action" value="bl_import">';
					wp_nonce_field( 'bl_import' );
					submit_button( __( 'Import missing content', 'bottomline' ) );
					echo '</form>';
				}
				echo '</div>';
			},
		);
	}
);
function bl_import_asset( $asset, &$report, $alt = '' ) {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'meta_key'       => '_bl_source_asset',
			'meta_value'     => $asset,
			'fields'         => 'ids',
			'posts_per_page' => 1,
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}
	// Content images belong in the Media Library, not in the lightweight theme.
	return 0;
}
function bl_migrate_value( $value, $pages, &$report ) {
	if ( is_array( $value ) && isset( $value['asset'] ) ) {
		$id = bl_import_asset( $value['asset'], $report, $value['alt'] ?? '' );
		return $id;
	}
	if ( is_array( $value ) && isset( $value['page'] ) ) {
		return $pages[ $value['page'] ] ?? 0;
	}
	if ( is_array( $value ) && isset( $value['page_link'] ) ) {
		return array(
			'title'  => '',
			'url'    => isset( $pages[ $value['page_link'] ] ) ? get_permalink( $pages[ $value['page_link'] ] ) : '',
			'target' => '',
		);
	}
	if ( is_array( $value ) ) {
		foreach ( $value as $key => $child ) {
			$value[ $key ] = bl_migrate_value( $child, $pages, $report );
		}
	}
	return $value;
}
function bl_meta_exists( $target, $name ) {
	return $target === 'option'
		? get_option( 'options_' . $name, null ) !== null || get_option( '_options_' . $name, null ) !== null
		: metadata_exists( 'post', $target, $name ) || metadata_exists( 'post', $target, '_' . $name );
}
function bl_import_content() {
	if ( ! current_user_can( 'manage_options' ) || ! bl_acf_ready() ) {
		return new WP_Error( 'permission', __( 'Administrator access and ACF Pro are required.', 'bottomline' ) );
	}
	if ( ! add_option( 'bl_import_lock', time(), '', false ) ) {
		if ( (int) get_option( 'bl_import_lock' ) < time() - 900 ) {
			delete_option( 'bl_import_lock' );
		}
		return new WP_Error( 'locked', __( 'An import is running. Try again later.', 'bottomline' ) );
	}
	$report = array();
	$pages  = array();
	$data   = require __DIR__ . '/migration-data.php';
	$schema = require __DIR__ . '/field-schema.php';
	try {
		$titles = array(
			'index'      => 'Home',
			'about'      => 'About us',
			'services'   => 'Services',
			'team'       => 'Our Team',
			'clients'    => 'Our Clients',
			'contact'    => 'Book Your Free Consultation Today',
			'industries' => 'Industries We Serve',
		);
		foreach ( $titles as $slug => $title ) {
			$found    = get_posts(
				array(
					'post_type'      => 'page',
					'post_status'    => array( 'publish', 'draft', 'private', 'pending', 'trash' ),
					'meta_key'       => '_bl_source_page',
					'meta_value'     => $slug,
					'posts_per_page' => 1,
				)
			);
			$existing = $found ? $found[0] : get_page_by_path( $slug === 'index' ? 'home' : $slug );
			if ( $existing ) {
				$pages[ $slug ] = $existing->ID;
				continue;
			}
			$id = wp_insert_post(
				array(
					'post_type'   => 'page',
					'post_status' => 'publish',
					'post_title'  => $title,
					'post_name'   => $slug === 'index' ? 'home' : $slug,
					'meta_input'  => array(
						'_bl_source_page'   => $slug,
						'_wp_page_template' => $slug === 'index' ? 'default' : 'page-templates/' . $slug . '.php',
					),
				),
				true,
			);
			if ( is_wp_error( $id ) ) {
				throw new RuntimeException( $id->get_error_message() );
			}
			$pages[ $slug ] = $id;
			$report[]       = sprintf( __( 'Created page: %s', 'bottomline' ), $title );
		}
		foreach ( $data['scopes'] as $scope => $values ) {
			if ( $scope === 'team_post' ) {
				continue;
			}
			foreach ( $values as $name => $value ) {
				$target =
					in_array( $scope, array( 'options', 'testimonials' ), true ) || ( $scope === 'clients' && $name === 'clients' )
						? 'option'
						: $pages[ $scope === 'home' ? 'index' : $scope ] ?? 0;
				if ( ! $target || bl_meta_exists( $target, $name ) ) {
					continue;
				}
				// Existing unowned pages are not populated behind an administrator's back.
				if ( $target !== 'option' && ! get_post_meta( $target, '_bl_source_page', true ) ) {
					continue;
				}
				$definition = current( array_filter( $schema[ $scope ], fn( $f ) => $f['name'] === $name ) );
				if ( $scope === 'options' && $name === 'site_logo' && get_theme_mod( 'custom_logo' ) ) {
					bl_sync_logo( get_theme_mod( 'custom_logo' ), 'WordPress' );
					continue;
				}
				try {
					$resolved = bl_migrate_value( $value, $pages, $report );
					update_field( $definition['key'], $resolved, $target );
				} catch ( RuntimeException $e ) {
					$report[] = sprintf(
						__( 'Skipped incomplete field; rerun to retry: %1$s / %2$s', 'bottomline' ),
						$scope,
						$name,
					);
				}
			}
		}
		foreach ( $data['team'] as $order => $member ) {
			$source   = $member['source'] ?? sanitize_title( $member['name'] );
			$existing = get_posts(
				array(
					'post_type'      => 'team',
					'post_status'    => array( 'publish', 'draft', 'private', 'pending', 'trash' ),
					'meta_key'       => '_bl_source_member',
					'meta_value'     => $source,
					'posts_per_page' => 1,
				)
			);
			if ( $existing ) {
				$id = $existing[0]->ID;
			} else {
				if ( get_page_by_path( $source, OBJECT, 'team' ) ) {
					continue;
				}
				$id = wp_insert_post(
					array(
						'post_type'   => 'team',
						'post_status' => 'publish',
						'post_title'  => $member['name'],
						'post_name'   => $source,
						'menu_order'  => $order,
						'meta_input'  => array( '_bl_source_member' => $source ),
					),
					true,
				);
				if ( is_wp_error( $id ) ) {
					$report[] = $id->get_error_message();
					continue;
				}
			}
			foreach ( $schema['team_post'] as $field ) {
				if ( ! bl_meta_exists( $id, $field['name'] ) && isset( $member[ $field['name'] ] ) ) {
					update_field( $field['key'], $member[ $field['name'] ], $id );
				}
			}
		}
		// Establish initial reading/menu settings only once. Clearing them later is respected.
		if ( ! get_option( 'bl_initial_configuration_done' ) ) {
			if ( ! get_option( 'page_on_front' ) && get_post_meta( $pages['index'], '_bl_source_page', true ) ) {
				update_option( 'page_on_front', $pages['index'] );
				update_option( 'show_on_front', 'page' );
			}
			$locations = get_theme_mod( 'nav_menu_locations', array() );
			foreach ( array( 'primary', 'footer' ) as $location ) {
				if ( ! empty( $locations[ $location ] ) ) {
					continue;
				}
				$name = 'Bottom Line ' . ucfirst( $location );
				$menu = wp_get_nav_menu_object( $name );
				if ( $menu ) {
					$menu_id = $menu->term_id;
				} else {
					$menu_id = wp_create_nav_menu( $name );
					if ( is_wp_error( $menu_id ) ) {
						continue;
					}
					$labels =
						$location === 'primary'
							? array(
								'index'    => 'Home',
								'about'    => 'About',
								'services' => 'Services',
								'team'     => 'Team',
								'clients'  => 'Clients',
								'contact'  => 'Contact',
							)
							: array(
								'about'    => 'About',
								'services' => 'Services',
								'team'     => 'Team',
								'clients'  => 'Clients',
								'contact'  => 'Contact',
							);
					foreach ( $labels as $slug => $label ) {
						wp_update_nav_menu_item(
							$menu_id,
							0,
							array(
								'menu-item-title'     => $label,
								'menu-item-object-id' => $pages[ $slug ],
								'menu-item-object'    => 'page',
								'menu-item-type'      => 'post_type',
								'menu-item-status'    => 'publish',
							)
						);
					}
					if ( $location === 'primary' ) {
						wp_update_nav_menu_item(
							$menu_id,
							0,
							array(
								'menu-item-title'     => 'Get in Touch',
								'menu-item-object-id' => $pages['contact'],
								'menu-item-object'    => 'page',
								'menu-item-type'      => 'post_type',
								'menu-item-status'    => 'publish',
								'menu-item-classes'   => 'nav-cta',
							)
						);
					}
				}
				$locations[ $location ] = $menu_id;
			}
			set_theme_mod( 'nav_menu_locations', $locations );
			update_option( 'bl_initial_configuration_done', 1, false );
		}
		flush_rewrite_rules( false );
		$report[] = __( 'Existing administrator content was preserved.', 'bottomline' );
		update_option( 'bl_import_report', $report, false );
	} finally {
		delete_option( 'bl_import_lock' );
	}
	return $report;
}
add_action(
	'admin_post_bl_import',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Access denied.', 'bottomline' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'bl_import' );
		$result = bl_import_content();
		if ( is_wp_error( $result ) ) {
			wp_die( esc_html( $result->get_error_message() ) );
		}
		wp_safe_redirect( admin_url( 'themes.php?page=bl-setup&imported=1' ) );
		exit();
	}
);
