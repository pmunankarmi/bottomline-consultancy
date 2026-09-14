<?php
/**
 * Template Name: Team
 * Template Post Type: page
 *
 * The team page layout.
 *
 * @package Bottomline
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<?php get_template_part( 'template-parts/page-header' ); ?>
	<?php if ( wp_count_posts( 'team' )->publish > 0 ) : ?>
		<section class="section">
			<div class="container">
				<?php get_template_part( 'template-parts/team-list' ); ?>
			</div>
		</section>
	<?php endif; ?>
	<?php get_template_part( 'template-parts/cta' ); ?>
	<?php
endwhile;

get_footer();
