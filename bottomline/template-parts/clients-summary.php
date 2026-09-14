<?php
/**
 * The Clients page introduction and shared client list.
 *
 * @package Bottomline
 */

if ( ! bl_rows( bl_field( 'clients', 'option' ) ) ) {
	return;
}
$data = bl_field( 'home_summary', $args['source'] ?? get_the_ID() );
if ( ! bl_has_content( $data ) ) {
	return;
}
?>
<section class="clients-section">
	<div class="container">
		<div class="section-head center">
			<h2><?php echo bl_text( $data['heading'] ?? '' ); ?></h2>
			<p><?php echo bl_text( $data['description'] ?? '' ); ?></p>
		</div>
		<?php get_template_part( 'template-parts/clients' ); ?>
		<div class="clients-cta">
			<a href="<?php echo esc_url( get_permalink( $args['source'] ) ); ?>" class="btn btn-ghost"><?php echo bl_text( $data['btn_btn_ghost'] ?? '' ); ?><?php bl_icon( $data['icon'] ?? '', $data['icon_image'] ?? 0 ); ?></a>
		</div>
	</div>
</section>
