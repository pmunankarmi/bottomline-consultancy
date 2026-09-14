<?php
/** WordPress setup and safe rendering helpers. */
defined( 'ABSPATH' ) || exit();
add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'bottomline', get_template_directory() . '/languages' );
		add_theme_support( 'title-tag' );
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 160,
				'width'       => 500,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
		register_nav_menus(
			array(
				'primary' => __( 'Primary navigation', 'bottomline' ),
				'footer'  => __( 'Footer navigation', 'bottomline' ),
			)
		);
	}
);
add_action(
	'wp_enqueue_scripts',
	function () {
		$uri = get_template_directory_uri();
		wp_enqueue_style(
			'bl-font',
			'https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap',
			array(),
			null,
		);
		foreach ( array( 'styles', 'wordpress' ) as $file ) {
			wp_enqueue_style(
				'bl-' . $file,
				$uri . '/assets/css/' . $file . '.css',
				$file === 'WordPress' ? array( 'bl-styles' ) : array( 'bl-font' ),
				filemtime( get_template_directory() . '/assets/css/' . $file . '.css' ),
			);
		}
		wp_enqueue_script(
			'bl-main',
			$uri . '/assets/js/main.js',
			array(),
			filemtime( get_template_directory() . '/assets/js/main.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			),
		);
	}
);
add_action(
	'init',
	function () {
		register_post_type(
			'team',
			array(
				'labels'       => array(
					'name'          => __( 'Team', 'bottomline' ),
					'singular_name' => __( 'Team member', 'bottomline' ),
					'add_new_item'  => __( 'Add team member', 'bottomline' ),
				),
				'public'       => true,
				'has_archive'  => 'our-team',
				'rewrite'      => array( 'slug' => 'team-member' ),
				'menu_icon'    => 'dashicons-groups',
				'supports'     => array( 'title', 'page-attributes' ),
				'show_in_rest' => true,
			)
		);
	}
);
function bl_acf_ready() {
	return function_exists( 'acf_add_options_page' ) &&
		function_exists( 'get_field' ) &&
		function_exists( 'acf_get_field_type' ) &&
		acf_get_field_type( 'repeater' );
}
function bl_field( $name, $source = null ) {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}
	$value = get_field( $name, $source ?? get_the_ID() );
	return $value === false || $value === null ? '' : $value;
}
function bl_rows( $value ) {
	return is_array( $value ) ? array_values( array_filter( $value, 'is_array' ) ) : array();
}
function bl_text( $value ) {
	return is_scalar( $value ) ? nl2br( esc_html( (string) $value ) ) : '';
}
function bl_has_content( $value ) {
	if ( is_array( $value ) ) {
		foreach ( $value as $key => $item ) {
			if ( $key !== 'layout' && ! str_starts_with( (string) $key, 'icon' ) && bl_has_content( $item ) ) {
				return true;
			}
		}
		return false;
	}
	return is_scalar( $value ) && trim( (string) $value ) !== '';
}
function bl_image( $id, $class = '', $alt = null ) {
	$id = absint( $id );
	if ( ! $id || ! wp_attachment_is_image( $id ) ) {
		return;
	}
	$attributes = array(
		'class'    => $class,
		'decoding' => 'async',
	);
	if ( $alt !== null ) {
		$attributes['alt'] = $alt;
	}
	echo wp_get_attachment_image( $id, 'full', false, $attributes );
}
function bl_link_url( $link ) {
	if ( ! is_array( $link ) ) {
		return '';
	}
	// WordPress resolves same-site stored links to the current permalink where possible.
	$url = $link['url'] ?? '';
	$id  = url_to_postid( $url );
	return $id ? get_permalink( $id ) : $url;
}
add_filter(
	'nav_menu_link_attributes',
	function ( $atts, $item, $args ) {
		if ( $args->theme_location === 'primary' && in_array( 'nav-cta', (array) $item->classes, true ) ) {
			$atts['class'] = 'btn btn-primary';
		}
		return $atts;
	},
	10,
	3,
);
add_action(
	'admin_notices',
	function () {
		if ( current_user_can( 'manage_options' ) && ! bl_acf_ready() ) {
			echo '<div class="notice notice-error"><p>' .
			esc_html__(
				'Bottom Line requires ACF Pro for editable content and the explicit content importer. Install and activate ACF Pro; your saved content will be retained.',
				'bottomline',
			) .
				'</p></div>';
		}
	}
);

add_action(
	'wp_head',
	function () {
		if ( ! is_singular() || defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
			return;
		}
		$description = bl_field( 'meta_description' );
		if ( is_string( $description ) && $description !== '' ) {
			echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
		}
	},
	1,
);

function bl_number( $value ) {
	if ( ! is_numeric( $value ) ) {
		return '';
	}
	$parts = explode( '.', (string) $value );
	return number_format_i18n( (float) $value, isset( $parts[1] ) ? strlen( $parts[1] ) : 0 );
}

/** Preserve the theme logo class on WordPress custom-logo images. */
add_filter(
	'get_custom_logo_image_attributes',
	function ( $attributes ) {
		$attributes['class'] = 'custom-logo brand-logo';
		return $attributes;
	}
);
