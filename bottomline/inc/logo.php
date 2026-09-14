<?php
/** Bidirectional logo synchronization. All stored values are attachment IDs. */
defined( 'ABSPATH' ) || exit();
function bl_logo_id( $value ) {
	$id = is_array( $value ) ? absint( $value['ID'] ?? 0 ) : absint( $value );
	return $id && wp_attachment_is_image( $id ) ? $id : 0;
}
function bl_sync_logo( $value, $from ) {
	static $syncing = false;
	$id             = bl_logo_id( $value );
	if ( $syncing ) {
		return $id;
	}
	$syncing = true;
	try {
		if ( $from === 'acf' && (int) get_theme_mod( 'custom_logo', 0 ) !== $id ) {
			$id ? set_theme_mod( 'custom_logo', $id ) : remove_theme_mod( 'custom_logo' );
		}
		if ( $from === 'WordPress' ) {
			// Raw option writes avoid recursive ACF filters and also work while ACF is disabled.
			$schema = require __DIR__ . '/field-schema.php';
			$logo   = $schema['options'][0];
			update_option( 'options_site_logo', $id, false );
			update_option( '_options_site_logo', $logo['key'], false );
			if ( function_exists( 'acf_flush_value_cache' ) ) {
				acf_flush_value_cache( 'options', 'site_logo' );
				acf_flush_value_cache( 'option', 'site_logo' );
			}
		}
	} finally {
		$syncing = false;
	}
	return $id;
}
add_filter(
	'acf/update_value/name=site_logo',
	function ( $value, $post_id ) {
		return in_array( $post_id, array( 'option', 'options' ), true ) ? bl_sync_logo( $value, 'acf' ) : $value;
	},
	10,
	2,
);
// Run after theme_mods is persisted so removals, Customizer saves and native API changes are covered.
add_action(
	'updated_option',
	function ( $option, $old, $new ) {
		if (
			$option === 'theme_mods_' . get_option( 'stylesheet' ) &&
			( $old['custom_logo'] ?? 0 ) !== ( $new['custom_logo'] ?? 0 )
		) {
			bl_sync_logo( $new['custom_logo'] ?? 0, 'WordPress' );
		}
	},
	10,
	3,
);
add_action(
	'added_option',
	function ( $option, $value ) {
		if (
			$option === 'theme_mods_' . get_option( 'stylesheet' ) &&
			is_array( $value ) &&
			array_key_exists( 'custom_logo', $value )
		) {
			bl_sync_logo( $value['custom_logo'], 'WordPress' );
		}
	},
	10,
	2,
);
add_action(
	'deleted_option',
	function ( $option ) {
		if ( $option === 'theme_mods_' . get_option( 'stylesheet' ) ) {
			bl_sync_logo( 0, 'WordPress' );
		}
	}
);
