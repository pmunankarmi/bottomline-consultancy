<?php
/**
 * Template Name: Clients
 * Template Post Type: page
 *
 * The clients page layout.
 *
 * @package Bottomline
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<?php get_template_part( 'template-parts/page-header' ); ?>
	<?php if ( bl_rows( bl_field( 'clients', 'option' ) ) ) : ?>
		<section class="section">
			<div class="container">
				<?php get_template_part( 'template-parts/clients' ); ?>
			</div>
		</section>
	<?php endif; ?>
	<?php get_template_part( 'template-parts/cta' ); ?>
	<?php
endwhile;

get_footer();
