<?php
/**
 * "Website Help" box on the Dashboard: the everyday jobs, in plain words.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_dashboard_setup', function () {
	wp_add_dashboard_widget( 'qpc_help', __( 'Website Help — Queanbeyan Presbyterian Church', 'qpc' ), 'qpc_render_help_widget' );

	// Put it first.
	global $wp_meta_boxes;
	$normal = $wp_meta_boxes['dashboard']['normal']['core'] ?? array();
	if ( isset( $normal['qpc_help'] ) ) {
		$wp_meta_boxes['dashboard']['normal']['core'] = array( 'qpc_help' => $normal['qpc_help'] ) + $normal;
	}
} );

function qpc_render_help_widget() {
	$last = get_option( 'qpc_sermon_feed_last_run' );
	$jobs = array(
		array(
			__( 'Sermons', 'qpc' ),
			__( 'New podcast episodes appear on the Sermons page by themselves (checked twice a day). To file one under a series, open it in Sermons and tick the series on the right. To add one by hand: Sermons → Add New, fill in the details box, and Publish.', 'qpc' ),
			admin_url( 'edit.php?post_type=qpc_sermon' ),
		),
		array(
			__( 'Homepage notices (events, special services)', 'qpc' ),
			__( 'Notices → Add New. Give it a title, a few sentences, a picture (Featured image) and the dates to show it. It appears on the homepage on the "Show from" date and disappears after the "Show until" date — no need to remove it.', 'qpc' ),
			admin_url( 'post-new.php?post_type=qpc_notice' ),
		),
		array(
			__( 'Changing text on a page', 'qpc' ),
			__( 'Pages → hover the page → Edit. Click any text and type. Click Update when done. Headings, cards and buttons are normal blocks; ready-made sections are in the "+" inserter under Patterns → QPC Sections.', 'qpc' ),
			admin_url( 'edit.php?post_type=page' ),
		),
		array(
			__( 'Calendar', 'qpc' ),
			__( 'The Calendar page shows the church iCloud calendar. Add events in that calendar as usual; the website updates within 30 minutes.', 'qpc' ),
			admin_url( 'options-general.php?page=qpc-settings' ),
		),
		array(
			__( 'Phone, address, email, service times (footer)', 'qpc' ),
			__( 'Appearance → Customize → Church Details.', 'qpc' ),
			admin_url( 'customize.php?autofocus[section]=qpc_details' ),
		),
		array(
			__( 'Menu at the top of the site', 'qpc' ),
			__( 'Appearance → Menus → "Main Menu". The "Plan Your Visit" button style comes from the CSS class nav-cta on that item.', 'qpc' ),
			admin_url( 'nav-menus.php' ),
		),
	);
	echo '<ol style="margin-left:1.2em">';
	foreach ( $jobs as list( $title, $text, $url ) ) {
		echo '<li style="margin-bottom:.8em"><strong><a href="' . esc_url( $url ) . '">' . esc_html( $title ) . '</a></strong><br>' . esc_html( $text ) . '</li>';
	}
	echo '</ol>';
	if ( $last ) {
		/* translators: %s: time ago */
		echo '<p class="description">' . esc_html( sprintf( __( 'Podcast last checked %s ago.', 'qpc' ), human_time_diff( $last ) ) ) . '</p>';
	}
}
