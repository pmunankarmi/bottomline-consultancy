<?php
require (getenv('BL_WP_ROOT') ?: dirname(__DIR__, 2) . '/.test-wordpress') . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/post.php';
wp_set_current_user(1);
function check_light($ok,$label){if(!$ok){fwrite(STDERR,"FAIL $label\n");exit(1);}echo "PASS $label\n";}
check_light(!use_block_editor_for_post_type('post'),'classic editor for posts');
check_light(!use_block_editor_for_post_type('page'),'classic editor for pages');
check_light(!use_block_editor_for_post_type('team'),'classic editor for team');
check_light(!wp_use_widgets_block_editor(),'classic widgets');
$report=[];$value=bl_migrate_value(['description'=>'Retained text','image'=>['asset'=>'assets/not-bundled.png']],[],$report);
check_light($value['description']==='Retained text' && $value['image']===0,'missing images do not block text migration');
check_light((int)get_theme_mod('custom_logo')>0,'existing media logo retained');
$logo=(int)get_theme_mod('custom_logo');bl_import_content();check_light((int)get_theme_mod('custom_logo')===$logo,'repeat import preserves native logo');
$home=(int)get_option('page_on_front');$GLOBALS['wp_query']=new WP_Query(['page_id'=>$home]);
wp_enqueue_style('wp-block-library');wp_enqueue_style('global-styles');bl_trim_block_styles();
check_light(!wp_style_is('wp-block-library','enqueued') && !wp_style_is('global-styles','enqueued'),'unused block CSS omitted');
$images=[];foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(get_template_directory(),FilesystemIterator::SKIP_DOTS)) as $file){if(preg_match('/\.(png|jpe?g|webp|gif|avif)$/i',$file->getFilename()))$images[]=$file->getFilename();}
check_light($images===['screenshot.jpg'],'only preview screenshot is packaged');
$size=getimagesize(get_template_directory().'/screenshot.jpg');check_light($size[0]===1200 && $size[1]===900,'theme screenshot is 1200 by 900');
