<?php
/* Template Name: Contact */
get_header(); while (have_posts()): the_post(); ?>
<?php get_template_part('template-parts/contact-section_0', null, ['source' => get_the_ID()]); ?>
<?php get_template_part('template-parts/contact-section_1', null, ['source' => get_the_ID()]); ?>
<?php endwhile; get_footer(); ?>