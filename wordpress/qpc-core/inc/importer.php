<?php
/**
 * One-time import of the old static site (Tools → Import Old Site, or
 * `wp eval 'qpc_run_import();'`). Safe to run again: it updates what it
 * created before (matched by slug / Spotify ID) instead of duplicating.
 *
 * Images come from wp-content/qpc-import/images/ if that folder exists,
 * otherwise they are downloaded from the old site (QPC_IMPORT_IMAGE_BASE).
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'QPC_IMPORT_IMAGE_BASE' ) ) {
	define( 'QPC_IMPORT_IMAGE_BASE', 'https://queanbeyanpc.org/' );
}

function qpc_import_pages() {
	// slug => [ title, menu order ]
	return array(
		'home'              => array( 'Home', 0 ),
		'church-life'       => array( 'Church Life', 10 ),
		'our-history'       => array( 'Our History', 20 ),
		'our-team'          => array( 'Our Team', 30 ),
		'sermons'           => array( 'Sermons', 40 ),
		'calendar'          => array( 'Calendar', 50 ),
		'plan-your-visit'   => array( 'Plan Your Visit', 60 ),
		'playgroup'         => array( "Joey's Play Group", 100 ),
		'm3'                => array( "M3 — Men's Motor Ministry", 101 ),
		'mens-brekkie'      => array( "Men's Brekkie", 102 ),
		'prayer'            => array( 'Midweek Prayer Meeting', 103 ),
		'bible-study'       => array( 'Bible Study Groups', 104 ),
		'ladies-craft'      => array( 'Ladies Craft & Chat', 105 ),
		'christian-singles' => array( 'Christian Singles', 106 ),
		'christmas-gala'    => array( 'Christmas Gala', 110 ),
	);
}

add_action( 'admin_menu', function () {
	add_management_page( __( 'Import Old Site', 'qpc' ), __( 'Import Old Site', 'qpc' ), 'manage_options', 'qpc-import', function () {
		echo '<div class="wrap"><h1>' . esc_html__( 'Import the old static website', 'qpc' ) . '</h1>';
		if ( isset( $_POST['qpc_import'] ) && check_admin_referer( 'qpc_import' ) ) {
			$log = qpc_run_import();
			echo '<div class="notice notice-success"><p>' . esc_html__( 'Import finished.', 'qpc' ) . '</p></div><pre style="background:#fff;padding:1em;max-height:400px;overflow:auto">' . esc_html( implode( "\n", $log ) ) . '</pre>';
		}
		echo '<p>' . esc_html__( 'Creates the pages, menus, sermons and the Christmas Gala notice from the old site. Running it again updates those items to match the old site (any edits made to them here would be overwritten), so only run it while setting up.', 'qpc' ) . '</p>';
		echo '<form method="post">';
		wp_nonce_field( 'qpc_import' );
		submit_button( __( 'Run import', 'qpc' ), 'primary', 'qpc_import' );
		echo '</form></div>';
	} );
} );

/**
 * @return string[] log lines
 */
function qpc_run_import() {
	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 600 );
	}
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_defer_term_counting( true );
	// Imported pages contain embeds (maps, Spotify, ticket form) that WordPress's
	// content filter would strip when no admin is logged in (e.g. via WP-CLI).
	kses_remove_filters();

	$log = array();
	$log = array_merge( $log, qpc_import_site_settings() );
	$log = array_merge( $log, qpc_import_sermons() );
	$log = array_merge( $log, qpc_import_static_pages() );
	$log = array_merge( $log, qpc_import_menus() );
	$log = array_merge( $log, qpc_import_notices() );

	wp_defer_term_counting( false );
	kses_init_filters();
	qpc_flush_data_cache();
	flush_rewrite_rules();
	$log[] = 'Done.';
	return $log;
}

function qpc_import_site_settings() {
	update_option( 'blogname', 'Queanbeyan Presbyterian Church' );
	update_option( 'blogdescription', 'Reflecting Christ, Refreshing Believers, Reaching Friends' );
	update_option( 'timezone_string', 'Australia/Sydney' );
	update_option( 'date_format', 'j F Y' );
	update_option( 'start_of_week', 1 );
	update_option( 'permalink_structure', '/%postname%/' );
	update_option( 'default_comment_status', 'closed' );
	update_option( 'default_ping_status', 'closed' );
	// Remove WordPress's starter content.
	foreach ( array( array( 'sample-page', 'page' ), array( 'hello-world', 'post' ) ) as list( $slug, $type ) ) {
		$p = get_page_by_path( $slug, OBJECT, $type );
		if ( $p ) {
			wp_delete_post( $p->ID, true );
		}
	}
	return array( 'Site settings: title, timezone (Australia/Sydney), permalinks, comments off.' );
}

function qpc_import_json( $file ) {
	return json_decode( (string) file_get_contents( QPC_CORE_DIR . '/data/' . $file ), true );
}

/* ---------- Sermons ---------- */

