<?php
/**
 * Template Name: Services
 * Template Post Type: page
 *
 * The services page layout.
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
				<div class="grid" style="gap: 24px;">
					<?php
					foreach ( bl_rows( $data['grid_items'] ?? '' ) as $service ) :
						if ( ! bl_has_content( $service ) ) {
							continue;
						}
						?>
						<article class="service-card reveal">
							<div class="card-icon"><?php bl_icon( $service['icon'] ?? '' ); ?></div>
							<div class="service-card-body">
								<h3><?php echo bl_text( $service['card_title'] ?? '' ); ?></h3>
								<p><?php echo bl_text( $service['description'] ?? '' ); ?></p>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php
	$data = bl_field( 'section_2', get_the_ID() );
	if ( bl_has_content( $data ) ) :
		?>
		<section class="section section-soft process-section">
			<div class="container">
				<div class="section-head center">
					<h2><?php echo bl_text( $data['heading'] ?? '' ); ?></h2>
					<p><?php echo bl_text( $data['description'] ?? '' ); ?></p>
				</div>
				<div class="process-flow">
					<?php
					foreach ( bl_rows( $data['process_flow_items'] ?? '' ) as $step ) :
						if ( ! bl_has_content( $step ) ) {
							continue;
						}
						?>
						<div class="process-step reveal">
							<div class="process-num"><?php echo bl_text( $step['process_num'] ?? '' ); ?></div>
							<h3><?php echo bl_text( $step['card_title'] ?? '' ); ?></h3>
							<p><?php echo bl_text( $step['description'] ?? '' ); ?></p>
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
