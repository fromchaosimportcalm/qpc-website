<?php
/**
 * Plugin Name:       QPC Core
 * Description:       Sermons, notices, the church calendar and the homepage verse for Queanbeyan Presbyterian Church. Keep this plugin active — the site's content depends on it, whichever theme is used.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            Queanbeyan Presbyterian Church
 * License:           GPL-2.0-or-later
 * Text Domain:       qpc
 */

defined( 'ABSPATH' ) || exit;

define( 'QPC_CORE_VERSION', '1.0.0' );
define( 'QPC_CORE_FILE', __FILE__ );
define( 'QPC_CORE_DIR', __DIR__ );

require_once __DIR__ . '/inc/settings.php';
require_once __DIR__ . '/inc/post-types.php';
require_once __DIR__ . '/inc/meta-boxes.php';
require_once __DIR__ . '/inc/rest.php';
require_once __DIR__ . '/inc/sermon-feed.php';
require_once __DIR__ . '/inc/blocks.php';
require_once __DIR__ . '/inc/admin-help.php';
require_once __DIR__ . '/inc/redirects.php';
require_once __DIR__ . '/inc/importer.php';

register_activation_hook( __FILE__, function () {
	qpc_register_content_types();
	flush_rewrite_rules();
	qpc_schedule_sermon_feed();
} );

register_deactivation_hook( __FILE__, function () {
	wp_clear_scheduled_hook( 'qpc_sermon_feed_check' );
	flush_rewrite_rules();
} );
