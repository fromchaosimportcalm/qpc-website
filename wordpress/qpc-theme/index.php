<?php
/**
 * Fallback template (posts, archives, search, 404). Uses the standard page
 * header band so anything without its own template still looks on-brand.
 */
get_header();
?>
<header class="page-header">
	<div class="page-header-inner page-header-inner--single">
		<div>
			<h1 class="page-title">
				<?php
				if ( is_404() ) {
					esc_html_e( 'Page not found', 'qpc' );
				} elseif ( is_search() ) {
					/* translators: %s: search term */
					printf( esc_html__( 'Search: %s', 'qpc' ), esc_html( get_search_query() ) );
				} elseif ( is_singular() ) {
					single_post_title();
				} else {
					the_archive_title();
				}
				?>
			</h1>
		</div>
	</div>
</header>

<section class="content-section">
	<div class="content-inner">
		<?php if ( is_404() ) : ?>
			<p><?php esc_html_e( 'Sorry, that page has moved or no longer exists.', 'qpc' ); ?></p>
			<p><a class="btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'qpc' ); ?></a></p>
		<?php elseif ( is_singular() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<div class="entry-content"><?php the_content(); ?></div>
			<?php endwhile; ?>
		<?php elseif ( have_posts() ) : ?>
			<ul class="post-list">
				<?php while ( have_posts() ) : the_post(); ?>
					<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
				<?php endwhile; ?>
			</ul>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing found.', 'qpc' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
