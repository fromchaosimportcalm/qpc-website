<?php
/**
 * Old addresses (visit.html, sermons.html…) permanently redirect to the new
 * pages, so links on Facebook, Google and printed material keep working.
 */

defined( 'ABSPATH' ) || exit;

// Matched on the raw path: depending on the server, WordPress may resolve these
// to the front page rather than a 404, so don't rely on is_404().
add_action( 'template_redirect', function () {
	$path = strtolower( trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' ) );
	$old  = array(
		'index.html'             => '',
		'ministries.html'        => 'church-life',
		'history.html'           => 'our-history',
		'team.html'              => 'our-team',
		'sermons.html'           => 'sermons',
		'calendar.html'          => 'calendar',
		'visit.html'             => 'plan-your-visit',
		'bible-study.html'       => 'bible-study',
		'mens-brekkie.html'      => 'mens-brekkie',
		'playgroup.html'         => 'playgroup',
		'prayer.html'            => 'prayer',
		'ladies-craft.html'      => 'ladies-craft',
		'christian-singles.html' => 'christian-singles',
		'm3.html'                => 'm3',
		'christmas-gala.html'    => 'christmas-gala',
	);
	if ( isset( $old[ $path ] ) ) {
		wp_safe_redirect( home_url( '/' . ( $old[ $path ] ? $old[ $path ] . '/' : '' ) ), 301, 'QPC old-site redirect' );
		exit;
	}
}, 1 ); // before WordPress's own "guess the URL" redirect
