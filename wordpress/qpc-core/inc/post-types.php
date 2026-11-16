<?php
/**
 * Content types: Sermons (+ Series), Notices.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'qpc_register_content_types' );

function qpc_register_content_types() {
	register_post_type( 'qpc_sermon', array(
		'labels'        => array(
			'name'          => __( 'Sermons', 'qpc' ),
			'singular_name' => __( 'Sermon', 'qpc' ),
			'add_new_item'  => __( 'Add New Sermon', 'qpc' ),
			'edit_item'     => __( 'Edit Sermon', 'qpc' ),
			'all_items'     => __( 'All Sermons', 'qpc' ),
		),
		'description'   => __( 'Shown on the Sermons page. The text box is the sermon summary / transcript (optional).', 'qpc' ),
		'public'        => true,
		'has_archive'   => false,
		'rewrite'       => array( 'slug' => 'sermon' ),
		'menu_icon'     => 'dashicons-microphone',
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'excerpt', 'revisions' ),
		'show_in_rest'  => true,
		'template'      => array( array( 'core/paragraph', array( 'placeholder' => __( 'Optional: sermon summary or transcript. Leave empty if you don\'t have one.', 'qpc' ) ) ) ),
	) );

	register_taxonomy( 'qpc_series', 'qpc_sermon', array(
		'labels'            => array(
			'name'          => __( 'Series', 'qpc' ),
			'singular_name' => __( 'Series', 'qpc' ),
			'add_new_item'  => __( 'Add New Series', 'qpc' ),
		),
		'hierarchical'      => true, // checkbox UI, easier than tags
		'public'            => false,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => false,
	) );


	register_post_type( 'qpc_notice', array(
		'labels'        => array(
			'name'          => __( 'Notices', 'qpc' ),
			'singular_name' => __( 'Notice', 'qpc' ),
			'add_new_item'  => __( 'Add New Notice', 'qpc' ),
			'edit_item'     => __( 'Edit Notice', 'qpc' ),
			'all_items'     => __( 'All Notices', 'qpc' ),
		),
		'description'   => __( 'Banners on the homepage for events and one-off announcements. They appear and disappear on the dates you set.', 'qpc' ),
		'public'        => false,
		'show_ui'       => true,
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-megaphone',
		'menu_position' => 4,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'revisions' ),
		'template'      => array( array( 'core/paragraph', array( 'placeholder' => __( 'A few sentences about the event: what, where, cost…', 'qpc' ) ) ) ),
		'template_lock' => false,
	) );
}

/* ---------- Admin list columns ---------- */

add_filter( 'manage_qpc_sermon_posts_columns', function ( $cols ) {
	$new = array();
	foreach ( $cols as $k => $v ) {
		if ( 'date' === $k ) {
			$new['qpc_preached'] = __( 'Preached', 'qpc' );
			$new['qpc_scripture'] = __( 'Passage', 'qpc' );
			continue; // the publish date is not useful here
		}
		$new[ $k ] = $v;
	}
	return $new;
} );

add_action( 'manage_qpc_sermon_posts_custom_column', function ( $col, $id ) {
	if ( 'qpc_preached' === $col ) {
		$d = get_post_meta( $id, 'qpc_date', true );
		echo esc_html( $d ? wp_date( get_option( 'date_format' ), strtotime( $d ) ) : '—' );
	}
	if ( 'qpc_scripture' === $col ) {
		echo esc_html( get_post_meta( $id, 'qpc_scripture', true ) ?: '—' );
	}
}, 10, 2 );

add_filter( 'manage_edit-qpc_sermon_sortable_columns', function ( $cols ) {
	$cols['qpc_preached'] = 'qpc_preached';
	return $cols;
} );

add_action( 'pre_get_posts', function ( WP_Query $q ) {
	if ( ! is_admin() || ! $q->is_main_query() || 'qpc_sermon' !== $q->get( 'post_type' ) ) {
		return;
	}
	if ( ! $q->get( 'orderby' ) || 'qpc_preached' === $q->get( 'orderby' ) ) {
		$q->set( 'meta_key', 'qpc_date' );
		$q->set( 'orderby', 'meta_value' );
		if ( ! $q->get( 'order' ) ) {
			$q->set( 'order', 'DESC' );
		}
	}
} );

add_filter( 'manage_qpc_notice_posts_columns', function ( $cols ) {
	unset( $cols['date'] );
	$cols['qpc_showing'] = __( 'Showing on homepage', 'qpc' );
	return $cols;
} );

add_action( 'manage_qpc_notice_posts_custom_column', function ( $col, $id ) {
	if ( 'qpc_showing' !== $col ) {
		return;
	}
	$from  = get_post_meta( $id, 'qpc_show_from', true );
	$until = get_post_meta( $id, 'qpc_show_until', true );
	$fmt   = function ( $d ) { return $d ? wp_date( 'j M Y', strtotime( $d ) ) : ''; };
	$state = qpc_notice_is_current( $id ) ? '✅ ' . __( 'Now', 'qpc' ) : ( $until && $until < current_time( 'Y-m-d' ) ? '⏹ ' . __( 'Ended', 'qpc' ) : '⏳ ' . __( 'Scheduled', 'qpc' ) );
	echo esc_html( $state . ' — ' . ( $from ? $fmt( $from ) : __( 'now', 'qpc' ) ) . ' → ' . ( $until ? $fmt( $until ) : __( 'no end date', 'qpc' ) ) );
}, 10, 2 );

function qpc_notice_is_current( $id ) {
	if ( 'publish' !== get_post_status( $id ) ) {
		return false;
	}
	$today = current_time( 'Y-m-d' );
	$from  = get_post_meta( $id, 'qpc_show_from', true );
	$until = get_post_meta( $id, 'qpc_show_until', true );
	return ( ! $from || $from <= $today ) && ( ! $until || $until >= $today );
}
