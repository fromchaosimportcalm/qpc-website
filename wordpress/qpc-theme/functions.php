<?php
/**
 * QPC theme setup.
 *
 * Content lives in pages (block editor). Church contact details used in the
 * header/footer are edited under Appearance → Customize → Church Details.
 */

defined( 'ABSPATH' ) || exit;

define( 'QPC_THEME_VERSION', '1.0.3' );

require_once __DIR__ . '/inc/customizer.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 96, 'flex-width' => true ) );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	remove_theme_support( 'core-block-patterns' );

	register_nav_menus( array(
		'primary' => __( 'Main menu (top of page)', 'qpc' ),
		'footer'  => __( 'Footer "Pages" list', 'qpc' ),
	) );

	// Editor sees the same styles as the site.
	add_editor_style( array( qpc_fonts_url(), 'assets/css/base.css', 'assets/css/editor.css' ) );
	foreach ( qpc_section_stylesheets() as $file ) {
		add_editor_style( 'assets/css/sections/' . $file );
	}
	add_editor_style( 'assets/css/fixes.css' );
} );

function qpc_fonts_url() {
	return 'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Crimson+Pro:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Mono:wght@300;400&display=swap';
}

/** All files in assets/css/sections/, alphabetical. */
function qpc_section_stylesheets() {
	$files = glob( get_theme_file_path( 'assets/css/sections/*.css' ) ) ?: array();
	return array_map( 'basename', $files );
}

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'qpc-fonts', qpc_fonts_url(), array(), null );
	wp_enqueue_style( 'qpc-base', get_theme_file_uri( 'assets/css/base.css' ), array( 'qpc-fonts' ), QPC_THEME_VERSION );

	// Section styles are small and cached after the first page, so load them all
	// rather than guessing which sections a page uses.
	foreach ( qpc_section_stylesheets() as $file ) {
		wp_enqueue_style( 'qpc-section-' . basename( $file, '.css' ), get_theme_file_uri( 'assets/css/sections/' . $file ), array( 'qpc-base' ), QPC_THEME_VERSION );
	}

	wp_enqueue_style( 'qpc-fixes', get_theme_file_uri( 'assets/css/fixes.css' ), array( 'qpc-base' ), QPC_THEME_VERSION );

	wp_enqueue_script( 'qpc-nav', get_theme_file_uri( 'assets/js/nav.js' ), array(), QPC_THEME_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
} );

/** Pattern category so the church's sections are easy to find in the inserter. */
add_action( 'init', function () {
	register_block_pattern_category( 'qpc', array( 'label' => __( 'QPC Sections', 'qpc' ) ) );
	register_block_pattern_category( 'qpc-pages', array( 'label' => __( 'QPC Full Pages', 'qpc' ) ) );

	// Button styles matching the site's two button types.
	register_block_style( 'core/button', array( 'name' => 'primary', 'label' => __( 'Green', 'qpc' ), 'is_default' => true ) );
	register_block_style( 'core/button', array( 'name' => 'secondary', 'label' => __( 'Outline', 'qpc' ) ) );
	register_block_style( 'core/button', array( 'name' => 'gold', 'label' => __( 'Gold', 'qpc' ) ) );
	register_block_style( 'core/button', array( 'name' => 'light-outline', 'label' => __( 'Outline (on dark)', 'qpc' ) ) );
	register_block_style( 'core/button', array( 'name' => 'spotify', 'label' => __( 'Spotify', 'qpc' ) ) );
} );

/** Menu items: mark the current page, and let a menu item use the "nav-cta" class for the green button. */
add_filter( 'nav_menu_link_attributes', function ( $atts, $item, $args ) {
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
		$classes = array();
		if ( in_array( 'nav-cta', (array) $item->classes, true ) ) {
			$classes[] = 'nav-cta';
		}
		if ( $item->current ) {
			$classes[] = 'active';
			$atts['aria-current'] = 'page';
		}
		if ( $classes ) {
			$atts['class'] = trim( ( $atts['class'] ?? '' ) . ' ' . implode( ' ', $classes ) );
		}
	}
	return $atts;
}, 10, 3 );

