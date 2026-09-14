<?php
/** Theme bootstrap. */
defined('ABSPATH') || exit;
foreach (['core', 'classic-editor', 'updater', 'fields', 'icons', 'logo', 'forms', 'migration'] as $module) {
    require_once get_template_directory() . '/inc/' . $module . '.php';
}
