<?php
/**
 * The individual team member template.
 *
 * @package Bottomline
 */

get_header();

$team_pages = get_posts(
	array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_query'     => array(
			array(
				'key'     => '_wp_page_template',
				'value'   => bl_page_template_paths( 'team' ),
				'compare' => 'IN',
			),
		),
	)
);
$team_url = $team_pages ? get_permalink( $team_pages[0] ) : get_post_type_archive_link( 'team' );

while ( have_posts() ) :
	the_post();
	$other_members = get_posts(
		array(
			'post_type'      => 'team',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'post__not_in'   => array( get_the_ID() ),
			'orderby'       => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);
	?>
	<section class="section">
		<div class="container team-profile">
			<a class="team-back-link" href="<?php echo esc_url( $team_url ); ?>">
				<span aria-hidden="true">&larr;</span> <?php esc_html_e( 'Back to team', 'bottomline' ); ?>
			</a>
			<div class="team-profile-layout">
				<div class="team-profile-content">
					<h1><?php the_title(); ?></h1>
					<?php the_post_thumbnail( 'large', array( 'class' => 'profile-photo' ) ); ?>
					<p class="lead"><?php echo bl_text( bl_field( 'position' ) ); ?></p>
					<?php if ( bl_field( 'biography' ) ) : ?>
						<div class="team-biography"><?php echo wpautop( esc_html( bl_field( 'biography' ) ) ); ?></div>
					<?php endif; ?>
					<p><?php echo bl_text( bl_field( 'branch' ) ); ?></p>
					<?php foreach ( bl_rows( bl_field( 'social_links' ) ) as $social ) : ?>
						<a href="<?php echo esc_url( $social['url'] ?? '' ); ?>" rel="noopener noreferrer"><?php echo bl_text( $social['label'] ?? '' ); ?></a>
					<?php endforeach; ?>
				</div>
				<?php if ( $other_members ) : ?>
					<aside class="team-sidebar" aria-labelledby="other-team-heading">
						<h2 id="other-team-heading"><?php esc_html_e( 'Other team members', 'bottomline' ); ?></h2>
						<select class="team-member-select" aria-labelledby="other-team-heading">
							<option value=""><?php esc_html_e( 'Select a team member', 'bottomline' ); ?></option>
							<?php foreach ( $other_members as $member ) : ?>
								<option value="<?php echo esc_url( get_permalink( $member ) ); ?>"><?php echo esc_html( get_the_title( $member ) ); ?></option>
							<?php endforeach; ?>
						</select>
						<ul class="team-sidebar-list">
							<?php foreach ( $other_members as $member ) : ?>
								<li>
									<a href="<?php echo esc_url( get_permalink( $member ) ); ?>"><?php echo esc_html( get_the_title( $member ) ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</aside>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
endwhile;

get_footer();
