<?php
/** Small GitHub Releases integration using WordPress's native theme updater. */
defined( 'ABSPATH' ) || exit();
const BL_UPDATE_REPOSITORY = 'https://github.com/pmunankarmi/bottomline-consultancy';
const BL_UPDATE_API        = 'https://api.github.com/repos/pmunankarmi/bottomline-consultancy/releases/latest';

function bl_update_request( $url ) {
	$response = wp_safe_remote_get(
		$url,
		array(
			'timeout'             => 10,
			'redirection'         => 5,
			'limit_response_size' => 131072,
			'headers'             => array(
				'Accept'     => 'application/vnd.github+json',
				'User-Agent' => 'Bottomline-WordPress-Theme-Updater',
			),
		)
	);
	if ( is_wp_error( $response ) ) {
		return $response;
	}
	$status = wp_remote_retrieve_response_code( $response );
	if ( $status !== 200 ) {
		return new WP_Error(
			'bl_release_http',
			$status === 404
				? __( 'No published theme release is available yet.', 'bottomline' )
				: __( 'GitHub could not be reached or its request limit was reached. Try again later.', 'bottomline' ),
		);
	}
	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	return is_array( $data )
		? $data
		: new WP_Error( 'bl_release_json', __( 'GitHub returned invalid release information.', 'bottomline' ) );
}
function bl_release_asset( $release, $name ) {
	$tag      = $release['tag_name'] ?? '';
	$expected = BL_UPDATE_REPOSITORY . '/releases/download/' . rawurlencode( $tag ) . '/' . $name;
	foreach ( bl_rows( $release['assets'] ?? array() ) as $asset ) {
		if (
			( $asset['name'] ?? '' ) === $name &&
			( $asset['state'] ?? '' ) === 'uploaded' &&
			( $asset['browser_download_url'] ?? '' ) === $expected
		) {
			return $expected;
		}
	}
	return '';
}
function bl_github_release( $force = false ) {
	if ( ! $force ) {
		$cached = get_site_transient( 'bl_github_release' );
		if ( is_array( $cached ) ) {
			return isset( $cached['error'] ) ? new WP_Error( 'bl_release_cached', $cached['error'] ) : $cached;
		}
	}
	$release = bl_update_request( BL_UPDATE_API );
	if ( ! is_wp_error( $release ) ) {
		$tag = $release['tag_name'] ?? '';
		if (
			! is_string( $tag ) ||
			! preg_match( '/^v?(\d+\.\d+\.\d+)$/', $tag, $match ) ||
			! empty( $release['draft'] ) ||
			! empty( $release['prerelease'] )
		) {
			$release = new WP_Error( 'bl_release_version', __( 'No stable theme release is available.', 'bottomline' ) );
		} else {
			$package      = bl_release_asset( $release, 'bottomline.zip' );
			$manifest_url = bl_release_asset( $release, 'bottomline-update.json' );
			if ( ! $package || ! $manifest_url ) {
				$release = new WP_Error(
					'bl_release_assets',
					__( 'The release is missing its installable theme ZIP or update manifest.', 'bottomline' ),
				);
			} else {
				$manifest = bl_update_request( $manifest_url );
				if ( is_wp_error( $manifest ) ) {
					$release = $manifest;
				} elseif (
					( $manifest['version'] ?? '' ) !== $match[1] ||
					! is_string( $manifest['requires'] ?? null ) ||
					! preg_match( '/^\d+\.\d+(\.\d+)?$/', $manifest['requires'] ) ||
					! is_string( $manifest['requires_php'] ?? null ) ||
					! preg_match( '/^\d+\.\d+(\.\d+)?$/', $manifest['requires_php'] )
				) {
					$release = new WP_Error(
						'bl_release_manifest',
						__( 'The release manifest is invalid or does not match the release tag.', 'bottomline' ),
					);
				} else {
					$release = array(
						'version'      => $match[1],
						'notes'        => is_string( $release['body'] ?? null ) ? $release['body'] : '',
						'url'          => BL_UPDATE_REPOSITORY . '/releases/tag/' . rawurlencode( $tag ),
						'package'      => $package,
						'requires'     => $manifest['requires'],
						'requires_php' => $manifest['requires_php'],
					);
				}
			}
		}
	}
	set_site_transient(
		'bl_github_release',
		is_wp_error( $release ) ? array( 'error' => $release->get_error_message() ) : $release,
		is_wp_error( $release ) ? 15 * MINUTE_IN_SECONDS : 5 * MINUTE_IN_SECONDS,
	);
	return $release;
}
add_filter(
	'update_themes_github.com',
	function ( $update, $theme_data, $stylesheet ) {
		if ( ( $theme_data['UpdateURI'] ?? '' ) !== BL_UPDATE_REPOSITORY || $stylesheet !== get_template() ) {
			return $update;
		}
		$release = bl_github_release();
		if ( is_wp_error( $release ) ) {
			return $update;
		}
		return array_merge(
			$release,
			array(
				'id'    => BL_UPDATE_REPOSITORY,
				'theme' => $stylesheet,
				'url'   => bl_release_details_url(),
			)
		);
	},
	10,
	3,
);

