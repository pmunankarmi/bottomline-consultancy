<?php
/** Verify icon field types, nested image values and legacy compatibility. */
require ( getenv( 'BL_WP_ROOT' ) ?: dirname( __DIR__, 2 ) . '/.test-wordpress' ) . '/wp-load.php';
function check_icon( $ok, $message ) {
	if ( ! $ok ) {
		fwrite( STDERR, 'FAIL ' . $message . PHP_EOL );
		exit( 1 );
	}
	echo 'PASS ' . $message . PHP_EOL;
}
$schema = require get_template_directory() . '/inc/field-schema.php';
$count = 0;
$walk = function ( $fields ) use ( &$walk, &$count ) {
	foreach ( $fields as $field ) {
		if ( preg_match( '/^icon(?:_\d+)?$/', $field['name'] ) ) {
			check_icon( $field['type'] === 'select' && ! empty( $field['choices'] ), $field['key'] . ' retains its icon dropdown' );
			$image_field = current( array_filter( $fields, fn( $item ) => $item['name'] === $field['name'] . '_image' ) );
			check_icon( $image_field && $image_field['type'] === 'image', 'paired image override exists' );
			++$count;
		}
		$walk( $field['sub_fields'] ?? array() );
	}
};
foreach ( $schema as $fields ) {
	$walk( $fields );
}
check_icon( $count === 16, 'all 16 dropdowns have image overrides' );
$page = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'draft', 'post_title' => 'Icon field test' ) );
$group = current( array_filter( $schema['services'], fn( $field ) => $field['name'] === 'section_1' ) );
$image = (int) get_theme_mod( 'custom_logo' );
check_icon( wp_attachment_is_image( $image ), 'test image exists' );
try {
	foreach ( array( 0, $image, 0 ) as $override ) {
		update_field( $group['key'], array( 'grid_items' => array( array( 'card_title' => 'Test service', 'icon' => 'icon_853a788ebe', 'icon_image' => $override ) ) ), $page );
		$data = get_field( 'section_1', $page );
		$row = $data['grid_items'][0];
		ob_start();
		bl_icon( $row['icon'], $row['icon_image'] );
		$html = ob_get_clean();
		check_icon( $override ? str_contains( $html, '<img' ) && ! str_contains( $html, '<svg' ) : str_contains( $html, '<svg' ), $override ? 'image takes priority over dropdown' : 'dropdown used when override is empty or removed' );
	}

} finally {
	wp_delete_post( $page, true );
}
echo 'Icon image checks complete.' . PHP_EOL;
