<?php
/** Featured image migration checks in the disposable local WordPress install. */
require ( getenv( 'BL_WP_ROOT' ) ?: dirname( __DIR__, 2 ) . '/.test-wordpress' ) . '/wp-load.php';
$images = get_posts( array( 'post_type' => 'attachment', 'post_mime_type' => 'image', 'posts_per_page' => 2, 'fields' => 'ids' ) );
if ( count( $images ) < 2 ) { throw new RuntimeException( 'Two test images required.' ); }
$id = wp_insert_post( array( 'post_type' => 'team', 'post_title' => 'Featured image migration test', 'post_status' => 'draft' ) );
try {
	update_post_meta( $id, 'photo', $images[0] );
	bl_migrate_team_featured_image( $id );
	if ( get_post_thumbnail_id( $id ) !== $images[0] ) { throw new RuntimeException( 'Legacy photo not migrated.' ); }
	delete_post_thumbnail( $id );
	bl_migrate_team_featured_image( $id );
	if ( has_post_thumbnail( $id ) ) { throw new RuntimeException( 'Removed image restored.' ); }
	delete_post_meta( $id, '_bl_featured_image_migrated' );
	set_post_thumbnail( $id, $images[1] );
	bl_migrate_team_featured_image( $id );
	if ( get_post_thumbnail_id( $id ) !== $images[1] ) { throw new RuntimeException( 'Existing featured image overwritten.' ); }
	echo "PASS legacy migration, removal persistence and existing featured image preservation\n";
} finally {
	wp_delete_post( $id, true );
}
