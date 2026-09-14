<?php
/**
 * Template Name: About
 * Template Post Type: page
 *
 * The about page layout.
 *
 * @package Bottomline
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<?php get_template_part( 'template-parts/page-header' ); ?>
	<?php
	$data = bl_field( 'section_1', get_the_ID() );
	if ( bl_has_content( $data ) ) :
		?>
		<section class="section">
			<div class="container">
				<div class="split">
					<div class="split-content reveal">
						<h2><?php echo bl_text( $data['heading'] ?? '' ); ?></h2>
						<p><?php echo bl_text( $data['description'] ?? '' ); ?></p>
						<p><?php echo bl_text( $data['description_2'] ?? '' ); ?></p>
					</div>
					<div class="split-visual reveal" aria-hidden="true"><?php bl_icon( $data['icon'] ?? '', $data['icon_image'] ?? 0 ); ?></div>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php
	$data = bl_field( 'section_2', get_the_ID() );
	if ( bl_has_content( $data ) ) :
		?>
		<section class="section section-soft">
			<div class="container">
				<div class="section-head">
					<h2><?php echo bl_text( $data['heading'] ?? '' ); ?></h2>
				</div>
				<div class="feature-list">
					<?php
					foreach ( bl_rows( $data['feature_list_items'] ?? '' ) as $value ) :
						if ( ! bl_has_content( $value ) ) {
							continue;
						}
						?>
						<div class="feature reveal">
							<h4><?php echo bl_text( $value['label'] ?? '' ); ?></h4>
							<p><?php echo bl_text( $value['description'] ?? '' ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php
	$data = bl_field( 'section_3', get_the_ID() );
	if ( bl_has_content( $data ) ) :
		?>
		<section class="section">
			<div class="container">
				<div class="section-head center">
					<h2><?php echo bl_text( $data['heading'] ?? '' ); ?></h2>
					<p><?php echo bl_text( $data['description'] ?? '' ); ?></p>
				</div>
				<div class="targets">
					<?php
					foreach ( bl_rows( $data['targets_items'] ?? '' ) as $target ) :
						if ( ! bl_has_content( $target ) ) {
							continue;
						}
						?>
						<div class="target reveal">
							<div class="target-icon"><?php bl_icon( $target['icon'] ?? '', $target['icon_image'] ?? 0 ); ?></div>
							<h4><?php echo bl_text( $target['label'] ?? '' ); ?></h4>
							<p style="font-size: 0.9375rem; color: var(--muted);"><?php echo bl_text( $target['description'] ?? '' ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php get_template_part( 'template-parts/cta' ); ?>
	<?php
endwhile;

get_footer();
