<?php
require (getenv('BL_WP_ROOT') ?: dirname(__DIR__, 2) . '/.test-wordpress') . '/wp-load.php';
wp_set_current_user(1);
$before = bl_field('testimonials','option');
bl_apply_september_content_updates();
$members=get_posts(array('post_type'=>'team','posts_per_page'=>-1,'orderby'=>array('menu_order'=>'ASC','title'=>'ASC')));
$names=wp_list_pluck($members,'post_title');
function verify_content($ok,$label){if(!$ok){fwrite(STDERR,'FAIL '.$label.PHP_EOL);exit(1);}echo 'PASS '.$label.PHP_EOL;}
verify_content(in_array('Elie Moutran',$names,true)&&in_array('Al-Khedr Makke',$names,true),'corrected team names');
verify_content(array_search('Hisham Youssef',$names,true)+1===array_search('Mohammad Rushdi',$names,true),'Hisham immediately before Rushdi');
$after=bl_field('testimonials','option');
verify_content(array_slice($after,0,count($before))===$before,'existing testimonials preserved');
verify_content(count(array_filter($after,fn($r)=>($r['author']??'')==='Jad Abou Hamdan'))===1,'new attributed testimonial added once');
bl_apply_september_content_updates();
verify_content(bl_field('testimonials','option')===$after,'content update is idempotent');
$contact=get_page_by_path('contact');verify_content(bl_field('section_1',$contact->ID)['label']==='WhatsApp/Phone','contact label saved in admin');
$seed=require get_template_directory().'/inc/migration-data.php';
verify_content(count($seed['team'])===32,'seed retains all 32 members');
$seed_names=array_column($seed['team'],'name');verify_content(array_search('Hisham Youssef',$seed_names,true)+1===array_search('Mohammad Rushdi',$seed_names,true),'new installations have corrected order');
