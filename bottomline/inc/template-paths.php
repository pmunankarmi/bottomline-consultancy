<?php
/**
 * Custom page template paths and backwards compatibility.
 *
 * @package Bottomline
 */
defined( 'ABSPATH' ) || exit();

/**
 * Return the previous and current paths for a custom page template.
 *
 * @param string $name Template name without a file extension.
 * @return string[]
 */
function bl_page_template_paths( $name ) {
	return array( 'page-' . $name . '.php', 'page-templates/' . $name . '.php' );
}

/**
 * Check a page assignment while supporting themes installed before version 1.3.
 *
 * @param string   $name    Template name without a file extension.
 * @param int|null $post_id Page ID, or the current page when omitted.
 * @return bool
 */
function bl_uses_page_template( $name, $post_id = null ) {
	return in_array( get_page_template_slug( $post_id ?? get_queried_object_id() ), bl_page_template_paths( $name ), true );
}

/**
 * Resolve an old page assignment before an administrator has opened the site.
 *
 * @param string $template Resolved template filename.
 * @return string
 */
function bl_resolve_legacy_page_template( $template ) {
	$assigned = get_page_template_slug();
	foreach ( array( 'about', 'services', 'clients', 'team', 'contact', 'industries' ) as $name ) {
		$paths = bl_page_template_paths( $name );
		if ( $assigned === $paths[0] ) {
			$located = locate_template( $paths[1] );
			return $located ?: $template;
		}
	}
	return $template;
}
add_filter( 'page_template', 'bl_resolve_legacy_page_template' );

/**
 * Move existing template assignments without changing page or ACF content.
 *
 * Runs once on an administrator request after the theme update.
 */
function bl_migrate_page_template_paths() {
	if ( ! current_user_can( 'manage_options' ) || get_option( 'bl_template_paths_version' ) ) {
		return;
	}
	foreach ( array( 'about', 'services', 'clients', 'team', 'contact', 'industries' ) as $name ) {
		$paths = bl_page_template_paths( $name );
		$pages = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => array( 'publish', 'draft', 'private', 'pending', 'future', 'trash' ),
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_key'       => '_wp_page_template',
				'meta_value'     => $paths[0],
			)
		);
		foreach ( $pages as $page_id ) {
			update_post_meta( $page_id, '_wp_page_template', $paths[1] );
		}
	}
	update_option( 'bl_template_paths_version', 1, false );
}
add_action( 'admin_init', 'bl_migrate_page_template_paths' );
