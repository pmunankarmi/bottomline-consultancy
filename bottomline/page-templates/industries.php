<?php /**
 * Template Name: Industries
 * Template Post Type: page
 *
 * The industries page layout.
 *
 * @package Bottomline
 */ get_header();
while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/industries', null, array( 'source' => get_the_ID() ) );
	get_template_part( 'template-parts/cta' );
endwhile;
get_footer();
