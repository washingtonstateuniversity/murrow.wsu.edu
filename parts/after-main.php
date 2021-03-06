<?php
$murrow_footer_menu_args = array(
	'theme_location' => 'site-footer-menu',
	'menu' => 'site-footer-menu',
	'container' => false,
	'menu_class' => null,
	'menu_id' => null,
	'items_wrap' => '<ul>%3$s</ul>',
	'depth' => 3,
);
?>
<footer class="site-footer single">
	<div class="general-contact">
		<?php
		if ( is_active_sidebar( 'murrow-footer-contact' ) ) {
			dynamic_sidebar( 'murrow-footer-contact' );
		}
		?>
	</div>
	<?php if ( has_nav_menu( 'site-footer-menu' ) ) : ?>
	<nav class="footer-nav">
		<?php wp_nav_menu( $murrow_footer_menu_args ); ?>
	</nav>
	<?php endif; ?>
	<div class="social-wrap">
		<header>
			Social media
		</header>
		<ul class="social-channels">
			<?php
			foreach ( spine_social_options() as $socialite => $social_url ) {
				?>
				<li>
					<a class="social-<?php echo esc_attr( $socialite ); ?>" href="<?php echo esc_url( $social_url ); ?>"><?php echo esc_html( $socialite ); ?></a>
				</li><?php
			}
			?>
		</ul>
		<div class="wsu-signature">
			<a href="https://wsu.edu"><img alt="Washington State University logo" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/images/wsu-logo-white.svg' ); ?>"></a>
		</div>
	</div>
	<div class="wsu-signature-mobile">
		<a href="https://wsu.edu"><img alt="Washington State University logo" src="https://medicine.wsu.edu/wp-content/themes/wsu-medicine/images/wsu-logo-wht.svg"></a>
	</div>
	<div class="footer-bottom-wrap">
		<address class="footer-contact-info">
			<span class="dept-name address-item"><?php echo esc_attr( spine_get_option( 'contact_department' ) ); ?></span>
			<span class="street-address address-item"><?php
				echo esc_attr( spine_get_option( 'contact_streetAddress' ) ) . ', ';
				echo esc_attr( spine_get_option( 'contact_addressLocality' ) ) . ' ';
				echo esc_attr( spine_get_option( 'contact_postalCode' ) );
			?></span>
		</address>
		<nav class="global-links">
			<ul>
				<li>
					<a href="https://my.wsu.edu">myWSU</a>
				</li>
				<li>
					<a href="https://accesscenter.wsu.edu/">Accessibility</a>
				</li>
				<li>
					<a href="https://policies.wsu.edu/">Policies</a>
				</li>
				<li>
					<a href="https://ucomm.wsu.edu/wsu-copyright-policy/">©</a>
				</li>
			</ul>
		</nav>
	</div>
</footer>