/**
 * Refresh native update notices when an administrator visits the dashboard.
 *
 * The short cache avoids requesting GitHub on every admin page. WordPress also
 * checks in the background through its existing wp_update_themes cron event.
 */
function bl_refresh_theme_updates() {
	if ( ! current_user_can( 'update_themes' ) || get_site_transient( 'bl_update_check_due' ) ) {
		return;
	}
	set_site_transient( 'bl_update_check_due', 1, 5 * MINUTE_IN_SECONDS );
	bl_github_release( true );
	delete_site_transient( 'update_themes' );
	wp_update_themes();
}
add_action( 'admin_init', 'bl_refresh_theme_updates' );

add_action(
	'upgrader_process_complete',
	function ( $upgrader, $options ) {
		if (
			( $options['type'] ?? '' ) === 'theme' &&
			in_array( get_template(), (array) ( $options['themes'] ?? array() ), true )
		) {
			delete_site_transient( 'bl_github_release' );
		}
	},
	10,
	2,
);

/** Return the local URL used by WordPress's version-details dialog. */
function bl_release_details_url() {
	return admin_url( 'admin-post.php?action=bl_release_details' );
}

/** Repair cached update links from theme versions that pointed directly to GitHub. */
function bl_local_update_details( $updates ) {
	if ( ! is_object( $updates ) ) {
		return $updates;
	}
	$stylesheet = get_template();
	foreach ( array( 'response', 'no_update' ) as $group ) {
		if ( isset( $updates->{$group}[ $stylesheet ] ) && is_array( $updates->{$group}[ $stylesheet ] ) ) {
			$entry = $updates->{$group}[ $stylesheet ];
			if ( ( $entry['id'] ?? '' ) === BL_UPDATE_REPOSITORY || str_starts_with( $entry['url'] ?? '', BL_UPDATE_REPOSITORY . '/releases/' ) ) {
				$updates->{$group}[ $stylesheet ]['url'] = bl_release_details_url();
			}
		}
	}
	return $updates;
}
add_filter( 'site_transient_update_themes', 'bl_local_update_details' );

/** Render escaped release information inside the local dialog. */
function bl_render_release_details( $release ) {
	echo '<main class="wrap"><h1>' . esc_html__( 'Bottom Line Consultancy — Version details', 'bottomline' ) . '</h1>';
	if ( is_wp_error( $release ) ) {
		echo '<p>' . esc_html( $release->get_error_message() ) . '</p>';
	} else {
		echo '<h2>' . esc_html( sprintf( __( 'Version %s', 'bottomline' ), $release['version'] ) ) . '</h2>';
		echo '<p>' . esc_html( sprintf( __( 'Requires WordPress %1$s or later and PHP %2$s or later.', 'bottomline' ), $release['requires'], $release['requires_php'] ) ) . '</p>';
		echo '<h2>' . esc_html__( 'Release notes', 'bottomline' ) . '</h2>';
		echo '<div style="white-space:pre-wrap;overflow-wrap:anywhere">' . esc_html( $release['notes'] ?: __( 'No release notes were provided.', 'bottomline' ) ) . '</div>';
	}
	echo '<p><a href="' . esc_url( BL_UPDATE_REPOSITORY . '/releases' ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Open releases on GitHub', 'bottomline' ) . '</a></p></main>';
}

/** Serve details on the same origin so the WordPress popup can display them. */
function bl_release_details() {
	if ( ! current_user_can( 'update_themes' ) ) {
		wp_die( esc_html__( 'Access denied.', 'bottomline' ), '', array( 'response' => 403 ) );
	}
	$release = bl_github_release();
	if ( is_array( $release ) && ! array_key_exists( 'notes', $release ) ) {
		$release = bl_github_release( true );
	}
	iframe_header( __( 'Theme version details', 'bottomline' ) );
	bl_render_release_details( $release );
	iframe_footer();
	exit;
}
add_action( 'admin_post_bl_release_details', 'bl_release_details' );
