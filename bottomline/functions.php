<?php
/** Theme bootstrap. */
defined( 'ABSPATH' ) || exit();
foreach (
	array( 'core', 'template-paths', 'classic-editor', 'updater', 'fields', 'icons', 'logo', 'forms', 'migration', 'content-revisions' )
	as $module
) {
	require_once get_template_directory() . '/inc/' . $module . '.php';
}
