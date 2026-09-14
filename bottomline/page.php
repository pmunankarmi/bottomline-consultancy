<?php
/**
 * The default page template.
 *
 * @package Bottomline
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<section class="section">
		<div class="container">
			<h1><?php the_title(); ?></h1>
			<?php the_content(); ?>
		</div>
	</section>
	<?php
endwhile;

get_footer();
