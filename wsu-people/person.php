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
			<?php
			if ( $display['degrees'] ) {
				foreach ( $display['degrees'] as $degree ) {
				?><span class="degree"><?php echo esc_html( $degree ); ?></span> <?php
				}
			}
			?>

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

	if ( function_exists( 'wsuwp_uc_get_object_projects' ) ) {
		$projects = wsuwp_uc_get_object_projects( get_the_ID() );
	} else {
		$projects = array();
	}

	if ( 0 < count( $projects ) ) {
	?>
	<div class="wsuwp-uc-projects row single padded-ends">
		<div class="column one">
			<div class="content-syndicate-wrapper">
			<?php foreach ( $projects as $project ) { ?>
				<article class="content-syndicate-item">
					<?php if ( has_post_thumbnail( $project['id'] ) ) { ?>
					<figure class="content-item-image">
						<a href="<?php echo esc_url( $project['url'] ); ?>">
							<?php echo get_the_post_thumbnail( $project['id'], 'spine-small_size' ); ?>
						</a>
						<?php
						$thumbnail_id = get_post_thumbnail_id( $project['id'] );
						$thumbnail_caption = get_post( $thumbnail_id )->post_excerpt;
						$thumbnail_credit = get_post_meta( $thumbnail_id, '_photo_credit', true );
						if ( $thumbnail_caption || $thumbnail_credit ) {
						?>
						<figcaption class="caption" itemprop="description">
							<?php if ( $thumbnail_caption ) { ?>
							<span class="caption-text"><?php echo wp_kses_post( $thumbnail_caption ); ?></span>
							<?php } ?>
							<?php if ( $thumbnail_credit ) { ?>
							<span class="credit" itemprop="copyrightHolder">
								<span class="visually-hidden">Credit</span> <?php echo wp_kses_post( $thumbnail_credit ); ?>
							</span>
							<?php } ?>
						</figcaption>
						<?php } ?>
					</figure>
					<?php } ?>
					<header class="content-item-title"><?php echo esc_html( $project['name'] ); ?></header>
					<span class="content-item-excerpt">
						<?php
						$sub_headline = get_post_meta( $project['id'], '_murrow_subhead', true );
						if ( $sub_headline ) {
							echo wp_kses_post( $sub_headline );
						}
						?>
					</span>
					<span class="content-item-cta">
						<a href="<?php echo esc_url( $project['url'] ); ?>">Read more</a>
					</span>
					<span class="content-item-categories">
						<?php
						$categories = get_the_category( $project['id'] );
						$category_output = array();
						foreach ( $categories as $category ) {
							$category_output[] = '<a href="' . esc_url( $category->url ) . '">' . esc_html( $category->name ) . '</a>';
						}

						$category_output = implode( ', ', $category_output );
						echo wp_kses_post( $category_output );
						?>
					</span>
				</article>
			<?php } ?>
			</div>
		</div>
	</div>
	<?php
	}
}
