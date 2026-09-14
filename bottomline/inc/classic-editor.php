<?php
/** Classic editing and lean frontend assets for this PHP/ACF theme. */
defined( 'ABSPATH' ) || exit();
add_filter( 'use_block_editor_for_post', '__return_false', 100 );
add_filter( 'use_block_editor_for_post_type', '__return_false', 100 );
add_filter( 'use_widgets_block_editor', '__return_false', 100 );
add_action(
	'after_setup_theme',
	function () {
		remove_theme_support( 'widgets-block-editor' );
	},
	100,
);

function bl_trim_block_styles() {
	// Retain styling for previously saved block content on generic posts/pages.
	if ( is_singular() && has_blocks( get_post() ) ) {
		return;
	}
	foreach ( array( 'wp-block-library', 'wp-block-library-theme', 'global-styles', 'classic-theme-styles' ) as $handle ) {
		wp_dequeue_style( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'bl_trim_block_styles', 100 );
add_action( 'wp_footer', 'bl_trim_block_styles', 1 );
