<?php
/** Verify custom template discovery and migration without changing content. */
require ( getenv( 'BL_WP_ROOT' ) ?: dirname( __DIR__, 2 ) . '/.test-wordpress' ) . '/wp-load.php';
function assert_template( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
	echo 'PASS ' . $message . PHP_EOL;
}
wp_set_current_user( 1 );
$registered = wp_get_theme()->get_page_templates();
$version = get_option( 'bl_template_paths_version' );
foreach ( array( 'about', 'services', 'clients', 'team', 'contact', 'industries' ) as $name ) {
	$page = get_page_by_path( $name );
	$saved = get_page_template_slug( $page );
	$content = get_post_meta( $page->ID );
	assert_template( isset( $registered[ 'page-templates/' . $name . '.php' ] ), $name . ' registered in WordPress' );
	try {
		update_post_meta( $page->ID, '_wp_page_template', 'page-' . $name . '.php' );
		$GLOBALS['post'] = $page;
		$GLOBALS['wp_query'] = new WP_Query( array( 'page_id' => $page->ID ) );
		assert_template( str_ends_with( bl_resolve_legacy_page_template( '' ), '/page-templates/' . $name . '.php' ), $name . ' legacy assignment resolves' );
		delete_option( 'bl_template_paths_version' );
		bl_migrate_page_template_paths();
		assert_template( get_page_template_slug( $page ) === 'page-templates/' . $name . '.php', $name . ' assignment migrated' );
		$after = get_post_meta( $page->ID );
		unset( $after['_wp_page_template'], $content['_wp_page_template'] );
		assert_template( $after === $content, $name . ' content preserved' );
	} finally {
		update_post_meta( $page->ID, '_wp_page_template', $saved );
	}
}
update_option( 'bl_template_paths_version', $version );
echo 'Completed 24 template checks.' . PHP_EOL;
