<?php
/**
 * "Church Details" panel in Appearance → Customize.
 * These appear in the footer and anywhere qpc_detail() is used, so a phone
 * number or address change is made once.
 */

defined( 'ABSPATH' ) || exit;

function qpc_detail_defaults() {
	return array(
		'tagline'      => 'Reflecting · Refreshing · Reaching',
		'blurb'        => 'A congregation of the Presbyterian Church of Australia, faithfully serving Queanbeyan and the surrounding region for the glory of God.',
		'address'      => '2 Morisset Street, Queanbeyan NSW 2620',
		'phone'        => '+61 422 715 329',
		'email'        => 'admin@queanbeyanpc.org',
		'service_times' => 'Sunday 9:00 am & 10:45 am',
		'facebook_url' => 'https://www.facebook.com/profile.php?id=100088331591011',
		'spotify_url'  => 'https://open.spotify.com/show/5XJPM6ftmGcBDK5yanFw9n',
	);
}

function qpc_detail( $key ) {
	$defaults = qpc_detail_defaults();
	$value    = get_theme_mod( 'qpc_' . $key, $defaults[ $key ] ?? '' );
	return '' === $value ? ( $defaults[ $key ] ?? '' ) : $value;
}

/** mailto: link with the same pre-filled subject the static site used. */
function qpc_mailto() {
	$body = "Hi,\n\nI found your details on the Queanbeyan Presbyterian Church website and would like to get in touch.\n\n";
	return 'mailto:' . qpc_detail( 'email' ) . '?subject=' . rawurlencode( 'Website Enquiry' ) . '&body=' . rawurlencode( $body );
}

function qpc_tel() {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', qpc_detail( 'phone' ) );
}

add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) {
	$wp_customize->add_section( 'qpc_details', array(
		'title'       => __( 'Church Details', 'qpc' ),
		'description' => __( 'Shown in the footer on every page.', 'qpc' ),
		'priority'    => 30,
	) );

	$fields = array(
		'tagline'       => array( __( 'Tagline (under footer logo)', 'qpc' ), 'text' ),
		'blurb'         => array( __( 'Footer description', 'qpc' ), 'textarea' ),
		'address'       => array( __( 'Address', 'qpc' ), 'text' ),
		'phone'         => array( __( 'Phone', 'qpc' ), 'text' ),
		'email'         => array( __( 'Email', 'qpc' ), 'email' ),
		'service_times' => array( __( 'Service times', 'qpc' ), 'text' ),
		'facebook_url'  => array( __( 'Facebook page link', 'qpc' ), 'url' ),
		'spotify_url'   => array( __( 'Spotify podcast link', 'qpc' ), 'url' ),
	);

	$defaults = qpc_detail_defaults();
	foreach ( $fields as $key => list( $label, $type ) ) {
		$sanitize = 'url' === $type ? 'esc_url_raw' : ( 'email' === $type ? 'sanitize_email' : ( 'textarea' === $type ? 'sanitize_textarea_field' : 'sanitize_text_field' ) );
		$wp_customize->add_setting( 'qpc_' . $key, array( 'default' => $defaults[ $key ], 'sanitize_callback' => $sanitize ) );
		$wp_customize->add_control( 'qpc_' . $key, array( 'label' => $label, 'section' => 'qpc_details', 'type' => $type ) );
	}
} );
