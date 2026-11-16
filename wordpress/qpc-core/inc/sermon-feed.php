<?php
/**
 * Podcast feed → Sermons. Runs twice a day (and from Settings → Church Site).
 *
 * Each new episode becomes a published Sermon. Episode titles look like
 * "Acts 23: A Clear Conscience - Pastor Robert McMullan", so the part after the
 * last " - " is used as the preacher, and the sermon is put in a series whose
 * name the title starts with (e.g. "Acts"). Anything unmatched shows under
 * "New Episodes" on the Sermons page until someone ticks a series for it.
 */

defined( 'ABSPATH' ) || exit;

function qpc_schedule_sermon_feed() {
	if ( ! wp_next_scheduled( 'qpc_sermon_feed_check' ) ) {
		wp_schedule_event( time() + 300, 'twicedaily', 'qpc_sermon_feed_check' );
	}
}
add_action( 'init', 'qpc_schedule_sermon_feed' );
add_action( 'qpc_sermon_feed_check', 'qpc_import_sermon_feed' );

add_action( 'admin_post_qpc_check_feed', function () {
	check_admin_referer( 'qpc_check_feed' );
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'qpc' ) );
	}
	$added = qpc_import_sermon_feed();
	wp_safe_redirect( add_query_arg( 'qpc_added', is_wp_error( $added ) ? 0 : $added, admin_url( 'options-general.php?page=qpc-settings' ) ) );
	exit;
} );

/** Normalised title for matching ("Psalm 22: Psalms…" ≈ "Psalm 22: Psalms… - Pastor X"). */
function qpc_title_key( $title ) {
	return preg_replace( '/[^a-z0-9]/', '', strtolower( remove_accents( html_entity_decode( $title, ENT_QUOTES ) ) ) );
}

/** Split "Title - Preacher" on the last " - ". */
function qpc_split_episode_title( $raw ) {
	$raw = trim( html_entity_decode( $raw, ENT_QUOTES ) );
	if ( preg_match( '/^(.*\S)\s+[-–]\s+([^-–]+)$/u', $raw, $m ) && preg_match( '/^(Pastor|Reverend|Rev\.?|Dr\.?|Mr\.?|[A-Z][a-z]+ [A-Z])/u', trim( $m[2] ) ) ) {
		return array( trim( $m[1] ), trim( $m[2] ) );
	}
	return array( $raw, '' );
}

/**
 * Books of the Bible: name used for the series => other ways titles start.
 */
function qpc_bible_books() {
	$books = array(
		'Genesis', 'Exodus', 'Leviticus', 'Numbers', 'Deuteronomy', 'Joshua', 'Judges', 'Ruth',
		'1 Samuel', '2 Samuel', '1 Kings', '2 Kings', '1 Chronicles', '2 Chronicles', 'Ezra', 'Nehemiah',
		'Esther', 'Job', 'Psalms', 'Proverbs', 'Ecclesiastes', 'Song of Songs', 'Isaiah', 'Jeremiah',
		'Lamentations', 'Ezekiel', 'Daniel', 'Hosea', 'Joel', 'Amos', 'Obadiah', 'Jonah', 'Micah', 'Nahum',
		'Habakkuk', 'Zephaniah', 'Haggai', 'Zechariah', 'Malachi',
		'Matthew', 'Mark', 'Luke', 'John', 'Acts', 'Romans', '1 Corinthians', '2 Corinthians', 'Galatians',
		'Ephesians', 'Philippians', 'Colossians', '1 Thessalonians', '2 Thessalonians', '1 Timothy',
		'2 Timothy', 'Titus', 'Philemon', 'Hebrews', 'James', '1 Peter', '2 Peter', '1 John', '2 John',
		'3 John', 'Jude', 'Revelation',
	);
	$aliases = array(
		'Psalms'        => array( 'Psalm' ),
		'Song of Songs' => array( 'Song of Solomon' ),
		'Revelation'    => array( 'Revelations' ),
	);
	$out = array();
	foreach ( $books as $b ) {
		$names = array_merge( array( $b ), $aliases[ $b ] ?? array() );
		if ( preg_match( '/^([123]) (.+)$/', $b, $m ) ) {
			$ord     = array( '1' => '1st', '2' => '2nd', '3' => '3rd' )[ $m[1] ];
			$names[] = "$ord {$m[2]}";
			$names[] = str_repeat( 'I', (int) $m[1] ) . ' ' . $m[2]; // "II Kings"
		}
		$out[ $b ] = $names;
	}
	return $out;
}

/**
 * Series for a podcast episode: the Bible book its title starts with
 * ("Acts 23: …" → Acts, "Psalm 22" → Psalms), creating that series if needed.
 * No book → no series (shows under "New Episodes" until someone ticks one).
 */
