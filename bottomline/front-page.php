<?php get_header(); while (have_posts()): the_post(); ?>
<?php get_template_part('template-parts/hero'); ?>
<?php get_template_part('template-parts/home-section_1', null, ['source' => get_the_ID()]); ?>
<?php get_template_part('template-parts/home-section_2', null, ['source' => get_the_ID()]); ?>
<?php $source = absint(bl_field('source_about')); if ($source && get_post_status($source) === 'publish') { get_template_part('template-parts/about-home_summary', null, ['source' => $source]); } ?>
<?php get_template_part('template-parts/home-section_4', null, ['source' => get_the_ID()]); ?>
<?php $source = absint(bl_field('source_services')); if ($source && get_post_status($source) === 'publish') { get_template_part('template-parts/services-home_summary', null, ['source' => $source]); } ?>
<?php $source = absint(bl_field('source_clients')); if ($source && get_post_status($source) === 'publish') { get_template_part('template-parts/clients-home_summary', null, ['source' => $source]); } ?>
<?php $source = absint(bl_field('source_industries')); if ($source && get_post_status($source) === 'publish') { get_template_part('template-parts/industries-home_summary', null, ['source' => $source]); } ?>
<?php get_template_part('template-parts/home-section_8', null, ['source' => get_the_ID()]); ?>
<?php get_template_part('template-parts/home-section_9', null, ['source' => get_the_ID()]); ?>
<?php get_template_part('template-parts/cta'); ?>
<?php endwhile; get_footer(); ?>