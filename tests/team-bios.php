<?php
/** Verify supplied biographies and their rendered profiles. */
require ( getenv( 'BL_WP_ROOT' ) ?: dirname( __DIR__, 2 ) . '/.test-wordpress' ) . '/wp-load.php';
wp_set_current_user( 1 );
$bios = json_decode( file_get_contents( get_template_directory() . '/inc/team-bios.json' ), true );
$members = get_posts( array( 'post_type' => 'team', 'posts_per_page' => -1 ) );
$before = array();
foreach ( $members as $member ) {
	$before[ $member->ID ] = bl_field( 'biography', $member->ID );
}
function check_bio( $ok, $label ) {
	if ( ! $ok ) {
		fwrite( STDERR, 'FAIL ' . $label . PHP_EOL );
		exit( 1 );
	}
	echo 'PASS ' . $label . PHP_EOL;
}
bl_apply_supplied_team_bios();
$count = 0;
foreach ( $members as $member ) {
	$source = get_post_meta( $member->ID, '_bl_source_member', true );
	if ( isset( $bios[ $source ] ) ) {
		check_bio( bl_field( 'biography', $member->ID ) === $bios[ $source ]['biography'], $member->post_title . ' full biography saved' );
		check_bio( bl_field( 'position', $member->ID ) === $bios[ $source ]['position'], $member->post_title . ' role matches spreadsheet' );
		$GLOBALS['wp_query'] = new WP_Query( array( 'post_type' => 'team', 'p' => $member->ID ) );
		ob_start();
		include get_template_directory() . '/single-team.php';
		$html = ob_get_clean();
		check_bio( str_contains( html_entity_decode( strip_tags( $html ), ENT_QUOTES, 'UTF-8' ), $bios[ $source ]['biography'] ), $member->post_title . ' complete biography rendered' );
		++$count;
	} else {
		check_bio( bl_field( 'biography', $member->ID ) === $before[ $member->ID ], $member->post_title . ' unsupplied bio preserved' );
	}
}
check_bio( $count === 19, 'all 19 supplied bios matched' );
$first = $members[0]->ID;
$original = bl_field( 'biography', $first );
update_field( 'biography', 'Later administrator edit', $first );
bl_apply_supplied_team_bios();
check_bio( bl_field( 'biography', $first ) === 'Later administrator edit', 'later administrator edits preserved' );
update_field( 'biography', $original, $first );
echo 'Team biography checks complete.' . PHP_EOL;
