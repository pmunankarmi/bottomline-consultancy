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
