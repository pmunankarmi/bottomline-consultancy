<?php
/**
 * Testimonials and the migration from the former theme options repeater.
 *
 * @package Bottomline
 */
defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_post_type(
			'testimonial',
			array(
				'labels'       => array(
					'name'          => __( 'Testimonials', 'bottomline' ),
					'singular_name' => __( 'Testimonial', 'bottomline' ),
					'add_new_item'  => __( 'Add testimonial', 'bottomline' ),
					'edit_item'     => __( 'Edit testimonial', 'bottomline' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => true,
				'menu_icon'    => 'dashicons-format-quote',
				'supports'     => array( 'title', 'page-attributes' ),
			)
		);
	}
);

/** Return published testimonials in the administrator's chosen order. */
function bl_testimonials() {
	$rows = array();
	foreach ( get_posts(
		array(
			'post_type'      => 'testimonial',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'ID'         => 'ASC',
			),
		)
	) as $post ) {
		$quote = bl_field( 'quote', $post->ID );
		if ( ! $quote ) {
			continue;
		}
		$rows[] = array(
			'quote'    => $quote,
			'author'   => $post->post_title,
			'position' => bl_field( 'position', $post->ID ),
			'company'  => bl_field( 'company', $post->ID ),
			'photo'    => bl_field( 'photo', $post->ID ),
		);
	}
	return $rows;
}

/** Copy old entries once; retain the source options as a recovery copy. */
function bl_migrate_testimonial_posts() {
	if ( ! current_user_can( 'manage_options' ) || ! bl_acf_ready() || get_option( 'bl_testimonial_posts_migrated' ) ) {
		return;
	}
	$count = (int) get_option( 'options_testimonials', 0 );
	if ( ! $count ) {
		return;
	}
	$rows = array();
	for ( $index = 0; $index < $count; ++$index ) {
		$row = array();
		foreach ( array( 'quote', 'author', 'position', 'company', 'photo' ) as $field ) {
			$row[ $field ] = get_option( 'options_testimonials_' . $index . '_' . $field, '' );
		}
		// Keep the two unnamed entries as drafts, outside the public slider.
		$row['status']              = $index < 2 && ! trim( $row['author'] ) ? 'draft' : 'publish';
		$rows[ 'option-' . $index ] = $row;
	}
	$rows['joe-feghali'] = array(
		'quote'    => "Exceptional service, combining in a very smart way HR, ACCOUNTANT, AUDIT in an studied budget. Covering Dubai and Saudi market.\nWe are happy with our partnership!",
		'author'   => 'Mr. Joe Feghali',
		'position' => 'Managing Director',
		'company'  => 'Deep Fit DWC',
		'photo'    => 0,
		'status'   => 'publish',
	);
	foreach ( $rows as $source => $row ) {
		$existing = get_posts(
			array(
				'post_type'      => 'testimonial',
				'post_status'    => 'any',
				'meta_key'       => '_bl_testimonial_source',
				'meta_value'     => $source,
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( $existing ) {
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'   => 'testimonial',
				'post_status' => $row['status'],
				'post_title'  => $row['author'] ?: __( 'Unnamed testimonial', 'bottomline' ),
				'menu_order'  => count(
					get_posts(
						array(
							'post_type'      => 'testimonial',
							'post_status'    => 'any',
							'posts_per_page' => -1,
							'fields'         => 'ids',
						)
					)
				),
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			return;
		}
		foreach ( array( 'quote', 'position', 'company', 'photo' ) as $field ) {
			update_field( 'field_bl_testimonial_' . $field, $row[ $field ], $id );
		}
		update_post_meta( $id, '_bl_testimonial_source', $source );
	}
	update_option( 'bl_testimonial_posts_migrated', 1, false );
}
add_action( 'admin_init', 'bl_migrate_testimonial_posts', 30 );
