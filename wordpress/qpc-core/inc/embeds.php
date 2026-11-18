<?php
/**
 * Third-party form embeds.
 *
 * Fillout forms (e.g. Christmas Gala tickets) are placed in a Custom HTML
 * block as an empty <div data-fillout-id="...">. Fillout's loader script
 * turns that div into the form, so load it on any page that contains one.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', function () {
	if ( ! is_singular() ) {
		return;
	}
	$post = get_queried_object();
	if ( ! $post instanceof WP_Post || false === strpos( $post->post_content, 'data-fillout-id' ) ) {
		return;
	}
	wp_enqueue_script( 'fillout-embed', 'https://server.fillout.com/embed/v1/', array(), null, array( 'in_footer' => true ) );
} );