function qpc_series_for_title( $title ) {
	$t     = strtolower( trim( $title ) );
	$match = '';
	foreach ( qpc_bible_books() as $book => $names ) {
		foreach ( $names as $n ) {
			$n = strtolower( $n );
			// Longest match wins, so "1 John 4" is 1 John, not John.
			if ( 0 === strpos( $t, $n ) && ! ctype_alpha( (string) substr( $t, strlen( $n ), 1 ) ) && strlen( $n ) > strlen( $match ) ) {
				$match = $n;
				$found = $book;
			}
		}
	}
	if ( ! $match ) {
		return 0;
	}
	$term = get_term_by( 'name', $found, 'qpc_series' ) ?: get_term_by( 'slug', sanitize_title( $found ), 'qpc_series' );
	if ( $term ) {
		return (int) $term->term_id;
	}
	$new = wp_insert_term( $found, 'qpc_series' );
	if ( is_wp_error( $new ) ) {
		return 0;
	}
	update_term_meta( $new['term_id'], 'qpc_tag', 'Series' );
	return (int) $new['term_id'];
}

function qpc_parse_duration( $raw ) {
	$raw = trim( (string) $raw );
	if ( '' === $raw ) {
		return '';
	}
	if ( false !== strpos( $raw, ':' ) ) {
		$p    = array_map( 'intval', explode( ':', $raw ) );
		$mins = 3 === count( $p ) ? $p[0] * 60 + $p[1] : $p[0];
		return $mins . ' min';
	}
	return is_numeric( $raw ) ? round( $raw / 60 ) . ' min' : $raw;
}

/**
 * @return int|WP_Error number of sermons added
 */
function qpc_import_sermon_feed() {
	$url = qpc_setting( 'sermon_rss_url' );
	if ( ! $url ) {
		return 0;
	}
	$res = wp_remote_get( $url, array( 'timeout' => 20 ) );
	if ( is_wp_error( $res ) || 200 !== wp_remote_retrieve_response_code( $res ) ) {
		return new WP_Error( 'qpc_feed', 'Podcast feed could not be fetched' );
	}
	libxml_use_internal_errors( true );
	$xml = simplexml_load_string( wp_remote_retrieve_body( $res ) );
	if ( ! $xml || ! isset( $xml->channel->item ) ) {
		return new WP_Error( 'qpc_feed', 'Podcast feed could not be read' );
	}

	// What we already have, by podcast GUID and by title.
	$known_guids  = array();
	$known_titles = array();
	foreach ( get_posts( array( 'post_type' => 'qpc_sermon', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids' ) ) as $id ) {
		$g = get_post_meta( $id, 'qpc_rss_guid', true );
		if ( $g ) {
			$known_guids[ $g ] = $id;
		}
		$known_titles[ qpc_title_key( get_the_title( $id ) ) ] = $id;
	}

	$added = 0;
	foreach ( $xml->channel->item as $item ) {
		$guid = trim( (string) $item->guid );
		if ( ! $guid || isset( $known_guids[ $guid ] ) ) {
			continue;
		}
		$itunes            = $item->children( 'http://www.itunes.com/dtds/podcast-1.0.dtd' );
		list( $title, $by ) = qpc_split_episode_title( (string) $item->title );
		$audio             = isset( $item->enclosure ) ? (string) $item->enclosure['url'] : '';
		$date              = (string) $item->pubDate ? wp_date( 'Y-m-d', strtotime( (string) $item->pubDate ) ) : current_time( 'Y-m-d' );
		$desc              = trim( wp_strip_all_tags( html_entity_decode( (string) ( $itunes->summary ?: $item->description ), ENT_QUOTES ) ) );

		// Already entered by hand (e.g. imported from the old site)? Just link it to the feed.
		$existing = $known_titles[ qpc_title_key( $title ) ] ?? ( $known_titles[ qpc_title_key( (string) $item->title ) ] ?? 0 );
		if ( $existing ) {
			update_post_meta( $existing, 'qpc_rss_guid', $guid );
			if ( $audio && ! get_post_meta( $existing, 'qpc_audio_url', true ) ) {
				update_post_meta( $existing, 'qpc_audio_url', esc_url_raw( $audio ) );
			}
			$known_guids[ $guid ] = $existing;
			continue;
		}

		$id = wp_insert_post( array(
			'post_type'    => 'qpc_sermon',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_excerpt' => $desc,
			'post_content' => '',
			'post_date'    => $date . ' 10:00:00',
		), true );
		if ( is_wp_error( $id ) ) {
			continue;
		}
		update_post_meta( $id, 'qpc_rss_guid', $guid );
		update_post_meta( $id, 'qpc_date', $date );
		update_post_meta( $id, 'qpc_preacher', $by ?: qpc_setting( 'default_preacher' ) );
		update_post_meta( $id, 'qpc_duration', qpc_parse_duration( (string) $itunes->duration ) );
		update_post_meta( $id, 'qpc_audio_url', esc_url_raw( $audio ) );
		if ( preg_match( '~open\.spotify\.com/episode/([A-Za-z0-9]+)~', (string) $item->link, $m ) ) {
			update_post_meta( $id, 'qpc_spotify', $m[1] );
		}
		$series = qpc_series_for_title( $title );
		if ( $series ) {
			wp_set_object_terms( $id, array( $series ), 'qpc_series' );
		}
		$known_guids[ $guid ]                   = $id;
		$known_titles[ qpc_title_key( $title ) ] = $id;
		++$added;
	}
	update_option( 'qpc_sermon_feed_last_run', time(), false );
	qpc_flush_data_cache();
	return $added;
}
