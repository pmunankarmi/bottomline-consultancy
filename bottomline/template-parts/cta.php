<?php
/**
 * The global consultation call to action.
 *
 * @package Bottomline
 */

$data = bl_field( 'cta', 'option' );
if ( ! bl_has_content( $data ) ) {
	return;
}
?>
<section class="section section-tight">
	<div class="container">
		<div class="cta-banner reveal">
			<h2><?php echo bl_text( $data['heading'] ?? '' ); ?></h2>
			<p><?php echo bl_text( $data['description'] ?? '' ); ?></p>
			<a href="<?php echo esc_url( bl_link_url( $data['destination'] ?? '' ) ); ?>" class="btn btn-primary"><?php echo bl_text( $data['btn_btn_primary'] ?? '' ); ?><?php bl_icon( $data['icon'] ?? '' ); ?></a>
		</div>
	</div>
</section>
