<?php
/**
 * Settings → Church Site: the two external feeds the site reads.
 */

defined( 'ABSPATH' ) || exit;

function qpc_setting_defaults() {
	return array(
		// iCloud public calendar (the same one the old site linked to).
		'calendar_ics_url' => 'https://p69-caldav.icloud.com/published/2/MTM0OTU4Njg0ODEzNDk1OAiKAMI78sHP0UEgYBo74ceckrXowy2-fiJvsH_5i6YL8bt9A16z_rVJrL1OG3ip5Zc7DI0b1b_ukcWJ5iMtMsg',
		// Podcast feed (Spotify for Creators / Anchor). New episodes are added as sermons automatically.
		'sermon_rss_url'   => 'https://anchor.fm/s/107ca96dc/podcast/rss',
		'default_preacher' => 'Pastor Robert McMullan',
	);
}

function qpc_setting( $key ) {
	$opts     = get_option( 'qpc_settings', array() );
	$defaults = qpc_setting_defaults();
	return isset( $opts[ $key ] ) && '' !== $opts[ $key ] ? $opts[ $key ] : ( $defaults[ $key ] ?? '' );
}

add_action( 'admin_menu', function () {
	add_options_page( __( 'Church Site', 'qpc' ), __( 'Church Site', 'qpc' ), 'manage_options', 'qpc-settings', 'qpc_render_settings_page' );
} );

add_action( 'admin_init', function () {
	register_setting( 'qpc_settings', 'qpc_settings', array(
		'type'              => 'array',
		'sanitize_callback' => function ( $in ) {
			$out = array();
			$out['calendar_ics_url'] = esc_url_raw( trim( str_replace( 'webcal://', 'https://', $in['calendar_ics_url'] ?? '' ) ) );
			$out['sermon_rss_url']   = esc_url_raw( trim( $in['sermon_rss_url'] ?? '' ) );
			$out['default_preacher'] = sanitize_text_field( $in['default_preacher'] ?? '' );
			delete_transient( 'qpc_calendar_ics' );
			return $out;
		},
	) );
} );

function qpc_render_settings_page() {
	$fields = array(
		'calendar_ics_url' => array( __( 'Church calendar feed', 'qpc' ), __( 'The public iCloud (or Google) calendar link. "webcal://" links are fine. The Calendar page refreshes from it every 30 minutes.', 'qpc' ) ),
		'sermon_rss_url'   => array( __( 'Podcast RSS feed', 'qpc' ), __( 'Checked twice a day. New episodes are added under Sermons automatically, in "New Episodes" until you give them a series.', 'qpc' ) ),
		'default_preacher' => array( __( 'Default preacher', 'qpc' ), __( 'Used for sermons added automatically from the podcast.', 'qpc' ) ),
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Church Site Settings', 'qpc' ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'qpc_settings' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( $fields as $key => list( $label, $help ) ) : ?>
					<tr>
						<th scope="row"><label for="qpc_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
						<td>
							<input type="text" class="large-text" id="qpc_<?php echo esc_attr( $key ); ?>" name="qpc_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( qpc_setting( $key ) ); ?>">
							<p class="description"><?php echo esc_html( $help ); ?></p>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>
		<hr>
		<h2><?php esc_html_e( 'Check for new sermons now', 'qpc' ); ?></h2>
		<p><?php esc_html_e( 'Normally this happens automatically. Use this if an episode was just published.', 'qpc' ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="qpc_check_feed">
			<?php wp_nonce_field( 'qpc_check_feed' ); ?>
			<?php submit_button( __( 'Check podcast feed', 'qpc' ), 'secondary', 'submit', false ); ?>
		</form>
		<?php if ( isset( $_GET['qpc_added'] ) ) : ?>
			<div class="notice notice-success inline"><p>
				<?php
				/* translators: %d number of sermons */
				printf( esc_html( _n( '%d new sermon added.', '%d new sermons added.', (int) $_GET['qpc_added'], 'qpc' ) ), (int) $_GET['qpc_added'] );
				?>
			</p></div>
		<?php endif; ?>
	</div>
	<?php
}
