<?php
/**
 * The branch map and country cards.
 *
 * @package Bottomline
 */

$branches = bl_rows( bl_field( 'branches', 'option' ) );
if ( ! $branches ) {
	return;
}
$countries = array();
foreach ( $branches as $index => $branch ) {
	$branch['index']                         = $index;
	$countries[ $branch['country'] ?? '' ][] = $branch;
}
?>
<div class="precise-map-wrap">
	<div class="precise-map-canvas reveal">
		<div class="img-map-container">
			<?php bl_image( bl_field( 'map_image', 'option' ), 'img-map' ); ?>
			<?php foreach ( $branches as $index => $branch ) : ?>
				<button class="img-map-pin" type="button" data-city="branch-<?php echo (int) $index; ?>" data-country="<?php echo esc_attr( sanitize_title( $branch['country'] ?? '' ) ); ?>" style="left:<?php echo esc_attr( max( 0, min( 100, (float) ( $branch['map_x'] ?? 0 ) ) ) ); ?>%;top:<?php echo esc_attr( max( 0, min( 100, (float) ( $branch['map_y'] ?? 0 ) ) ) ); ?>%" aria-label="<?php echo esc_attr( $branch['name'] ?? '' ); ?>"></button>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="country-cards reveal" id="countryCards">
		<?php foreach ( $countries as $country => $rows ) : ?>
			<article class="country-card" data-country="<?php echo esc_attr( sanitize_title( $country ) ); ?>">
				<div class="country-card-head">
					<div class="country-name">
						<span class="country-flag" aria-hidden="true"><?php echo bl_text( $rows[0]['flag'] ?? '' ); ?></span>
						<?php echo bl_text( $country ); ?>
					</div>
					<span class="country-branch-count"><?php echo esc_html( sprintf( _n( '%s office', '%s offices', count( $rows ), 'bottomline' ), number_format_i18n( count( $rows ) ) ) ); ?></span>
				</div>
				<div class="country-branches">
					<?php foreach ( $rows as $branch ) : ?>
						<a class="country-branch" data-city="branch-<?php echo (int) $branch['index']; ?>" href="<?php echo esc_url( $branch['map_link'] ?? '' ); ?>" target="_blank" rel="noopener noreferrer">
							<div class="branch-num"><?php echo (int) $branch['index'] + 1; ?></div>
							<div>
								<div class="country-branch-name">
									<?php echo bl_text( explode( ',', $branch['name'] ?? '' )[0] ); ?>
									<?php if ( ! empty( $branch['tag'] ) ) : ?>
										<span class="tag"><?php echo bl_text( $branch['tag'] ); ?></span>
									<?php endif; ?>
								</div>
								<div class="country-branch-addr"><?php echo bl_text( $branch['address'] ?? '' ); ?></div>
							</div>
						</a>
					<?php endforeach; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</div>