function qpc_import_sermons() {
	$data = qpc_import_json( 'sermons.json' );
	$log  = array();
	$term = array();
	foreach ( $data['series'] as $i => $s ) {
		$existing = get_term_by( 'slug', $s['id'], 'qpc_series' );
		$t        = $existing ? array( 'term_id' => $existing->term_id ) : wp_insert_term( $s['label'], 'qpc_series', array( 'slug' => $s['id'] ) );
		if ( is_wp_error( $t ) ) {
			continue;
		}
		update_term_meta( $t['term_id'], 'qpc_tag', $s['tag'] ?? 'Series' );
		$term[ $s['id'] ] = (int) $t['term_id'];
	}
	$log[] = 'Series: ' . count( $term );

	$n = 0;
	foreach ( $data['episodes'] as $e ) {
		$found = get_posts( array( 'post_type' => 'qpc_sermon', 'post_status' => 'any', 'meta_key' => 'qpc_spotify', 'meta_value' => $e['spotifyId'], 'fields' => 'ids', 'numberposts' => 1 ) );
		$args  = array(
			'ID'           => $found[0] ?? 0,
			'post_type'    => 'qpc_sermon',
			'post_status'  => 'publish',
			'post_title'   => $e['title'],
			'post_excerpt' => $e['description'] ?? '',
			'post_content' => trim( $e['transcript'] ?? '' ) ? "<!-- wp:html -->\n" . $e['transcript'] . "\n<!-- /wp:html -->" : '',
			'post_date'    => $e['date'] . ' 10:00:00',
		);
		$id    = wp_insert_post( wp_slash( $args ), true );
		if ( is_wp_error( $id ) ) {
			$log[] = 'Sermon failed: ' . $e['title'];
			continue;
		}
		update_post_meta( $id, 'qpc_spotify', $e['spotifyId'] );
		update_post_meta( $id, 'qpc_date', $e['date'] );
		update_post_meta( $id, 'qpc_preacher', $e['preacher'] ?? '' );
		update_post_meta( $id, 'qpc_scripture', $e['scripture'] ?? '' );
		update_post_meta( $id, 'qpc_duration', $e['duration'] ?? '' );
		if ( isset( $term[ $e['series'] ] ) ) {
			wp_set_object_terms( $id, array( $term[ $e['series'] ] ), 'qpc_series' );
		}
		++$n;
	}
	$log[] = "Sermons from old site: $n";

	$added = qpc_import_sermon_feed();
	$log[] = is_wp_error( $added ) ? 'Podcast feed: ' . $added->get_error_message() . ' (it will retry automatically)' : "Podcast feed: $added more sermon(s) added";
	return $log;
}

/* ---------- Images ---------- */

/** Old-site image path (e.g. "images/staff/x.png") → Media Library attachment ID. */
function qpc_import_image( $rel ) {
	$rel = ltrim( $rel, '/' );
	$map = get_option( 'qpc_import_media_map', array() );
	if ( isset( $map[ $rel ] ) && get_post( $map[ $rel ] ) ) {
		return (int) $map[ $rel ];
	}
	$local = WP_CONTENT_DIR . '/qpc-import/' . $rel;
	if ( file_exists( $local ) ) {
		$tmp = wp_tempnam( basename( $rel ) );
		copy( $local, $tmp );
	} else {
		// The old site first; if it has already been replaced, the copy in the Git repo.
		$path = str_replace( '%2F', '/', rawurlencode( $rel ) );
		foreach ( array( QPC_IMPORT_IMAGE_BASE, 'https://raw.githubusercontent.com/fromchaosimportcalm/qpc-website/main/' ) as $base ) {
			$tmp = download_url( $base . $path, 60 );
			if ( ! is_wp_error( $tmp ) ) {
				break;
			}
		}
		if ( is_wp_error( $tmp ) ) {
			return 0;
		}
	}
	$id = media_handle_sideload( array( 'name' => basename( $rel ), 'tmp_name' => $tmp ), 0 );
	if ( is_wp_error( $id ) ) {
		@unlink( $tmp ); // phpcs:ignore
		return 0;
	}
	$map[ $rel ] = $id;
	update_option( 'qpc_import_media_map', $map, false );
	return $id;
}

/* ---------- Pages ---------- */

