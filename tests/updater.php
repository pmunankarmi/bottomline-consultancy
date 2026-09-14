<?php
require (getenv('BL_WP_ROOT') ?: dirname(__DIR__, 2) . '/.test-wordpress') . '/wp-load.php';
wp_set_current_user(1);
function assert_update($ok,$label){if(!$ok){fwrite(STDERR,"FAIL $label\n");exit(1);}echo "PASS $label\n";}
$mode='valid';$requests=0;
$mock=function($pre,$args,$url)use(&$mode,&$requests){
 if(!str_contains($url,'pmunankarmi/bottomline-consultancy'))return $pre;
 $requests++;
 if($mode==='offline')return new WP_Error('offline','Simulated offline');
 if($url===BL_UPDATE_API){
  $assets=[];foreach(['bottomline.zip','bottomline-update.json'] as $name)$assets[]=['name'=>$name,'state'=>'uploaded','browser_download_url'=>BL_UPDATE_REPOSITORY.'/releases/download/v9.0.0/'.$name];
  if($mode==='foreign')$assets[0]['browser_download_url']='https://example.test/evil.zip';
  if($mode==='missing')array_pop($assets);
  $data=['tag_name'=>'v9.0.0','draft'=>false,'prerelease'=>$mode==='prerelease','assets'=>$assets,'body'=>'Fixed updater. <script>alert(1)</script>'];
 }else{$data=['version'=>$mode==='mismatch'?'8.0.0':'9.0.0','requires'=>'6.6','requires_php'=>'8.1','changes'=>['Fixed updater. <script>alert(1)</script>']];}
 return ['response'=>['code'=>200],'body'=>wp_json_encode($data),'headers'=>[]];
};
add_filter('pre_http_request',$mock,10,3);
delete_site_transient('bl_github_release');
$release=bl_github_release();assert_update(!is_wp_error($release)&&$release['version']==='9.0.0','stable release detected');assert_update($requests===2,'release and manifest fetched once');bl_github_release();assert_update($requests===2,'release cache prevents repeat network requests');
$header=['UpdateURI'=>BL_UPDATE_REPOSITORY];$update=apply_filters('update_themes_github.com',false,$header,get_template(),[]);assert_update($update['theme']===get_template() && str_ends_with($update['package'],'/bottomline.zip'),'native update response contains installable package');
assert_update(apply_filters('update_themes_github.com',false,['UpdateURI'=>'https://github.com/other/repo'],get_template(),[])===false,'unrelated repositories ignored');
assert_update($update['url']===bl_release_details_url(),'details link stays on the WordPress admin origin');
$cached=(object)['response'=>[get_template()=>['url'=>BL_UPDATE_REPOSITORY.'/releases/tag/v9.0.0','package'=>$release['package']]],'no_update'=>['unrelated'=>['url'=>'https://example.test']]];
$fixed=bl_local_update_details($cached);
assert_update($fixed->response[get_template()]['url']===bl_release_details_url() && $fixed->response[get_template()]['package']===$release['package'],'cached GitHub detail URLs repaired without changing ZIP');
assert_update($fixed->no_update['unrelated']['url']==='https://example.test','unrelated theme details untouched');
ob_start();bl_render_release_details($release);$details=ob_get_clean();
assert_update(str_contains($details,'Version 9.0.0') && str_contains($details,'Fixed updater.'),'version and fixes rendered locally');
assert_update(str_contains($details,'<li>') && !str_contains($details,'<a ') && !str_contains($details,'Requires WordPress'),'details use bullet points without external links or technical requirements');
assert_update(!str_contains($details,'<script>') && str_contains($details,'&lt;script&gt;'),'release text is escaped');
ob_start();bl_render_release_details(new WP_Error('offline','Offline test'));$details=ob_get_clean();
assert_update(str_contains($details,'Offline test'),'offline details show a readable error');
foreach(['foreign','missing','prerelease','mismatch','offline'] as $mode){assert_update(is_wp_error(bl_github_release(true)),$mode.' release safely rejected');}
$mode='valid';
$core_mock=function($pre,$args,$url){
 if(str_contains($url,'api.wordpress.org/themes/update-check'))return ['response'=>['code'=>200],'body'=>wp_json_encode(['themes'=>[],'no_update'=>[],'translations'=>[]]),'headers'=>[]];
 return $pre;
};
add_filter('pre_http_request',$core_mock,9,3);
delete_site_transient('bl_update_check_due');
bl_refresh_theme_updates();
$native=get_site_transient('update_themes');
assert_update(isset($native->response[get_template()]) && $native->response[get_template()]['new_version']==='9.0.0','admin visit populates standard WordPress update notice');
$before=$requests;bl_refresh_theme_updates();
assert_update($requests===$before,'repeated admin visits reuse five-minute check interval');
assert_update(!has_action('admin_post_bl_check_updates') && !function_exists('bl_theme_updates_page'),'custom update page and check action removed');
remove_filter('pre_http_request',$core_mock,9);
delete_site_transient('bl_update_check_due');
delete_site_transient('update_themes');
remove_filter('pre_http_request',$mock,10);delete_site_transient('bl_github_release');
$theme=wp_get_theme(get_template());assert_update($theme->get('UpdateURI')===BL_UPDATE_REPOSITORY,'theme has correct Update URI');
wp_set_current_user(0);assert_update(!current_user_can('update_themes'),'anonymous user cannot manage updates');
echo "Updater checks complete.\n";
