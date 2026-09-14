<?php
/**
 * The main post index template.
 *
 * @package Bottomline
 */

get_header();
 ?>
<section class="section">
	<div class="container">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article>
				<h2>
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h2>
				<?php the_excerpt(); ?>
			</article>
			<?php
		endwhile;
		the_posts_pagination();
		?>
	</div>
</section>
<?php get_footer(); ?>
