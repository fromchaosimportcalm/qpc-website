<?php
/**
 * Extra fields on Sermons and Notices ("Details" box under the editor).
 * Plain PHP meta boxes: no build step, and they work in the block editor.
 */

defined( 'ABSPATH' ) || exit;

function qpc_meta_fields() {
	return array(
		'qpc_sermon' => array(
			'qpc_date'      => array( 'label' => __( 'Date preached', 'qpc' ), 'type' => 'date' ),
			'qpc_preacher'  => array( 'label' => __( 'Preacher', 'qpc' ), 'type' => 'text' ),
			'qpc_scripture' => array( 'label' => __( 'Bible passage', 'qpc' ), 'type' => 'text', 'help' => __( 'e.g. Psalm 22', 'qpc' ) ),
			'qpc_spotify'   => array( 'label' => __( 'Spotify episode', 'qpc' ), 'type' => 'text', 'help' => __( 'Paste the Spotify episode link (Share → Copy link to episode).', 'qpc' ) ),
			'qpc_duration'  => array( 'label' => __( 'Length', 'qpc' ), 'type' => 'text', 'help' => __( 'e.g. 26 min', 'qpc' ) ),
			'qpc_audio_url' => array( 'label' => __( 'Audio (MP3 link)', 'qpc' ), 'type' => 'url', 'help' => __( 'Filled in automatically from the podcast. Used when there is no Spotify link.', 'qpc' ) ),
			'qpc_image'     => array( 'label' => __( 'Episode artwork (image link)', 'qpc' ), 'type' => 'url', 'help' => __( 'Filled in automatically from the podcast. To use a different picture, set a Featured image instead.', 'qpc' ) ),
			'qpc_rss_guid'  => array( 'label' => __( 'Podcast ID', 'qpc' ), 'type' => 'readonly', 'help' => __( 'Filled in automatically for episodes added from the podcast feed.', 'qpc' ) ),
		),
		'qpc_notice' => array(
			'qpc_show_from'  => array( 'label' => __( 'Show from', 'qpc' ), 'type' => 'date', 'help' => __( 'Leave empty to show straight away.', 'qpc' ) ),
			'qpc_show_until' => array( 'label' => __( 'Show until (inclusive)', 'qpc' ), 'type' => 'date', 'help' => __( 'Usually the day of the event. It disappears the day after.', 'qpc' ) ),
			'qpc_label'      => array( 'label' => __( 'Small heading', 'qpc' ), 'type' => 'text', 'help' => __( 'e.g. Special Event · Friday 18 December', 'qpc' ) ),
			'qpc_link_url'   => array( 'label' => __( 'Button link', 'qpc' ), 'type' => 'url', 'help' => __( 'Optional: a page with more details or tickets.', 'qpc' ) ),
			'qpc_link_text'  => array( 'label' => __( 'Button text', 'qpc' ), 'type' => 'text', 'help' => __( 'e.g. Get Tickets', 'qpc' ) ),
		),
	);
}

add_action( 'init', function () {
	foreach ( qpc_meta_fields() as $post_type => $fields ) {
		foreach ( $fields as $key => $f ) {
			register_post_meta( $post_type, $key, array(
				'type'          => 'string',
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => function () { return current_user_can( 'edit_posts' ); },
			) );
		}
	}
} );

add_action( 'add_meta_boxes', function () {
	$titles = array(
		'qpc_sermon' => __( 'Sermon details', 'qpc' ),
		'qpc_notice' => __( 'When and where it shows', 'qpc' ),
	);
	foreach ( $titles as $post_type => $title ) {
		add_meta_box( 'qpc_details', $title, 'qpc_render_meta_box', $post_type, 'normal', 'high' );
	}
} );

function qpc_render_meta_box( WP_Post $post ) {
	$fields = qpc_meta_fields()[ $post->post_type ] ?? array();
	wp_nonce_field( 'qpc_save_meta', 'qpc_meta_nonce' );
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( $fields as $key => $f ) {
		$value = get_post_meta( $post->ID, $key, true );
		if ( 'readonly' === $f['type'] && '' === $value ) {
			continue;
		}
		echo '<tr><th scope="row"><label for="' . esc_attr( $key ) . '">' . esc_html( $f['label'] ) . '</label></th><td>';
		if ( 'readonly' === $f['type'] ) {
			echo '<code>' . esc_html( $value ) . '</code>';
		} else {
			$type = in_array( $f['type'], array( 'date', 'url' ), true ) ? $f['type'] : 'text';
			echo '<input class="regular-text" type="' . esc_attr( $type ) . '" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '">';
		}
		if ( ! empty( $f['help'] ) ) {
			echo '<p class="description">' . esc_html( $f['help'] ) . '</p>';
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
	if ( 'qpc_notice' === $post->post_type ) {
		echo '<p class="description">' . esc_html__( 'The picture on the left of the banner is the "Featured image" (in the sidebar).', 'qpc' ) . '</p>';
	}
}

add_action( 'save_post', function ( $post_id, $post ) {
	if ( ! isset( $_POST['qpc_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['qpc_meta_nonce'] ), 'qpc_save_meta' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( qpc_meta_fields()[ $post->post_type ] ?? array() as $key => $f ) {
		if ( 'readonly' === $f['type'] || ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw = wp_unslash( $_POST[ $key ] );
		switch ( $f['type'] ) {
			case 'url':
				$value = esc_url_raw( trim( $raw ) );
				break;
			case 'date':
				$value = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) ? $raw : '';
				break;
			default:
				$value = sanitize_text_field( $raw );
		}
		if ( 'qpc_spotify' === $key ) {
			$value = qpc_spotify_id( $value );
		}
		update_post_meta( $post_id, $key, $value );
	}
	qpc_flush_data_cache();
}, 10, 2 );

/** Accept a full Spotify link or a bare ID; store the ID. */
function qpc_spotify_id( $value ) {
	if ( preg_match( '~episode/([A-Za-z0-9]+)~', $value, $m ) ) {
		return $m[1];
	}
	return preg_replace( '/[^A-Za-z0-9]/', '', $value );
}
