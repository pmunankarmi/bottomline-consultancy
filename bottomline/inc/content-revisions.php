<?php
/**
 * Apply the approved September 2026 content corrections once per installation.
 *
 * @package Bottomline
 */
defined( 'ABSPATH' ) || exit;

function bl_apply_september_content_updates() {
	if ( ! current_user_can( 'manage_options' ) || ! bl_acf_ready() || get_option( 'bl_content_revision_20260920' ) ) {
		return;
	}
	$members = get_posts(
		array(
			'post_type'      => 'team',
			'post_status'    => array( 'publish', 'draft', 'private', 'pending' ),
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);
	if ( ! $members ) {
		return;
	}
	$hisham = null;
	$rushdi = null;
	foreach ( $members as $member ) {
		if ( $member->post_title === 'Eli Moutran' ) {
			wp_update_post(
				array(
					'ID'         => $member->ID,
					'post_title' => 'Elie Moutran',
				)
			);
		}
		if ( $member->post_title === 'Khodr Makke' ) {
			wp_update_post(
				array(
					'ID'         => $member->ID,
					'post_title' => 'Al-Khedr Makke',
				)
			);
			if ( bl_field( 'initials', $member->ID ) === 'KM' ) {
				update_field( 'initials', 'AKM', $member->ID );
			}
		}
		if ( $member->post_title === 'Hisham Youssef' ) {
			$hisham = $member->ID;
		}
		if ( in_array( $member->post_title, array( 'Mohammad Rushdi', 'Mohamad Rushdi' ), true ) ) {
			$rushdi = $member->ID;
		}
	}
	if ( $hisham && $rushdi ) {
		$order = array_values( array_diff( wp_list_pluck( $members, 'ID' ), array( $hisham ) ) );
		array_splice( $order, array_search( $rushdi, $order, true ), 0, array( $hisham ) );
		foreach ( $order as $position => $id ) {
			wp_update_post(
				array(
					'ID'         => $id,
					'menu_order' => $position,
				)
			);
		}
	}
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'posts_per_page' => -1,
			'post_status'    => array( 'publish', 'draft', 'private' ),
		)
	);
	foreach ( $pages as $page ) {
		if ( bl_uses_page_template( 'contact', $page->ID ) ) {
			$section = bl_field( 'section_1', $page->ID );
			if ( is_array( $section ) && in_array( $section['label'] ?? '', array( 'Phone', 'WhatsApp/Phone' ), true ) ) {
				$section['label'] = 'WhatsApp/Phone';
				update_field( 'section_1', $section, $page->ID );
			}
		}
	}
	$seed = require __DIR__ . '/migration-data.php';
	$new  = $seed['scopes']['testimonials']['testimonials'][2];
	$rows = bl_rows( bl_field( 'testimonials', 'option' ) );
	if ( $new && ! in_array( 'Jad Abou Hamdan', array_column( $rows, 'author' ), true ) ) {
		$rows[] = $new;
		update_field( 'testimonials', $rows, 'option' );
	}
	if ( $new ) {
		update_option( 'bl_content_revision_20260920', 1, false );
	}
}
add_action( 'admin_init', 'bl_apply_september_content_updates' );

/** Import the supplied biographies once, leaving missing bios and later edits alone. */
function bl_apply_supplied_team_bios() {
	if ( ! current_user_can( 'manage_options' ) || ! bl_acf_ready() || get_option( 'bl_team_bios_20260920_done' ) ) {
		return;
	}
	$bios      = json_decode( file_get_contents( __DIR__ . '/team-bios.json' ), true );
	$completed = (array) get_option( 'bl_team_bios_20260920_members', array() );
	foreach ( $bios as $source => $fields ) {
		if ( in_array( $source, $completed, true ) ) {
			continue;
		}
		$members = get_posts(
			array(
				'post_type'      => 'team',
				'post_status'    => array( 'publish', 'draft', 'private', 'pending' ),
				'posts_per_page' => 1,
				'meta_key'       => '_bl_source_member',
				'meta_value'     => $source,
			)
		);
		if ( ! $members ) {
			continue;
		}
		$id = $members[0]->ID;
		foreach ( $fields as $name => $value ) {
			update_field( $name, $value, $id );
		}
		if ( bl_field( 'biography', $id ) === $fields['biography'] && bl_field( 'position', $id ) === $fields['position'] ) {
			$completed[] = $source;
			update_option( 'bl_team_bios_20260920_members', $completed, false );
		}
	}
	if ( count( $completed ) === count( $bios ) ) {
		update_option( 'bl_team_bios_20260920_done', 1, false );
	}
}
add_action( 'admin_init', 'bl_apply_supplied_team_bios', 20 );

/** Move a legacy team photo once, preserving existing and later thumbnail choices. */
function bl_migrate_team_featured_image( $post_id ) {
	if ( get_post_meta( $post_id, '_bl_featured_image_migrated', true ) ) {
		return;
	}
	$photo_id = absint( get_post_meta( $post_id, 'photo', true ) );
	if ( ! has_post_thumbnail( $post_id ) && $photo_id && wp_attachment_is_image( $photo_id ) ) {
		if ( ! set_post_thumbnail( $post_id, $photo_id ) ) {
			return;
		}
	}
	update_post_meta( $post_id, '_bl_featured_image_migrated', 1 );
}

/** Migrate team photos when an administrator first loads the updated theme. */
function bl_migrate_team_featured_images() {
	if ( ! current_user_can( 'manage_options' ) || get_option( 'bl_team_featured_images_migrated' ) ) {
		return;
	}
	$members = get_posts( array(
		'post_type' => 'team',
		'post_status' => array( 'publish', 'draft', 'private', 'pending', 'future', 'trash' ),
		'posts_per_page' => -1,
		'fields' => 'ids',
	) );
	foreach ( $members as $post_id ) {
		bl_migrate_team_featured_image( $post_id );
		if ( ! get_post_meta( $post_id, '_bl_featured_image_migrated', true ) ) {
			return;
		}
	}
	update_option( 'bl_team_featured_images_migrated', 1, false );
}
add_action( 'admin_init', 'bl_migrate_team_featured_images', 40 );
