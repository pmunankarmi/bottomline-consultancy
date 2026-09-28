<?php
require (getenv('BL_WP_ROOT') ?: dirname(__DIR__, 2) . '/.test-wordpress') . '/wp-load.php';
wp_set_current_user(1);
bl_migrate_testimonial_posts();
$rows=bl_testimonials();
if (count($rows)!==2) throw new RuntimeException('Expected two published testimonials');
if (!in_array('Mr. Joe Feghali',array_column($rows,'author'),true)) throw new RuntimeException('Joe missing');
if (!in_array('Jad Abou Hamdan',array_column($rows,'author'),true)) throw new RuntimeException('Jad missing');
$count=count(get_posts(['post_type'=>'testimonial','post_status'=>'any','posts_per_page'=>-1]));
bl_migrate_testimonial_posts();
if ($count!==count(get_posts(['post_type'=>'testimonial','post_status'=>'any','posts_per_page'=>-1]))) throw new RuntimeException('Duplicate migration');
if (count(get_posts(['post_type'=>'testimonial','post_status'=>'draft']))!==2) throw new RuntimeException('Unnamed backup drafts missing');
echo "PASS migration, named testimonials, draft backups and repeat safety\n";
