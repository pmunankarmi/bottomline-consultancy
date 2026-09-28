<?php
/**
 * The site footer.
 *
 * Closes the main content area and renders the footer navigation and contact details.
 *
 * @package Bottomline
 */

$footer       = bl_field( 'footer', 'option' );
$phone        = bl_field( 'phone', 'option' );
$email        = bl_field( 'email', 'option' );
$social_links = array_filter(
	bl_rows( bl_field( 'social_links', 'option' ) ),
	function ( $link ) {
		return ! empty( $link['label'] ) && ! empty( $link['url'] );
	}
);
?>
</main>
<footer class="site-footer">
	<div class="container">
		<div class="footer-grid">
			<div class="footer-about">
				<div class="footer-brand"><?php bl_image( bl_field( 'footer_logo', 'option' ), 'brand-logo-footer' ); ?></div>
				<p><?php echo bl_text( $footer['description'] ?? '' ); ?></p>
			</div>
			<div>
				<div class="footer-heading"><?php echo bl_text( $footer['footer_heading'] ?? '' ); ?></div>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'footer-list',
						'fallback_cb'    => false,
					)
				);
				?>
			</div>
			<div>
				<div class="footer-heading"><?php echo bl_text( $footer['footer_heading_2'] ?? '' ); ?></div>
				<ul class="footer-list">
					<?php foreach ( bl_rows( bl_field( 'branches', 'option' ) ) as $branch ) : ?>
						<li>
							<a href="<?php echo esc_url( $branch['map_link'] ?? '' ); ?>" target="_blank" rel="noopener noreferrer"><?php echo bl_text( $branch['name'] ?? '' ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="footer-contact">
				<div class="footer-heading"><?php echo bl_text( bl_field( 'footer_contact_heading', 'option' ) ); ?></div>
				<?php if ( $phone ) : ?>
					<p>
						<a href="tel:<?php echo esc_attr( preg_replace( '/[^+0-9]/', '', $phone ) ); ?>"><?php echo bl_text( $phone ); ?></a>
					</p>
				<?php endif; ?>
				<?php if ( $email ) : ?>
					<p>
						<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo bl_text( $email ); ?></a>
					</p>
				<?php endif; ?>
				<?php if ( $social_links ) : ?>
				<div class="footer-social">
					<?php foreach ( $social_links as $link ) : ?>
						<?php
						$label   = trim( $link['label'] ?? '' );
						$url     = $link['url'] ?? '';
						$network = strtolower( $label );
						if ( ! $label || ! $url ) {
							continue;
						}
						?>
						<a href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $label ); ?>" target="_blank" rel="noopener noreferrer">
							<?php if ( 'instagram' === $network ) : ?>
								<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true" focusable="false">
									<rect x="3" y="3" width="18" height="18" rx="5" />
									<circle cx="12" cy="12" r="4" />
									<circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none" />
								</svg>
							<?php elseif ( 'linkedin' === $network ) : ?>
								<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
									<path d="M20.45 2H3.55C2.69 2 2 2.68 2 3.52v16.96c0 .84.69 1.52 1.55 1.52h16.9c.86 0 1.55-.68 1.55-1.52V3.52c0-.84-.69-1.52-1.55-1.52ZM7.93 18.75H4.98V9.2h2.95v9.55ZM6.45 7.9a1.71 1.71 0 1 1 0-3.42 1.71 1.71 0 0 1 0 3.42Zm12.3 10.85H15.8v-4.64c0-1.11-.02-2.54-1.55-2.54-1.55 0-1.79 1.21-1.79 2.46v4.72H9.51V9.2h2.83v1.31h.04c.39-.75 1.36-1.55 2.79-1.55 2.98 0 3.58 1.96 3.58 4.51v5.28Z" />
								</svg>
							<?php else : ?>
								<?php echo bl_text( $label ); ?>
							<?php endif; ?>
						</a>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<div class="footer-bottom">
			<span>
				©
				<span id="year"><?php echo esc_html( wp_date( 'Y' ) ); ?></span>
				<?php echo bl_text( $footer['label'] ?? '' ); ?>
			</span>
			<span><?php echo bl_text( $footer['label_2'] ?? '' ); ?></span>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
