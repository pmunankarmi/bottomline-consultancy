<?php
/**
 * The template for pages that cannot be found.
 *
 * @package Bottomline
 */

get_header();
 ?>
<section class="section">
	<div class="container">
		<h1><?php esc_html_e( 'Page not found', 'bottomline' ); ?></h1>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return home', 'bottomline' ); ?></a>
	</div>
</section>
<?php get_footer(); ?>
