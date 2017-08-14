<footer class="site-footer single">
	<div class="general-contact">
		<header>
			Contact us
		</header>
		<p class="contact-section">
			<span class="contact-section-head">Student services:</span>
			<span class="contact-section-info">(509) 335-7333<br>
							<a href="mailto:communication@wsu.edu">communication@wsu.edu</a></span>
		</p>
		<p class="contact-section">
			<span class="contact-section-head">Graduate programs:</span>
			<span class="contact-section-info">(509) 335-5608<br>
							<a href="mailto:christine.curtis@wsu.edu">christine.curtis@wsu.edu</a></span>
		</p>
	</div>
	<nav class="footer-nav">
		<ul>
			<li>
				<a href="#">Degrees and programs</a>
			</li>
			<li>
				<a href="#">Murrow Legacy</a>
			</li>
			<li>
				<a href="#">Another link</a>
			</li>
			<li>
				<a href="#">Support the Murrow College</a>
			</li>
			<li>
				<a href="#">Final Link</a>
			</li>
		</ul>
	</nav>
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
	<div class="footer-bottom-wrap">
		<address class="footer-contact-info">
			<span class="dept-name address-item">Edward R. Murrow College of Communication</span><span class="street-address address-item">Washington State University, PO Box 642520, Pullman, WA 99164-2520</span>
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