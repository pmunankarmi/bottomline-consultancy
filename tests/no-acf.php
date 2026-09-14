<?php
$_SERVER['SERVER_NAME']='127.0.0.1';
$GLOBALS['wp_filter']['option_active_plugins'][10][]=['function'=>fn()=>[],'accepted_args'=>1];
require (getenv('BL_WP_ROOT') ?: dirname(__DIR__, 2) . '/.test-wordpress') . '/wp-load.php';
if(function_exists('get_field')){throw new RuntimeException('ACF should be inactive in this process');}
$slug=$argv[1] ?? 'about'; {
 $page_post=get_page_by_path($slug);$GLOBALS['wp_query']=new WP_Query(['page_id'=>$page_post->ID]);ob_start();include get_template_directory().'/page-templates/'.$slug.'.php';$html=ob_get_clean();if(!str_contains($html,'</html>'))throw new RuntimeException('Incomplete '.$slug);echo 'PASS no ACF: '.$slug.PHP_EOL;
}
wp_set_current_user(1);ob_start();do_action('admin_notices');$notice=ob_get_clean();if(!str_contains($notice,'requires ACF Pro'))throw new RuntimeException('Missing dependency notice');echo 'PASS dependency notice'.PHP_EOL;
