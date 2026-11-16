<?php
/**
 * QPC blocks (Sermon Library, Church Calendar, Verse of the Day, Notices).
 * Each lives in blocks/<name>/ with block.json + render.php (+ view.js for the
 * interactive ones). No build step: the editor script is plain JavaScript.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	foreach ( glob( QPC_CORE_DIR . '/blocks/*/block.json' ) as $file ) {
		register_block_type( dirname( $file ) );
	}

	$ver = QPC_CORE_VERSION;
	wp_register_script( 'qpc-sermon-library', plugins_url( 'blocks/sermon-library/view.js', QPC_CORE_FILE ), array(), $ver, array( 'in_footer' => true ) );
	wp_register_script( 'qpc-calendar', plugins_url( 'blocks/calendar/view.js', QPC_CORE_FILE ), array(), $ver, array( 'in_footer' => true ) );
} );

/** Called from render.php: load the block's script with the URL of its data. */
function qpc_enqueue_block_view( $block ) {
	switch ( $block ) {
		case 'sermon-library':
			wp_localize_script( 'qpc-sermon-library', 'qpcSermons', array( 'url' => rest_url( 'qpc/v1/sermons' ) ) );
			break;
		case 'calendar':
			$ics = qpc_setting( 'calendar_ics_url' );
			wp_localize_script( 'qpc-calendar', 'qpcCalendar', array(
				'url'        => rest_url( 'qpc/v1/calendar' ),
				'webcal'     => preg_replace( '~^https?://~', 'webcal://', $ics ),
				'configured' => (bool) $ics,
			) );
			break;
	}
	wp_enqueue_script( 'qpc-' . $block );
}

/** Editor: show each block as a labelled box explaining where its content is managed. */
add_action( 'enqueue_block_editor_assets', function () {
	wp_enqueue_script( 'qpc-blocks-editor', plugins_url( 'assets/blocks-editor.js', QPC_CORE_FILE ), array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor' ), QPC_CORE_VERSION, true );
	wp_localize_script( 'qpc-blocks-editor', 'qpcAdminLinks', array(
		'qpc/sermon-library'   => admin_url( 'edit.php?post_type=qpc_sermon' ),
		'qpc/calendar'         => admin_url( 'options-general.php?page=qpc-settings' ),
		'qpc/notices'          => admin_url( 'edit.php?post_type=qpc_notice' ),
		'qpc/verse-of-the-day' => '',
	) );
} );
