<?php
/**
 * The site header.
 *
 * Outputs the document head, site branding, navigation and main content opening.
 *
 * @package Bottomline
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php wp_head(); ?>
		<noscript>
			<style>
				.reveal {
					opacity: 1 !important;
					transform: none !important;
				}
				.hero-slide:not(:first-child),
				.hero-controls {
					display: none !important;
				}
			</style>
		</noscript>
	</head>
	<body <?php body_class(); ?>>
		<?php wp_body_open(); ?>
		<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'bottomline' ); ?></a>
		<header id="siteHeader" class="site-header">
			<div class="container nav">
				<div class="brand">
					<?php if ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>">
							<?php bloginfo( 'name' ); ?>
						</a>
					<?php endif; ?>
				</div>
				<button
					type="button"
					id="navToggle"
					class="nav-toggle"
					aria-label="<?php esc_attr_e( 'Toggle menu', 'bottomline' ); ?>"
					aria-expanded="false"
					aria-controls="navLinks"
				>
					<span></span>
				</button>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'nav-links',
						'menu_id'        => 'navLinks',
						'fallback_cb'    => false,
						'depth'          => 2,
					)
				);
				?>
			</div>
		</header>
		<main id="main" class="site-main">
