<?php
/**
 * Data the front-end scripts read (same shape the old sermons.json had):
 *   /wp-json/qpc/v1/sermons   { series: [...], episodes: [...] }
 *   /wp-json/qpc/v1/calendar  the church calendar (.ics), cached for 30 minutes
 */

defined( 'ABSPATH' ) || exit;

const QPC_CAL_TTL = 30 * MINUTE_IN_SECONDS;

add_action( 'rest_api_init', function () {
	register_rest_route( 'qpc/v1', '/sermons', array(
		'methods'             => 'GET',
		'permission_callback' => '__return_true',
		'callback'            => function () { return rest_ensure_response( qpc_sermon_data() ); },
	) );
	register_rest_route( 'qpc/v1', '/calendar', array(
		'methods'             => 'GET',
		'permission_callback' => '__return_true',
		'callback'            => 'qpc_calendar_response',
	) );
} );

/* ---------- Cache: rebuilt whenever a sermon or series changes ---------- */

function qpc_flush_data_cache() {
	delete_transient( 'qpc_data_sermons_v2' );
}
add_action( 'save_post_qpc_sermon', 'qpc_flush_data_cache' );
add_action( 'deleted_post', 'qpc_flush_data_cache' );
add_action( 'trashed_post', 'qpc_flush_data_cache' );
add_action( 'edited_qpc_series', 'qpc_flush_data_cache' );
add_action( 'created_qpc_series', 'qpc_flush_data_cache' );
add_action( 'delete_qpc_series', 'qpc_flush_data_cache' );
add_action( 'set_object_terms', 'qpc_flush_data_cache' );

/* ---------- Sermons ---------- */

function qpc_sermon_data() {
	$cached = get_transient( 'qpc_data_sermons_v2' );
	if ( false !== $cached ) {
		return $cached;
	}

	$series = array();
	foreach ( get_terms( array( 'taxonomy' => 'qpc_series', 'hide_empty' => false ) ) as $t ) {
		$series[] = array(
			'id'    => $t->slug,
			'label' => $t->name,
			'tag'   => get_term_meta( $t->term_id, 'qpc_tag', true ) ?: 'Series',
		);
	}

	$episodes = array();
	$posts    = get_posts( array(
		'post_type'      => 'qpc_sermon',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'meta_key'       => 'qpc_date',
		'orderby'        => 'meta_value',
		'order'          => 'DESC',
	) );
	foreach ( $posts as $p ) {
		$terms      = get_the_terms( $p, 'qpc_series' );
		$episodes[] = array(
			'spotifyId'   => get_post_meta( $p->ID, 'qpc_spotify', true ),
			'audioUrl'    => get_post_meta( $p->ID, 'qpc_audio_url', true ),
			// Featured image if someone set one, else the podcast's episode art, else the podcast cover.
			'image'       => get_the_post_thumbnail_url( $p, 'medium' ) ?: ( get_post_meta( $p->ID, 'qpc_image', true ) ?: get_option( 'qpc_podcast_image', '' ) ),
			'_rssGuid'    => get_post_meta( $p->ID, 'qpc_rss_guid', true ) ?: 'post-' . $p->ID,
			'series'      => $terms && ! is_wp_error( $terms ) ? $terms[0]->slug : 'new',
			'title'       => get_the_title( $p ),
			'preacher'    => get_post_meta( $p->ID, 'qpc_preacher', true ),
			'date'        => get_post_meta( $p->ID, 'qpc_date', true ),
			'duration'    => get_post_meta( $p->ID, 'qpc_duration', true ),
			'scripture'   => get_post_meta( $p->ID, 'qpc_scripture', true ),
			'description' => wp_strip_all_tags( $p->post_excerpt ),
			'transcript'  => trim( apply_filters( 'the_content', $p->post_content ) ),
		);
	}

	// Bible books in biblical order (Genesis → Revelation), then any other series A–Z.
	$book_order = array_flip( array_keys( qpc_bible_books() ) );
	usort( $series, function ( $a, $b ) use ( $book_order ) {
		$ia = $book_order[ $a['label'] ] ?? PHP_INT_MAX;
		$ib = $book_order[ $b['label'] ] ?? PHP_INT_MAX;
		return $ia === $ib ? strcasecmp( $a['label'], $b['label'] ) : $ia <=> $ib;
	} );

	$data = array( 'series' => $series, 'episodes' => $episodes );
	set_transient( 'qpc_data_sermons_v2', $data, DAY_IN_SECONDS );
	return $data;
}