function qpc_import_static_pages() {
	$log = array();
	foreach ( qpc_import_pages() as $slug => list( $title, $order ) ) {
		$file = QPC_CORE_DIR . "/data/pages/$slug.html";
		if ( ! file_exists( $file ) ) {
			continue;
		}
		$content = (string) file_get_contents( $file );

		// Swap old image paths for Media Library copies (and tell image blocks their ID).
		$content = preg_replace_callback( '~<!-- wp:image(?: (\{.*?\}))? -->\s*<figure([^>]*)><img src="/?(images/[^"]+)"~', function ( $m ) {
			$id = qpc_import_image( $m[3] );
			if ( ! $id ) {
				return $m[0];
			}
			$attrs       = $m[1] ? json_decode( $m[1], true ) : array();
			$attrs['id'] = $id;
			return '<!-- wp:image ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES ) . " -->\n<figure" . $m[2] . '><img src="' . esc_url( wp_get_attachment_url( $id ) ) . '" class="wp-image-' . $id . '"';
		}, $content );
		$content = preg_replace_callback( '~(src|href)="/?(images/[^"]+)"~', function ( $m ) {
			$id = qpc_import_image( $m[2] );
			return $id ? $m[1] . '="' . esc_url( wp_get_attachment_url( $id ) ) . '"' : $m[0];
		}, $content );
		// Links between pages are written as /our-team/ etc. Make them full addresses so
		// they still work when WordPress lives in a folder (e.g. a staging copy at /new/).
		$content = preg_replace_callback( '~href="/([^"/][^"]*)?"~', function ( $m ) {
			return 'href="' . esc_url( home_url( '/' . ( $m[1] ?? '' ) ) ) . '"';
		}, $content );

		$existing = get_page_by_path( $slug, OBJECT, 'page' );
		$id       = wp_insert_post( wp_slash( array(
			'ID'           => $existing ? $existing->ID : 0,
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_name'    => $slug,
			'post_title'   => $title,
			'post_content' => $content,
			'menu_order'   => $order,
		) ), true );
		if ( is_wp_error( $id ) ) {
			$log[] = "Page failed: $slug";
			continue;
		}
		// Show the page's look in the editor's Template menu ("Page style: …").
		if ( function_exists( 'qpc_page_style_data' ) && isset( qpc_page_style_data()['styles'][ $slug ] ) ) {
			update_post_meta( $id, '_wp_page_template', 'qpc-style-' . $slug );
		}
		if ( 'home' === $slug ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $id );
		}
		$log[] = "Page: $title";
	}
	// Site logo for the header/footer.
	$logo = qpc_import_image( 'images/qpc_mini_transparent.png' );
	if ( $logo ) {
		set_theme_mod( 'custom_logo', $logo );
	}
	return $log;
}

/* ---------- Menus ---------- */

function qpc_import_menus() {
	$menus = array(
		'primary' => array( 'Main Menu', array( 'home', 'church-life', 'our-history', 'our-team', 'sermons', 'calendar', 'plan-your-visit' ) ),
		'footer'  => array( 'Footer Pages', array( 'home', 'church-life', 'our-history', 'our-team', 'sermons', 'plan-your-visit' ) ),
	);
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	foreach ( $menus as $location => list( $name, $slugs ) ) {
		$menu = wp_get_nav_menu_object( $name );
		if ( $menu ) {
			foreach ( (array) wp_get_nav_menu_items( $menu->term_id ) as $item ) {
				wp_delete_post( $item->ID, true );
			}
			$menu_id = $menu->term_id;
		} else {
			$menu_id = wp_create_nav_menu( $name );
		}
		foreach ( $slugs as $i => $slug ) {
			$page = get_page_by_path( $slug );
			if ( ! $page ) {
				continue;
			}
			wp_update_nav_menu_item( $menu_id, 0, array(
				'menu-item-title'     => 'church-life' === $slug ? 'Church Life' : ( 'home' === $slug ? 'Home' : $page->post_title ),
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $page->ID,
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
				'menu-item-position'  => $i + 1,
				'menu-item-classes'   => ( 'primary' === $location && 'plan-your-visit' === $slug ) ? 'nav-cta' : '',
			) );
		}
		$locations[ $location ] = $menu_id;
	}
	set_theme_mod( 'nav_menu_locations', $locations );
	return array( 'Menus: main and footer' );
}

/* ---------- Notices ---------- */

function qpc_import_notices() {
	$existing = get_page_by_path( 'christmas-gala-2026', OBJECT, 'qpc_notice' );
	$id       = wp_insert_post( wp_slash( array(
		'ID'           => $existing ? $existing->ID : 0,
		'post_type'    => 'qpc_notice',
		'post_status'  => 'publish',
		'post_name'    => 'christmas-gala-2026',
		'post_title'   => 'Christmas Gala',
		'post_content' => "<!-- wp:paragraph -->\n<p>A proper Christmas meal, fully catered and served on real crockery. Bring your friends, coworkers and family. Everyone's welcome at the table.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Southern Pavilion, Queanbeyan Showground. Tickets \$30 per adult, \$15 per child with a meal, and free for children without.</p>\n<!-- /wp:paragraph -->",
	) ), true );
	if ( is_wp_error( $id ) ) {
		return array( 'Notice failed' );
	}
	update_post_meta( $id, 'qpc_label', 'Special Event · Friday 18 December' );
	update_post_meta( $id, 'qpc_show_until', '2026-12-18' );
	update_post_meta( $id, 'qpc_link_url', home_url( '/christmas-gala/#tickets' ) );
	update_post_meta( $id, 'qpc_link_text', 'Get Tickets' );
	$img = qpc_import_image( 'images/christmas-gala-2026.png' );
	if ( $img ) {
		set_post_thumbnail( $id, $img );
	}
	return array( 'Notice: Christmas Gala (shows until 18 Dec 2026)' );
}
