<?php
/**
 * The front page layout.
 *
 * @package Bottomline
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<?php get_template_part( 'template-parts/hero' ); ?>
	<?php
	$data = bl_field( 'section_1', get_the_ID() );
	if ( bl_has_content( $data ) ) :
		?>
		<section class="stats-section">
			<div class="dot-grid" aria-hidden="true"></div>
			<div class="container">
				<div class="stats-grid">
					<?php
					foreach ( bl_rows( $data['stats_grid_items'] ?? '' ) as $statistic ) :
						if ( ! bl_has_content( $statistic ) ) {
							continue;
						}
						if ( ( $statistic['layout'] ?? '1' ) === '1' ) :
							?>
							<div class="stat reveal">
								<div class="stat-num">
									<span data-count="<?php echo esc_attr( $statistic['data_count'] ?? '' ); ?>"><?php echo bl_text( $statistic['data_count'] ?? '' ); ?></span>
									<span class="suffix">+</span>
								</div>
								<div class="stat-label"><?php echo bl_text( $statistic['stat_label'] ?? '' ); ?></div>
								<div class="stat-sub"><?php echo bl_text( $statistic['stat_sub'] ?? '' ); ?></div>
							</div>
						<?php endif; ?>
						<?php if ( ( $statistic['layout'] ?? '1' ) === '2' ) : ?>
							<div class="stat reveal">
								<div class="stat-num stat-num-text"><?php echo bl_text( $statistic['stat_num_stat_num_text'] ?? '' ); ?></div>
								<div class="stat-label"><?php echo bl_text( $statistic['stat_label'] ?? '' ); ?></div>
								<div class="stat-sub"><?php echo bl_text( $statistic['stat_sub'] ?? '' ); ?></div>
							</div>
						<?php endif; ?>
						<?php if ( ( $statistic['layout'] ?? '1' ) === '3' ) : ?>
							<div class="stat reveal">
								<div class="stat-num"><?php echo bl_text( $statistic['stat_num'] ?? '' ); ?></div>
								<div class="stat-label"><?php echo bl_text( $statistic['stat_label'] ?? '' ); ?></div>
								<div class="stat-sub"><?php echo bl_text( $statistic['stat_sub'] ?? '' ); ?></div>
							</div>

						<?php endif; ?>

					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php
	$data = bl_field( 'section_2', get_the_ID() );
	if ( bl_has_content( $data ) ) :
		?>
		<section class="section why-choose">
			<div class="why-arcs" aria-hidden="true"><?php bl_icon( $data['icon'] ?? '', $data['icon_image'] ?? 0 ); ?></div>
			<div class="container">
				<div class="why-grid">
					<div class="why-intro reveal">
						<h2><?php echo bl_text( $data['heading'] ?? '' ); ?></h2>
						<p class="lead"><?php echo bl_text( $data['lead'] ?? '' ); ?></p>
						<p><?php echo bl_text( $data['description'] ?? '' ); ?></p>
					</div>
					<ul class="why-list reveal" role="list">
						<?php
						foreach ( bl_rows( $data['why_list_reveal_items'] ?? '' ) as $reason ) :
							if ( ! bl_has_content( $reason ) ) {
								continue;
							}
							?>
							<li class="why-item">
								<span class="why-check" aria-hidden="true"><?php bl_icon( $reason['icon'] ?? '', $reason['icon_image'] ?? 0 ); ?></span>
								<span><?php echo bl_text( $reason['label'] ?? '' ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php
	$source = absint( bl_field( 'source_about' ) );
	if ( $source && get_post_status( $source ) === 'publish' ) {
		get_template_part( 'template-parts/about-summary', null, array( 'source' => $source ) );
	}
	$source = absint( bl_field( 'source_services' ) );
	if ( $source && get_post_status( $source ) === 'publish' ) {
		get_template_part( 'template-parts/services-summary', null, array( 'source' => $source ) );
	}
	$data = bl_field( 'section_4', get_the_ID() );
	if ( (int) wp_count_posts( 'team' )->publish && bl_has_content( $data ) ) :
		?>
		<section class="section">
			<div class="container">
				<div class="section-head center">
					<h2><?php echo bl_text( $data['heading'] ?? '' ); ?></h2>
					<p><?php echo bl_text( $data['description'] ?? '' ); ?></p>
				</div>
				<?php get_template_part( 'template-parts/team-list' ); ?>
				<div style="text-align: center; margin-top: 36px;">
					<a href="<?php echo esc_url( bl_link_url( $data['destination'] ?? '' ) ); ?>" class="btn btn-ghost"><?php echo bl_text( $data['btn_btn_ghost'] ?? '' ); ?><?php bl_icon( $data['icon'] ?? '', $data['icon_image'] ?? 0 ); ?></a>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php
	$source = absint( bl_field( 'source_clients' ) );
	if ( $source && get_post_status( $source ) === 'publish' ) {
		get_template_part( 'template-parts/clients-summary', null, array( 'source' => $source ) );
	}
	$source = absint( bl_field( 'source_industries' ) );
	if ( $source && get_post_status( $source ) === 'publish' ) {
		get_template_part( 'template-parts/industries', null, array( 'source' => $source ) );
	}
	$data = bl_field( 'section_8', get_the_ID() );
	if ( bl_rows( bl_field( 'branches', 'option' ) ) && bl_has_content( $data ) ) :
		?>
		<section class="precise-map-section">
			<div class="container">
				<div class="section-head center">
					<h2><?php echo bl_text( $data['heading'] ?? '' ); ?></h2>
					<p><?php echo bl_text( $data['description'] ?? '' ); ?></p>
				</div>
				<?php get_template_part( 'template-parts/branches-map' ); ?>
			</div>
		</section>
	<?php endif; ?>
	<?php
	$data = bl_field( 'section_9', get_the_ID() );
	if ( bl_rows( bl_field( 'testimonials', 'option' ) ) && bl_has_content( $data ) ) :
		?>
		<section class="testimonial-section">
			<div class="container">
				<div class="section-head center" style="margin-bottom: 40px;">
					<h2><?php echo bl_text( $data['heading'] ?? '' ); ?></h2>
				</div>
				<?php get_template_part( 'template-parts/testimonials' ); ?>
			</div>
		</section>
	<?php endif; ?>
	<?php get_template_part( 'template-parts/cta' ); ?>
	<?php
endwhile;

get_footer();
