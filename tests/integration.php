<?php
/** Run only against the isolated test install: php tests/integration.php */
require (getenv('BL_WP_ROOT') ?: dirname(__DIR__, 2) . '/.test-wordpress') . '/wp-load.php';
wp_set_current_user(1);
$checks=0;
function check($condition,$message) { global $checks; $checks++; if (!$condition) { throw new RuntimeException('FAIL: '.$message); } echo 'PASS '.$message.PHP_EOL; }
check(bl_acf_ready(),'ACF Pro active');
check((int)wp_count_posts('team')->publish===32,'32 migrated team posts');
check(count(bl_rows(bl_field('clients','option')))===82,'82 canonical client records');
check(count(bl_rows(bl_field('branches','option')))===4,'4 canonical branches');
check(count(bl_rows(bl_field('testimonials','option')))===2,'2 shared testimonials');
$home=(int)get_option('page_on_front');
check(count(bl_field('hero_slides',$home))===2,'2 editable hero slides');
check(count(bl_field('section_1',$home)['stats_grid_items'])===4,'4 statistic rows');
$schema=require get_template_directory().'/inc/field-schema.php'; $keys=[];
$walk=function($fields) use (&$walk,&$keys) { foreach($fields as $field) { check(!isset($keys[$field['key']]),'unique ACF key '.$field['name']);$keys[$field['key']]=1;check($field['type']!=='wysiwyg','plain structured field '.$field['name']);if(isset($field['sub_fields']))$walk($field['sub_fields']); } }; $walk($schema['options']);
foreach(['about','services','clients','industries'] as $scope) { $page=bl_field('source_'.$scope,$home);check($page && get_post_type($page)==='page','source selector '.$scope);check(bl_has_content(bl_field('home_summary',$page)),'source summary '.$scope); }
$logo=(int)get_theme_mod('custom_logo');$alt=(int)bl_field('footer_logo','option');
check($logo>0 && (int)bl_field('site_logo','option')===$logo,'initial logo agreement');
update_field('site_logo',$alt,'option');check((int)get_theme_mod('custom_logo')===$alt,'ACF logo updates native logo');
update_field('site_logo',0,'option');check(!(int)get_theme_mod('custom_logo'),'ACF logo removal updates native');
set_theme_mod('custom_logo',$logo);check((int)bl_field('site_logo','option')===$logo,'native logo updates ACF');
remove_theme_mod('custom_logo');check(!(int)bl_field('site_logo','option'),'native logo removal updates ACF');
set_theme_mod('custom_logo',$logo);
$invalid=bl_form_validate(['firstName'=>[], 'email'=>'not email','message'=>'']);check(count($invalid['errors'])>=4,'malformed and required values rejected');
$valid=['firstName'=>'Test','lastName'=>'User','email'=>'test@example.test','company'=>'Example','phone'=>'','interest'=>'','message'=>"Line one\nLine two"];
check(!bl_form_validate($valid)['errors'],'valid form accepted');
check(bl_form_validate(array_merge($valid,['interest'=>'Unknown']))['errors']!==[],'forged service rejected');
check(bl_form_validate(array_merge($valid,['message'=>str_repeat('x',10001)]))['errors']!==[],'oversized message rejected');
foreach(['=HYPERLINK("bad")','+cmd','-1+2','@SUM(A1)'," \t=1", "\xEF\xBB\xBF=1", "\n=2"] as $value) {check(str_starts_with(bl_csv_safe($value),"'"),'CSV formula neutralized');}
$stream=fopen('php://temp','w+');$round=['Comma, value',"Two\nlines",'A "quote"'];fputcsv($stream,$round,',','"','');rewind($stream);check(fgetcsv($stream,null,',','"','')===$round,'CSV quoting round trip');fclose($stream);
$about=bl_field('source_about',$home);$before=bl_field('home_summary',$about);$edited=$before;$edited['heading']='Administrator edited heading';update_field('home_summary',$edited,$about);
ob_start();get_template_part('template-parts/about-summary',null,['source'=>$about]);$rendered=ob_get_clean();check(str_contains($rendered,'Administrator edited heading'),'source-page change updates PHP section');
$count_before=count(get_posts(['post_type'=>'attachment','posts_per_page'=>-1,'fields'=>'ids']));
$phone=bl_field('phone','option');update_field('phone','','option');
$clients=bl_field('clients','option');$edited_clients=array_reverse($clients);update_field('clients',$edited_clients,'option');
$result=bl_import_content();check(!is_wp_error($result),'repeat import succeeds');check(bl_field('home_summary',$about)['heading']==='Administrator edited heading','repeat import preserves administrator edits');check(bl_field('phone','option')==='','repeat import preserves deliberate clearing');check(bl_field('clients','option')[0]['name']===$edited_clients[0]['name'],'repeat import preserves client order');check(count(get_posts(['post_type'=>'attachment','posts_per_page'=>-1,'fields'=>'ids']))===$count_before,'repeat import does not duplicate media');check((int)wp_count_posts('team')->publish===32,'repeat import does not duplicate team');
update_field('home_summary',$before,$about);update_field('phone',$phone,'option');update_field('clients',$clients,'option');
check(bl_text('<script>alert(1)</script>')==='&lt;script&gt;alert(1)&lt;/script&gt;','text escapes markup');check(str_contains(bl_text("A\nB"),'<br'),'textarea preserves line breaks');
wp_set_current_user(0);check(is_wp_error(bl_import_content()),'anonymous import blocked');
$user=get_user_by('login','bl_test_subscriber');if(!$user){$id=wp_insert_user(['user_login'=>'bl_test_subscriber','user_pass'=>wp_generate_password(),'role'=>'subscriber']);}else{$id=$user->ID;}wp_set_current_user($id);check(!current_user_can('manage_options'),'subscriber cannot access submission administration');check(is_wp_error(bl_import_content()),'subscriber import blocked');
wp_set_current_user(1);check(current_user_can('manage_options'),'administrator can manage submissions');
echo "Completed $checks checks.\n";
