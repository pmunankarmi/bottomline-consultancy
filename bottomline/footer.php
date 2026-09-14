<?php
/**
 * The site footer.
 *
 * Closes the main content area and renders the footer navigation and contact details.
 *
 * @package Bottomline
 */

$footer = bl_field( 'footer', 'option' );
$phone  = bl_field( 'phone', 'option' );
$email  = bl_field( 'email', 'option' );
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
				<?php foreach ( bl_rows( bl_field( 'social_links', 'option' ) ) as $link ) : ?>
					<p>
						<a href="<?php echo esc_url( $link['url'] ?? '' ); ?>" rel="noopener noreferrer"><?php echo bl_text( $link['label'] ?? '' ); ?></a>
					</p>
				<?php endforeach; ?>
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
