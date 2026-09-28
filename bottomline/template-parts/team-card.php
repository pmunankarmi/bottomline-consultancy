<?php
/**
 * A team member card with a link to the full biography.
 *
 * @package Bottomline
 */
$biography = bl_field( 'biography' );
?>
<div class="team-card reveal">
	<a href="<?php the_permalink(); ?>">
		<div class="team-photo">
			<?php
			if ( bl_field( 'photo' ) ) {
				bl_image( bl_field( 'photo' ) );
			} else {
				echo bl_text( bl_field( 'initials' ) );
			}
			?>
		</div>
		<div class="team-body">
			<div class="team-name"><?php the_title(); ?></div>
			<div class="team-title"><?php echo bl_text( bl_field( 'position' ) ); ?></div>
			<?php if ( $biography ) : ?>
				<span class="team-bio-link"><?php esc_html_e( 'Read biography', 'bottomline' ); ?> <span aria-hidden="true">&rarr;</span></span>
			<?php endif; ?>
		</div>
	</a>
</div>
