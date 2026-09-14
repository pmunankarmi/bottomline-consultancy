<!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?><noscript><style>.reveal{opacity:1!important;transform:none!important}.hero-slide:not(:first-child){display:none}.hero-controls{display:none!important}</style></noscript></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e('Skip to content', 'bottomline'); ?></a>
<header class="site-header" id="siteHeader"><div class="container nav">
<a href="<?php echo esc_url(home_url('/')); ?>" class="brand" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>"><?php bl_image(get_theme_mod('custom_logo'), 'brand-logo', get_bloginfo('name')); ?></a>
<button class="nav-toggle" id="navToggle" aria-label="<?php esc_attr_e('Toggle menu', 'bottomline'); ?>" aria-expanded="false" aria-controls="navLinks"><span></span></button>
<?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'menu_class' => 'nav-links', 'menu_id' => 'navLinks', 'fallback_cb' => false, 'depth' => 2]); ?>
</div></header><main id="main">
