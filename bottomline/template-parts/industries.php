<?php
/**
 * The shared industries section.
 *
 * @package Bottomline
 */

$data = bl_field( 'home_summary', $args['source'] ?? get_the_ID() );
if ( ! bl_has_content( $data ) ) {
	return;
}
?>
<section class="industries">
	<div class="container">
		<div class="section-head center">
			<?php if ( is_front_page() ) : ?>
				<h2><?php echo bl_text( $data['heading'] ?? '' ); ?></h2>
			<?php else : ?>
				<h1><?php the_title(); ?></h1>
			<?php endif; ?>
			<p><?php echo bl_text( $data['description'] ?? '' ); ?></p>
		</div>
		<div class="industries-grid">
			<?php
			foreach ( bl_rows( $data['industries_grid_items'] ?? '' ) as $industry ) :
				if ( ! bl_has_content( $industry ) ) {
					continue;
				}
				?>
				<div class="industry-card">
					<div class="industry-icon"><?php bl_icon( $industry['icon'] ?? '', $industry['icon_image'] ?? 0 ); ?></div>
					<span><?php echo bl_text( $industry['label'] ?? '' ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
