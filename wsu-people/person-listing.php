<?php
/**
 * Person template for directory page views.
 *
 * @var array  $display
 * @var object $person
 */
?>
<article class="<?php echo esc_attr( $display['card_classes'] ); ?>"<?php echo wp_kses_post( $display['card_attributes'] ); ?>>

	<div class="card">

		<h2 class="name">
			<?php if ( $display['link'] ) { ?><a href="<?php echo esc_url( $display['link'] ); ?>"><?php } ?>
			<?php echo esc_html( $display['name'] ); ?>
			<?php if ( $display['link'] ) { ?></a><?php } ?>
		</h2>

		<?php if ( $display['photo'] ) { ?>
		<figure class="photo">
			<?php if ( $display['link'] ) { ?><a href="<?php echo esc_url( $display['link'] ); ?>"><?php } ?>
			<img src=""
				 data-photo="<?php echo esc_url( $display['photo']->thumbnail ); ?>"
				 alt="<?php echo esc_html( $display['name'] ); ?>" />
			<?php if ( $display['link'] ) { ?></a><?php } ?>
		</figure>
		<?php } ?>

		<div class="contact">

			<?php
			if ( $display['titles'] ) {
				foreach ( $display['titles'] as $title ) {
				?><span class="title"><?php echo esc_html( $title ); ?></span><?php
				}
			}
			?>

			<?php if ( ! empty( $display['phone'] ) ) { ?>
			<span class="phone">
				<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12">
					<use href="#person-card-icon_phone" />
				</svg>
				<a href="tel:+1-<?php echo esc_html( $display['phone'] ); ?>"><?php echo esc_html( $display['phone'] ); ?></a>
			</span>
			<?php } ?>

			<?php if ( ! empty( $display['email'] ) ) { ?>
			<span class="email">
				<svg xmlns="http://www.w3.org/2000/svg" width="14" height="11">
					<use href="#person-card-icon_email" />
				</svg>
				<a href="mailto:<?php echo esc_attr( $display['email'] ); ?>"><?php echo esc_html( $display['email'] ); ?></a>
			</span>
			<?php } ?>

			<?php if ( ! empty( $display['website'] ) ) { ?>
			<span class="website">
				<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12">
					<use href="#person-card-icon_link" />
				</svg>
				<a href="<?php echo esc_url( $display['website'] ); ?>"><?php echo esc_url( $display['website'] ); ?></a>
			</span>
			<?php } ?>

			<?php if ( $display['link'] ) { ?>
			<span class="profile-link">
				<a href="<?php echo esc_url( $display['link'] ); ?>">View full profile</a>
			<?php } ?>

		</div>

	</div>

	<?php
	if ( is_admin() ) {
		include $display['directory_view_options'];
	}
	?>

</article>
