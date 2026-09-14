<div class="team-card reveal">
	<a href="<?php /**
		 * A team member card used by the directory and featured team list.
		 *
		 * @package Bottomline
		 */
		the_permalink(); ?>">
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
			<?php if ( bl_field( 'branch' ) ) : ?>
				<div class="team-branch"><?php echo bl_text( bl_field( 'branch' ) ); ?></div>
			<?php endif; ?>
		</div>
	</a>
</div>
