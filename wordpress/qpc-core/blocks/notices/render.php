<?php
/**
 * Notices: every published Notice whose dates include today, newest first.
 * Same look as the old homepage event banners (.gala-banner).
 */
defined( 'ABSPATH' ) || exit;

$qpc_today   = current_time( 'Y-m-d' );
$qpc_notices = get_posts( array(
	'post_type'      => 'qpc_notice',
	'post_status'    => 'publish',
	'posts_per_page' => 5,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'meta_query'     => array(
		'relation' => 'AND',
		array(
			'relation' => 'OR',
			array( 'key' => 'qpc_show_from', 'compare' => 'NOT EXISTS' ),
			array( 'key' => 'qpc_show_from', 'value' => '' ),
			array( 'key' => 'qpc_show_from', 'value' => $qpc_today, 'compare' => '<=', 'type' => 'DATE' ),
		),
		array(
			'relation' => 'OR',
			array( 'key' => 'qpc_show_until', 'compare' => 'NOT EXISTS' ),
			array( 'key' => 'qpc_show_until', 'value' => '' ),
			array( 'key' => 'qpc_show_until', 'value' => $qpc_today, 'compare' => '>=', 'type' => 'DATE' ),
		),
	),
) );

foreach ( $qpc_notices as $qpc_notice ) :
	$qpc_label = get_post_meta( $qpc_notice->ID, 'qpc_label', true );
	$qpc_link  = get_post_meta( $qpc_notice->ID, 'qpc_link_url', true );
	$qpc_btn   = get_post_meta( $qpc_notice->ID, 'qpc_link_text', true ) ?: __( 'Find out more', 'qpc' );
	$qpc_image = get_the_post_thumbnail( $qpc_notice, 'large', array( 'alt' => get_the_title( $qpc_notice ) ) );
	?>
	<section class="gala-banner qpc-notice<?php echo $qpc_image ? '' : ' qpc-notice--no-image'; ?>">
		<div class="gala-banner-inner">
			<?php if ( $qpc_image ) : ?>
				<?php if ( $qpc_link ) : ?>
					<a href="<?php echo esc_url( $qpc_link ); ?>" class="gala-banner-graphic"><?php echo $qpc_image; // phpcs:ignore ?></a>
				<?php else : ?>
					<div class="gala-banner-graphic"><?php echo $qpc_image; // phpcs:ignore ?></div>
				<?php endif; ?>
			<?php endif; ?>
			<div>
				<?php if ( $qpc_label ) : ?>
					<div class="section-label"><?php echo esc_html( $qpc_label ); ?></div>
				<?php endif; ?>
				<h2 class="gala-banner-title"><?php echo esc_html( get_the_title( $qpc_notice ) ); ?></h2>
				<div class="gala-banner-text"><?php echo apply_filters( 'the_content', $qpc_notice->post_content ); // phpcs:ignore ?></div>
				<?php if ( $qpc_link ) : ?>
					<a href="<?php echo esc_url( $qpc_link ); ?>" class="btn-primary"><?php echo esc_html( $qpc_btn ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
endforeach;
