<?php
/**
 * Old addresses (visit.html, sermons.html…) permanently redirect to the new
 * pages, so links on Facebook, Google and printed material keep working.
 */

defined( 'ABSPATH' ) || exit;

// Matched on the raw path: depending on the server, WordPress may resolve these
// to the front page rather than a 404, so don't rely on is_404().
function qpc_old_urls() {
	return array(
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
}

add_action( 'template_redirect', function () {
	$path = strtolower( trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' ) );
	$old  = qpc_old_urls();
	if ( isset( $old[ $path ] ) ) {
		wp_safe_redirect( home_url( '/' . ( $old[ $path ] ? $old[ $path ] . '/' : '' ) ), 301, 'QPC old-site redirect' );
		exit;
	}
}, 1 ); // before WordPress's own "guess the URL" redirect

/**
 * If the old site's files are still on the server, the web server sends them
 * before WordPress runs, so the same redirects also go in .htaccess. Leftover
 * files from the old site (data, the Git repo's folders) are hidden too.
 */
function qpc_write_htaccess_redirects() {
	$rules = array( '<IfModule mod_rewrite.c>', 'RewriteEngine On' );
	foreach ( qpc_old_urls() as $from => $to ) {
		$rules[] = 'RewriteRule ^' . preg_quote( $from, '' ) . '$ /' . ( $to ? $to . '/' : '' ) . ' [R=301,L,NC]';
	}
	$rules[] = 'RewriteRule ^(wordpress|archive)(/|$) - [R=404,L]';
	$rules[] = 'RewriteRule ^(sermons\\.json|verses\\.json|qpc-calendar-worker\\.js|CALENDAR-SETUP\\.txt|\\.gitignore)$ - [R=404,L]';
	$rules[] = '</IfModule>';
	require_once ABSPATH . 'wp-admin/includes/misc.php';
	if ( insert_with_markers( ABSPATH . '.htaccess', 'QPC old site', $rules ) ) {
		update_option( 'qpc_htaccess_version', QPC_CORE_VERSION, false );
	}
}
add_action( 'admin_init', function () {
	if ( get_option( 'qpc_htaccess_version' ) !== QPC_CORE_VERSION ) {
		qpc_write_htaccess_redirects();
	}
} );
