<?php
/**
 * Person template.
 *
 * @var array  $display
 * @var object $person
 */

if ( false !== $display['directory_view'] ) { ?>

	<article class="<?php echo esc_attr( $display['card_classes'] ); ?>"<?php echo wp_kses_post( $display['card_attributes'] ); ?>>

		<div class="card">

			<?php if ( $display['header'] ) { ?>
			<h2 class="name">
				<?php if ( $display['link'] ) { ?><a href="<?php echo esc_url( $display['link'] ); ?>"><?php } ?>
				<?php echo esc_html( $display['name'] ); ?>
				<?php if ( $display['link'] ) { ?></a><?php } ?>
			</h2>
			<?php } ?>

			<?php if ( $display['photo'] ) { ?>
			<figure class="photo">
				<?php if ( $display['link'] ) { ?><a href="<?php echo esc_url( $display['link'] ); ?>"><?php } ?>

				<?php if ( $display['directory_view'] ) { ?>
					<img src="<?php echo esc_url( plugins_url( 'images/placeholder.png', dirname( __FILE__ ) ) ); ?>"
						 data-photo="<?php echo esc_url( $display['photo'] ); ?>"
						 alt="<?php echo esc_html( $display['name'] ); ?>" />
				<?php } else { ?>
					<img src="<?php echo esc_url( $display['photo'] ); ?>"
						 alt="<?php echo esc_html( $display['name'] ); ?>" />
				<?php } ?>

				<?php if ( $display['link'] ) { ?></a><?php } ?>
			</figure>
			<?php } ?>

			<div class="contact">
				<span class="title"><?php echo wp_kses_post( $display['title'] ); ?></span>
				<span class="email"><a href="mailto:<?php echo esc_attr( $display['email'] ); ?>">
					<?php echo esc_html( $display['email'] ); ?></a>
				</span>
				<span class="phone"><?php echo esc_html( $display['phone'] ); ?></span>
				<span class="office"><?php echo esc_html( $display['office'] ); ?></span>
				<?php if ( ! empty( $display['address'] ) ) { ?>
				<span class="address"><?php echo esc_html( $display['address'] ); ?></span>
				<?php } ?>
				<?php if ( ! empty( $display['website'] ) ) { ?>
				<span class="website">
					<a href="<?php echo esc_url( $display['website'] ); ?>">Website</a>
				</span>
				<?php } ?>
			</div>

		</div>

		<?php if ( $display['about'] ) { ?>
		<div class="about">
			<?php echo wp_kses_post( apply_filters( 'the_content', $display['about'] ) ); ?>
		</div>
		<?php } ?>

		<?php
		if ( is_admin() ) {
			include $display['directory_view_options'];
		}
		?>

	</article>

<?php } else { ?>

	<header class="article-header">

		<?php if ( $display['photo'] ) { ?>
		<figure class="photo">
			<?php if ( $display['link'] ) { ?><a href="<?php echo esc_url( $display['link'] ); ?>"><?php } ?>

			<?php if ( $display['directory_view'] ) { ?>
				<img src="<?php echo esc_url( plugins_url( 'images/placeholder.png', dirname( __FILE__ ) ) ); ?>"
					 data-photo="<?php echo esc_url( $display['photo'] ); ?>"
					 alt="<?php echo esc_html( $display['name'] ); ?>" />
			<?php } else { ?>
				<img src="<?php echo esc_url( $display['photo'] ); ?>"
					 alt="<?php echo esc_html( $display['name'] ); ?>" />
			<?php } ?>

			<?php if ( $display['link'] ) { ?></a><?php } ?>
		</figure>
		<?php } ?>

		<hgroup>
			<h1 class="article-title"><?php echo esc_html( $display['name'] ); ?></h1>
			<span class="degree">MBA</span>
			<span class="degree">BBA</span>

			<div class="contact">
				<span class="title"><?php echo wp_kses_post( $display['title'] ); ?></span>
				<span class="office"><?php echo esc_html( $display['office'] ); ?></span>
				<span class="address"><?php echo esc_html( $display['address'] ); ?></span>
				<span class="phone link">
					<a href="tel:+1-<?php echo esc_html( $display['phone'] ); ?>"><?php echo esc_html( $display['phone'] ); ?></a>
				</span>
				<span class="email link">
					<a href="mailto:<?php echo esc_attr( $display['email'] ); ?>"><?php echo esc_html( $display['email'] ); ?></a>
				</span>
				<?php if ( ! empty( $display['website'] ) ) { ?>
				<span class="website link">
					<a href="<?php echo esc_url( $display['website'] ); ?>"><?php echo esc_url( $display['website'] ); ?></a>
				</span>
				<?php } ?>
			</div>
		</hgroup>

	</header>

	<?php
	if ( $display['about'] ) {
		?>
		<div class="article-body row side-right guttered">
			<div class="column one">
			<?php echo wp_kses_post( wpautop( $display['about'] ) ); ?>
			</div>
		</div>
		<?php
	}
}
