<?php
/**
 * A team member card with a link to the full biography.
 *
 * @package Bottomline
 */
$biography = bl_field( 'biography' );
$has_biography = '' !== trim( wp_strip_all_tags( (string) $biography ) );
?>
<div class="team-card reveal">
	<?php if ( $has_biography ) : ?>
	<a href="<?php the_permalink(); ?>">
	<?php endif; ?>
		<div class="team-photo">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'medium_large' );
			} else {
				echo bl_text( bl_field( 'initials' ) );
			}
			?>
		</div>
		<div class="team-body">
			<div class="team-name"><?php the_title(); ?></div>
			<div class="team-title"><?php echo bl_text( bl_field( 'position' ) ); ?></div>
			<?php if ( $has_biography ) : ?>
				<span class="team-bio-link"><?php esc_html_e( 'Read biography', 'bottomline' ); ?> <span aria-hidden="true">&rarr;</span></span>
			<?php endif; ?>
		</div>
	<?php if ( $has_biography ) : ?>
	</a>
	<?php endif; ?>
</div>
