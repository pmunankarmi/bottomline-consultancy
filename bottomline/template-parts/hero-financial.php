<?php
/**
 * The financial statement illustration for a hero slide.
 *
 * @package Bottomline
 */

$dashboard = $args['data'] ?? array();
?>
<div class="hero-dash pl-sheet pl-compact" aria-label="<?php echo esc_attr( $dashboard['aria_label'] ?? '' ); ?>">
	<div class="pl-head">
		<div>
			<div class="pl-eyebrow"><?php echo bl_text( $dashboard['pl_eyebrow'] ?? '' ); ?></div>
			<div class="pl-firm"><?php echo bl_text( $dashboard['pl_firm'] ?? '' ); ?></div>
		</div>
		<span class="pl-unit"><?php echo bl_text( $dashboard['pl_unit'] ?? '' ); ?></span>
	</div>
	<table class="pl-mini">
		<?php
		foreach ( bl_rows( $dashboard['pl_mini_items'] ?? '' ) as $statement_row ) :
			if ( ! bl_has_content( $statement_row ) ) {
				continue;
			}
			if ( ( $statement_row['layout'] ?? '1' ) === '1' ) :
				?>
				<tr>
					<td><?php echo bl_text( $statement_row['statement_label'] ?? '' ); ?></td>
					<td>
						<span class="num" data-target="<?php echo esc_attr( $statement_row['data_target'] ?? '' ); ?>"><?php echo esc_html( bl_number( $statement_row['data_target'] ?? '' ) ); ?></span>
					</td>
					<td class="up">
						▲
						<span class="num" data-target="<?php echo esc_attr( $statement_row['data_target_2'] ?? '' ); ?>"><?php echo esc_html( bl_number( $statement_row['data_target_2'] ?? '' ) ); ?></span>
						%
					</td>
				</tr>
			<?php endif; ?>
			<?php if ( ( $statement_row['layout'] ?? '1' ) === '2' ) : ?>
				<tr class="neg">
					<td><?php echo bl_text( $statement_row['statement_label'] ?? '' ); ?></td>
					<td>
						(
						<span class="num" data-target="<?php echo esc_attr( $statement_row['data_target'] ?? '' ); ?>"><?php echo esc_html( bl_number( $statement_row['data_target'] ?? '' ) ); ?></span>
						)
					</td>
					<td class="neutral">
						+
						<span class="num" data-target="<?php echo esc_attr( $statement_row['data_target_2'] ?? '' ); ?>"><?php echo esc_html( bl_number( $statement_row['data_target_2'] ?? '' ) ); ?></span>
						%
					</td>
				</tr>
			<?php endif; ?>
			<?php if ( ( $statement_row['layout'] ?? '1' ) === '3' ) : ?>
				<tr class="sub gp">
					<td><?php echo bl_text( $statement_row['statement_label'] ?? '' ); ?></td>
					<td>
						<span class="num" data-target="<?php echo esc_attr( $statement_row['data_target'] ?? '' ); ?>"><?php echo esc_html( bl_number( $statement_row['data_target'] ?? '' ) ); ?></span>
					</td>
					<td class="up">
						▲
						<span class="num" data-target="<?php echo esc_attr( $statement_row['data_target_2'] ?? '' ); ?>"><?php echo esc_html( bl_number( $statement_row['data_target_2'] ?? '' ) ); ?></span>
						%
					</td>
				</tr>
			<?php endif; ?>
			<?php if ( ( $statement_row['layout'] ?? '1' ) === '4' ) : ?>
				<tr class="sub">
					<td><?php echo bl_text( $statement_row['statement_label'] ?? '' ); ?></td>
					<td>
						<span class="num" data-target="<?php echo esc_attr( $statement_row['data_target'] ?? '' ); ?>"><?php echo esc_html( bl_number( $statement_row['data_target'] ?? '' ) ); ?></span>
					</td>
					<td class="up">
						▲
						<span class="num" data-target="<?php echo esc_attr( $statement_row['data_target_2'] ?? '' ); ?>"><?php echo esc_html( bl_number( $statement_row['data_target_2'] ?? '' ) ); ?></span>
						%
					</td>
				</tr>
			<?php endif; ?>
			<?php if ( ( $statement_row['layout'] ?? '1' ) === '5' ) : ?>
				<tr class="total">
					<td><?php echo bl_text( $statement_row['statement_label'] ?? '' ); ?></td>
					<td>
						<span class="num" data-target="<?php echo esc_attr( $statement_row['data_target'] ?? '' ); ?>"><?php echo esc_html( bl_number( $statement_row['data_target'] ?? '' ) ); ?></span>
					</td>
					<td class="up">
						▲
						<span class="num" data-target="<?php echo esc_attr( $statement_row['data_target_2'] ?? '' ); ?>"><?php echo esc_html( bl_number( $statement_row['data_target_2'] ?? '' ) ); ?></span>
						%
					</td>
				</tr>

			<?php endif; ?>

		<?php endforeach; ?>
	</table>
	<div class="pl-strip">
		<div class="pl-strip-item">
			<span><?php echo bl_text( $dashboard['label'] ?? '' ); ?></span>
			<strong>
				<span class="num" data-target="<?php echo esc_attr( $dashboard['data_target'] ?? '' ); ?>"><?php echo esc_html( bl_number( $dashboard['data_target'] ?? '' ) ); ?></span>
				%
			</strong>
		</div>
		<div class="pl-strip-sep"></div>
		<div class="pl-strip-item">
			<span><?php echo bl_text( $dashboard['label_2'] ?? '' ); ?></span>
			<strong>
				<span class="num" data-target="<?php echo esc_attr( $dashboard['data_target_2'] ?? '' ); ?>"><?php echo esc_html( bl_number( $dashboard['data_target_2'] ?? '' ) ); ?></span>
				%
			</strong>
		</div>
		<div class="pl-strip-sep"></div>
		<div class="pl-strip-item pl-strip-spark">
			<?php bl_icon( $dashboard['icon'] ?? '' ); ?>
			<span><?php echo bl_text( $dashboard['label_3'] ?? '' ); ?></span>
		</div>
	</div>
</div>
