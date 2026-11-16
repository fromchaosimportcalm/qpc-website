<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="nav-overlay" id="navOverlay"></div>
<nav class="site-nav" aria-label="<?php esc_attr_e( 'Main', 'qpc' ); ?>">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-logo">
		<img src="<?php echo esc_url( qpc_logo_url() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="nav-logo-img">
	</a>
	<button class="nav-hamburger" id="navToggle" aria-label="<?php esc_attr_e( 'Open menu', 'qpc' ); ?>" aria-expanded="false" aria-controls="navLinks">
		<span></span><span></span><span></span>
	</button>
	<?php
	wp_nav_menu( array(
		'theme_location' => 'primary',
		'container'      => false,
		'menu_class'     => 'nav-links',
		'menu_id'        => 'navLinks',
		'depth'          => 1,
		'fallback_cb'    => 'qpc_menu_fallback',
	) );
	?>
</nav>

<main id="main" class="site-main">
