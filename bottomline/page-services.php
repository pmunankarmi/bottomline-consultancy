<?php
/* Template Name: Services */
get_header(); while (have_posts()): the_post(); ?>
<?php get_template_part('template-parts/services-section_0', null, ['source' => get_the_ID()]); ?>
<?php get_template_part('template-parts/services-section_1', null, ['source' => get_the_ID()]); ?>
<?php get_template_part('template-parts/services-section_2', null, ['source' => get_the_ID()]); ?>
<?php get_template_part('template-parts/cta'); ?>
<?php endwhile; get_footer(); ?>