<?php

namespace WSU\Murrow\Content_Syndicate;

add_filter( 'wsuwp_content_syndicate_json_output', 'WSU\Murrow\Content_Syndicate\wsuwp_json_output', 10, 3 );

function wsuwp_json_output( $content, $data, $atts ) {
	// Provide a default output for when no `output` attribute is included.
	if ( 'json' === $atts['output'] ) {
		ob_start();
		?>
		<div class="content-syndicate-wrapper">
			<?php
			$offset_x = 0;
			foreach ( $data as $content ) {
				if ( $offset_x < absint( $atts['offset'] ) ) {
					$offset_x++;
					continue;
				}

				?>
				<article class="content-syndicate-item">
					<?php if ( ! empty( $content->thumbnail ) ) : ?>
					<figure class="content-item-image">
						<a href="<?php echo esc_url( $content->link ); ?>"><img src="<?php echo esc_url( $content->thumbnail ); ?>" alt="<?php echo esc_attr( $content->featured_media->alt_text ); ?>"></a>
						<?php

						// If a caption is not manually assigned, then WordPress will auto-create a caption that we should not use.
						if ( false === strpos( $content->featured_media->caption->rendered, $content->featured_media->source_url ) ) :?>
						<figcaption class="caption" itemprop="description">
							<span class="caption-text"><?php echo wp_kses_post( $content->featured_media->caption->rendered ); ?></span>
							<span class="credit" itemprop="copyrightHolder">
								<?php // @todo <span class="visually-hidden">Credit</span> Bob Hubner, WSU Photo Services ?>
							</span>
						</figcaption>
						<?php endif; ?>
					</figure>
					<?php endif; ?>
					<header class="content-item-title"><?php echo esc_html( $content->title ); ?></header>
					<span class="content-item-byline">
						<span class="content-item-byline-date"><?php echo esc_html( date( $atts['date_format'], strtotime( $content->date ) ) ); ?></span>
						<span class="content-item-byline-author"><?php echo esc_html( $content->author_name ); ?></span>
					</span>
					<span class="content-item-excerpt">
						<?php echo wp_kses_post( $content->sub_headline ); ?>
					</span>
					<span class="content-item-cta">
						<a href="<?php echo esc_url( $content->link ); ?>">Read more</a>
					</span>
					<span class="content-item-categories">
						<?php // @todo <a href="#">category</a>, <a href="#">research</a> ?>
					</span>
				</article>
				<?php
			}
			?>
		</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
	}

	return $content;
}