/** Fallback when no menu has been assigned yet: list the top-level pages. */
function qpc_menu_fallback( $args ) {
	$pages = get_pages( array( 'parent' => 0, 'sort_column' => 'menu_order,post_title' ) );
	echo '<ul class="' . esc_attr( $args['menu_class'] ) . '" id="' . esc_attr( $args['menu_id'] ) . '">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '"' . ( is_front_page() ? ' class="active"' : '' ) . '>' . esc_html__( 'Home', 'qpc' ) . '</a></li>';
	foreach ( $pages as $p ) {
		if ( (int) get_option( 'page_on_front' ) === $p->ID ) {
			continue;
		}
		$active = is_page( $p->ID ) ? ' class="active"' : '';
		echo '<li><a href="' . esc_url( get_permalink( $p ) ) . '"' . $active . '>' . esc_html( $p->post_title ) . '</a></li>';
	}
	echo '</ul>';
}

/** The logo: Site Identity logo if set, otherwise the bundled one. */
function qpc_logo_url() {
	$id = get_theme_mod( 'custom_logo' );
	if ( $id ) {
		$src = wp_get_attachment_image_url( $id, 'full' );
		if ( $src ) {
			return $src;
		}
	}
	return get_theme_file_uri( 'assets/images/qpc_mini_transparent.png' );
}

/* ---------- Page styles ----------
 * Each page from the old site had its own look. Those looks are "page styles":
 * pick one in the editor sidebar under Template ("Page style: Christmas Gala"),
 * e.g. to make next year's event page look like this year's. The page gets
 * body class "qpc-page-<style>", which its CSS (assets/css/sections/<style>.css)
 * is scoped to. A page with no style chosen uses one matching its own slug, if any.
 */
function qpc_page_style_data() {
	static $data = null;
	if ( null === $data ) {
		$data = (array) include __DIR__ . '/inc/page-style-flags.php';
	}
	return $data;
}

add_filter( 'theme_page_templates', function ( $templates ) {
	foreach ( qpc_page_style_data()['styles'] ?? array() as $style => $title ) {
		/* translators: %s: name of an existing page whose look is reused */
		$templates[ 'qpc-style-' . $style ] = sprintf( __( 'Page style: %s', 'qpc' ), $title );
	}
	return $templates;
} );

// The styles are CSS only; they all render with page.php.
add_filter( 'template_include', function ( $template ) {
	if ( is_page() && 0 === strpos( (string) get_page_template_slug(), 'qpc-style-' ) ) {
		return get_theme_file_path( 'page.php' );
	}
	return $template;
} );

/** The page style for the current request ('' if none). */
function qpc_current_page_style( $post_id = 0 ) {
	$post_id = $post_id ?: ( is_page() || is_front_page() ? get_queried_object_id() : 0 );
	if ( ! $post_id ) {
		return '';
	}
	$styles   = qpc_page_style_data()['styles'] ?? array();
	$template = (string) get_page_template_slug( $post_id );
	if ( 0 === strpos( $template, 'qpc-style-' ) && isset( $styles[ substr( $template, 10 ) ] ) ) {
		return substr( $template, 10 );
	}
	$slug = (int) get_option( 'page_on_front' ) === $post_id ? 'home' : get_post_field( 'post_name', $post_id );
	return isset( $styles[ $slug ] ) ? $slug : '';
}

/** CSS classes for a page style: qpc-page-<style> plus has-own-header / has-own-headings. */
function qpc_page_style_classes( $style ) {
	if ( ! $style ) {
		return array();
	}
	$classes = array( 'qpc-page-' . sanitize_html_class( $style ) );
	foreach ( qpc_page_style_data() as $flag => $styles ) {
		if ( 'styles' !== $flag && in_array( $style, (array) $styles, true ) ) {
			$classes[] = $flag;
		}
	}
	return $classes;
}

add_filter( 'body_class', function ( $classes ) {
	return array_merge( $classes, qpc_page_style_classes( qpc_current_page_style() ) );
} );

add_action( 'enqueue_block_editor_assets', function () {
	wp_enqueue_script( 'qpc-editor-page-style', get_theme_file_uri( 'assets/js/editor-page-style.js' ), array( 'wp-data', 'wp-dom-ready', 'wp-editor' ), QPC_THEME_VERSION, true );
	$data  = qpc_page_style_data();
	$flags = $data;
	unset( $flags['styles'] );
	wp_localize_script( 'qpc-editor-page-style', 'qpcPageStyles', array(
		'styles'      => $data['styles'] ?? array(),
		'flags'       => $flags,
		'frontPageId' => (int) get_option( 'page_on_front' ),
	) );
} );

/** Pattern helper: URL of an image bundled with the theme. */
function qpc_img( $file ) {
	return esc_url( get_theme_file_uri( 'assets/images/' . $file ) );
}

/* Use the visitor's own emoji font, like the old site, instead of WordPress's emoji images. */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
