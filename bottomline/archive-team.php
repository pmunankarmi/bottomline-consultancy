<?php
/**
 * The team archive template.
 *
 * @package Bottomline
 */

get_header();
 ?>
<section class="section">
	<div class="container">
		<h1>
			<?php post_type_archive_title(); ?>
		</h1>
		<div class="team-grid">
			<?php
			while ( have_posts() ) :
							the_post();
							get_template_part( 'template-parts/team-card' );
			endwhile;
			?>
		</div>
		<?php the_posts_pagination(); ?>
	</div>
</section>
<?php get_footer(); ?>
