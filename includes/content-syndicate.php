<?php

namespace WSU\Murrow\Content_Syndicate;

add_action( 'rest_api_init', 'WSU\Murrow\Content_Syndicate\register_api_fields' );
add_filter( 'wsu_content_syndicate_host_data', 'WSU\Murrow\Content_Syndicate\manage_subset_data', 10, 2 );
add_filter( 'wsuwp_content_syndicate_json_output', 'WSU\Murrow\Content_Syndicate\wsuwp_json_output', 10, 3 );

/**
 * Register a syndicate_categories field in the REST API to provide specific
 * data on categories that should appear with posts pulled in content syndicate.
 *
 * @since 0.1.0
 */
function register_api_fields() {
	register_rest_field( 'post', 'syndicate_categories', array(
		'get_callback' => 'WSU\Murrow\Content_Syndicate\get_api_syndicate_categories',
	) );
}

/**
 * Return the category data required by content syndicate.
 *
 * @since 0.1.0
 *
 * @param array            $object     The current post being processed.
 * @param string           $field_name Name of the field being retrieved.
 * @param \WP_Rest_Request $request    The full current REST request.
 *
 * @return mixed Category data associated with the post.
 */
function get_api_syndicate_categories( $object, $field_name, $request ) {
	if ( 'syndicate_categories' !== $field_name ) {
		return null;
	}

	$categories = wp_get_post_categories( $object['id'] );
	$data = array();

	foreach ( $categories as $category ) {
		$term = get_term( $category );
		$data[] = array(
			'id' => $term->term_id,
			'slug' => $term->slug,
			'name' => $term->name,
			'url' => get_category_link( $term->term_id ),
		);
	}

	return $data;
}

/**
 * Ensure the subset data in content syndicate has been populated
 * with category information from the REST API.
 *
 * @since 0.1.0
 *
 * @param $subset
 * @param $post
 *
 * @return mixed
 */
function manage_subset_data( $subset, $post ) {
	if ( isset( $post->syndicate_categories ) ) {
		$subset->categories = $post->syndicate_categories;
	} else {
		$subset->categories = array();
	}

	return $subset;
}

/**
 * Provide custom output for the wsuwp_json shortcode.
 *
 * @since 0.0.5
 *
 * @param string $content
 * @param array  $data
 * @param array  $atts
 *
 * @return string
 */
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
							<?php
							//<span class="credit" itemprop="copyrightHolder">
							// @todo <span class="visually-hidden">Credit</span> Bob Hubner, WSU Photo Services
							// </span>
							?>
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
						<?php
						$category_output = array();
						foreach ( $content->categories as $category ) {
							$category_output[] = '<a href="' . esc_url( $category->url ) . '">' . esc_html( $category->name ) . '</a>';
						}

						$category_output = implode( ', ', $category_output );
						echo $category_output; // @codingStandardsIgnoreLine
						?>
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
