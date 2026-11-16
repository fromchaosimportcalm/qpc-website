</main>

<footer class="site-footer">
	<div class="footer-inner">
		<div class="footer-brand">
			<img src="<?php echo esc_url( qpc_logo_url() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="footer-logo-img">
			<span class="sub"><?php echo esc_html( qpc_detail( 'tagline' ) ); ?></span>
			<p><?php echo esc_html( qpc_detail( 'blurb' ) ); ?></p>
		</div>
		<div class="footer-links">
			<h4><?php esc_html_e( 'Pages', 'qpc' ); ?></h4>
			<?php
			wp_nav_menu( array(
				'theme_location' => 'footer',
				'container'      => false,
				'depth'          => 1,
				'fallback_cb'    => false,
			) );
			?>
		</div>
		<div class="footer-links">
			<h4><?php esc_html_e( 'Contact', 'qpc' ); ?></h4>
			<ul>
				<li><?php echo esc_html( qpc_detail( 'address' ) ); ?></li>
				<li><a href="<?php echo esc_url( qpc_tel() ); ?>"><?php echo esc_html( qpc_detail( 'phone' ) ); ?></a></li>
				<li><?php echo esc_html( qpc_detail( 'service_times' ) ); ?></li>
				<li><a href="<?php echo esc_url( qpc_mailto() ); ?>"><?php esc_html_e( 'Send a Message', 'qpc' ); ?></a></li>
				<?php if ( qpc_detail( 'facebook_url' ) ) : ?>
					<li><a href="<?php echo esc_url( qpc_detail( 'facebook_url' ) ); ?>" target="_blank" rel="noopener">Facebook</a></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
	<div class="footer-bottom">
		<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?> · <?php esc_html_e( 'All rights reserved', 'qpc' ); ?></p>
		<div class="footer-social">
			<?php if ( qpc_detail( 'facebook_url' ) ) : ?>
				<a href="<?php echo esc_url( qpc_detail( 'facebook_url' ) ); ?>" class="social-link" target="_blank" rel="noopener" aria-label="Facebook">f</a>
			<?php endif; ?>
			<a href="<?php echo esc_url( qpc_mailto() ); ?>" class="social-link" aria-label="<?php esc_attr_e( 'Email', 'qpc' ); ?>">✉</a>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