/* Series "tag" (the small label on each series section, e.g. Series / Seasonal). */
add_action( 'qpc_series_add_form_fields', function () {
	echo '<div class="form-field"><label for="qpc_tag">' . esc_html__( 'Label', 'qpc' ) . '</label><input name="qpc_tag" id="qpc_tag" type="text" value="Series"><p>' . esc_html__( 'Shown above the series name, e.g. "Series" or "Seasonal".', 'qpc' ) . '</p></div>';
} );
add_action( 'qpc_series_edit_form_fields', function ( $term ) {
	$v = get_term_meta( $term->term_id, 'qpc_tag', true ) ?: 'Series';
	echo '<tr class="form-field"><th><label for="qpc_tag">' . esc_html__( 'Label', 'qpc' ) . '</label></th><td><input name="qpc_tag" id="qpc_tag" type="text" value="' . esc_attr( $v ) . '"><p class="description">' . esc_html__( 'Shown above the series name, e.g. "Series" or "Seasonal".', 'qpc' ) . '</p></td></tr>';
} );
$qpc_save_series_tag = function ( $term_id ) {
	if ( isset( $_POST['qpc_tag'] ) && current_user_can( 'manage_categories' ) ) {
		update_term_meta( $term_id, 'qpc_tag', sanitize_text_field( wp_unslash( $_POST['qpc_tag'] ) ) );
	}
};
add_action( 'created_qpc_series', $qpc_save_series_tag, 5 );
add_action( 'edited_qpc_series', $qpc_save_series_tag, 5 );

/* ---------- Calendar ---------- */

function qpc_calendar_ics() {
	$ics = get_transient( 'qpc_calendar_ics' );
	if ( false !== $ics ) {
		return $ics;
	}
	$url = qpc_setting( 'calendar_ics_url' );
	if ( ! $url ) {
		return new WP_Error( 'qpc_no_calendar', 'No calendar feed set', array( 'status' => 404 ) );
	}
	$res = wp_remote_get( $url, array( 'timeout' => 15, 'headers' => array( 'Accept' => 'text/calendar' ), 'user-agent' => 'QPC-Website/1.0' ) );
	$ok  = ! is_wp_error( $res ) && 200 === wp_remote_retrieve_response_code( $res ) && false !== strpos( wp_remote_retrieve_body( $res ), 'BEGIN:VCALENDAR' );
	if ( $ok ) {
		$ics = wp_remote_retrieve_body( $res );
		set_transient( 'qpc_calendar_ics', $ics, QPC_CAL_TTL );
		update_option( 'qpc_calendar_last_good', $ics, false );
		return $ics;
	}
	// Feed down: keep showing the last copy we had, and try again in 5 minutes.
	$last = get_option( 'qpc_calendar_last_good' );
	if ( $last ) {
		set_transient( 'qpc_calendar_ics', $last, 5 * MINUTE_IN_SECONDS );
		return $last;
	}
	return new WP_Error( 'qpc_calendar_unavailable', 'Calendar feed unavailable', array( 'status' => 502 ) );
}

function qpc_calendar_response() {
	$ics = qpc_calendar_ics();
	if ( is_wp_error( $ics ) ) {
		return $ics;
	}
	// Send the .ics text as-is instead of JSON.
	add_filter( 'rest_pre_serve_request', function ( $served ) use ( $ics ) {
		if ( ! headers_sent() ) {
			header( 'Content-Type: text/calendar; charset=utf-8' );
			header( 'Cache-Control: public, max-age=900' );
		}
		echo $ics; // phpcs:ignore WordPress.Security.EscapeOutput -- raw calendar file
		return true;
	} );
	return new WP_REST_Response( null, 200 );
}
