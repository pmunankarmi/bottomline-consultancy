<?php
/**
 * The shared testimonial carousel.
 *
 * @package Bottomline
 */

$rows = bl_testimonials();
if ( ! $rows ) {
	return;
}
?>
<div class="testimonial-slider swiper" role="region" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Client testimonials', 'bottomline' ); ?>">
	<div class="swiper-wrapper">
	<?php
	foreach ( $rows as $row ) :
		?>
		<div class="testimonial swiper-slide">
			<p class="testimonial-quote"><?php echo bl_text( $row['quote'] ); ?></p>
			<?php
			bl_image( $row['photo'] ?? 0, 'testimonial-photo' );
			if ( ! empty( $row['author'] ) ) :
				?>
				<p class="testimonial-author"><?php echo bl_text( $row['author'] ); ?></p>
			<?php endif; ?>
			<p><?php echo bl_text( implode( ' · ', array_filter( array( $row['position'] ?? '', $row['company'] ?? '' ) ) ) ); ?></p>
		</div>
	<?php endforeach; ?>
	</div>
	<?php if ( count( $rows ) > 1 ) : ?>
		<div class="testimonial-controls">
			<button type="button" class="testimonial-prev" aria-label="<?php esc_attr_e( 'Previous testimonial', 'bottomline' ); ?>">←</button>
			<div class="testimonial-pagination"></div>
			<button type="button" class="testimonial-next" aria-label="<?php esc_attr_e( 'Next testimonial', 'bottomline' ); ?>">→</button>
		</div>
	<?php endif; ?>
</div>
