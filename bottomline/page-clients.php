<?php
/* Template Name: Clients */
get_header(); while (have_posts()): the_post(); ?>
<?php get_template_part('template-parts/clients-section_0', null, ['source' => get_the_ID()]); ?>
<?php get_template_part('template-parts/clients-section_1', null, ['source' => get_the_ID()]); ?>
<?php get_template_part('template-parts/cta'); ?>
<?php endwhile; get_footer(); ?>