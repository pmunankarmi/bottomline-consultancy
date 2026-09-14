<?php
/* Template Name: About */
get_header(); while (have_posts()): the_post(); ?>
<?php get_template_part('template-parts/about-section_0', null, ['source' => get_the_ID()]); ?>
<?php get_template_part('template-parts/about-section_1', null, ['source' => get_the_ID()]); ?>
<?php get_template_part('template-parts/about-section_2', null, ['source' => get_the_ID()]); ?>
<?php get_template_part('template-parts/about-section_3', null, ['source' => get_the_ID()]); ?>
<?php get_template_part('template-parts/cta'); ?>
<?php endwhile; get_footer(); ?>