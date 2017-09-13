<?php

namespace WSU\Murrow\Content_Syndicate;

add_action( 'rest_api_init', 'WSU\Murrow\Content_Syndicate\register_api_fields' );
add_filter( 'wsu_content_syndicate_host_data', 'WSU\Murrow\Content_Syndicate\manage_subset_data', 10, 2 );
add_filter( 'wsuwp_content_syndicate_json_output', 'WSU\Murrow\Content_Syndicate\wsuwp_json_output', 10, 3 );
add_filter( 'wsuwp_content_syndicate_default_atts', 'WSU\Murrow\Content_Syndicate\append_default_attributes' );
add_filter( 'wsuwp_content_syndicate_taxonomy_filters', 'WSU\Murrow\Content_Syndicate\modify_rest_url', 10, 2 );
add_action( 'rest_query_vars', 'WSU\Murrow\Content_Syndicate\rest_query_vars' );
add_filter( 'query_vars', 'WSU\Murrow\Content_Syndicate\query_vars' );
add_filter( 'rest_post_query', 'WSU\Murrow\Content_Syndicate\rest_post_query', 12 );
add_filter( 'wsuwp_people_item_html', 'WSU\Murrow\Content_Syndicate\people_html', 10, 2 );
add_action( 'wp_footer', 'WSU\Murrow\Content_Syndicate\people_icons' );

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
 * Provide fallback URLs if thumbnail sizes have not been generated
 * for a post pulled in with content syndicate.
 *
 * @param \stdClass $content
 *
 * @return string
 */
function get_image_url( $content ) {
	// If no embedded featured media exists, use the full thumbnail.
	if ( ! isset( $content->featured_media )
		|| ! isset( $content->featured_media->media_details )
		|| ! isset( $content->featured_media->media_details->sizes ) ) {
		return $content->thumbnail;
	}

	$sizes = $content->featured_media->media_details->sizes;

	if ( isset( $sizes->{'spine-small_size'} ) ) {
		return $sizes->{'spine-small_size'}->source_url;
	}

	if ( isset( $sizes->{'medium_large'} ) ) {
		return $sizes->{'medium_large'}->source_url;
	}

	if ( isset( $sizes->{'large'} ) ) {
		return $sizes->{'large'}->source_url;
	}

	return $content->thumbnail;
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
						<?php
						$image_url = get_image_url( $content );
						?>
					<figure class="content-item-image">
						<a href="<?php echo esc_url( $content->link ); ?>"><img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $content->featured_media->alt_text ); ?>"></a>
						<?php

						// If a caption is not manually assigned, then WordPress will auto-create a caption that we should not use.
						if ( false === strpos( $content->featured_media->caption->rendered, $content->featured_media->source_url ) ) : ?>
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

/**
 * Add support for a "featured" flag.
 *
 * @param array $atts WSUWP Content Syndicate shortcode attributes.
 *
 * @return array Modified list of default shortcode attributes.
 */
function append_default_attributes( $atts ) {
	$atts['featured'] = '';

	return $atts;
}

/**
 * Include the featured flag as part of the REST API request.
 *
 * @param string $request_url
 * @param array $atts
 *
 * @return string
 */
function modify_rest_url( $request_url, $atts ) {
	if ( ! in_array( $atts['featured'], array( 'yes', 'no' ), true ) ) {
		return $request_url;
	}

	$request_url = add_query_arg( array(
		'filter[featured]' => $atts['featured'],
	), $request_url );

	return $request_url;
}

/**
 * Make the `meta_query` argument available to the REST API request.
 *
 * @param array $vars
 *
 * @return array
 */
function rest_query_vars( $vars ) {
	array_push( $vars, 'meta_query' );

	return $vars;
}

/**
 * Filter the query vars that the plugin expects to be available through the
 * the `filter` query argument attached to REST request URLs.
 *
 * @param array $vars
 *
 * @return array
 */
function query_vars( $vars ) {
	array_push( $vars, 'featured' );

	return $vars;
}

/**
 * Build a meta query from the featured flag passed via filter parameters
 * in the REST API request.
 *
 * @param array $args
 *
 * @return array
 */
function rest_post_query( $args ) {
	if ( ! isset( $args['featured'] ) || ! in_array( $args['featured'], array( 'yes', 'no' ), true ) ) {
		return $args;
	}

	if ( 'yes' === $args['featured'] ) {
		$args['meta_query'] = array(
			array(
				'key' => '_murrow_featured',
				'value' => $args['featured'],
			),
		);
	} elseif ( 'no' === $args['featured'] ) {
		$args['meta_query'] = array(
			array(
				'key' => '_murrow_featured',
				'compare' => 'NOT EXISTS',
			),
		);
	}

	return $args;
}

/**
 * Provide a custom HTML template for use with syndicated people.
 *
 * @since 0.6.3
 *
 * @param string   $html   The HTML to output for an individual person.
 * @param stdClass $person Object representing a person received from people.wsu.edu.
 *
 * @return string The HTML to output for a person.
 */
function people_html( $html, $person ) {
	// Cast the collection as an array to account for scenarios
	// where it can sometimes come through as an object.
	$photo_collection = (array) $person->photos;
	$photo = false;

	// Get the URL of the display photo.
	if ( ! empty( $photo_collection ) ) {
		if ( ! empty( $person->display_photo ) && isset( $photo_collection[ $person->display_photo ] ) ) {
			$photo = $photo_collection[ $person->display_photo ]->thumbnail;
		} elseif ( isset( $photo_collection[0] ) ) {
			$photo = $photo_collection[0]->thumbnail;
		}
	}

	// Get the legacy profile photo URL if the person's collection is empty.
	if ( ! $photo && isset( $person->profile_photo ) ) {
		$photo = $person->profile_photo;
	}

	// Get the display title(s).
	if ( ! empty( $person->working_titles ) ) {
		if ( ! empty( $person->display_title ) ) {
			$display_titles = explode( ',', $person->display_title );
			foreach ( $display_titles as $display_title ) {
				if ( isset( $person->working_titles[ $display_title ] ) ) {
					$titles[] = $person->working_titles[ $display_title ];
				}
			}
		} else {
			$titles = $person->working_titles;
		}
	} else {
		$titles = array( $person->position_title );
	}

	// Get the email address to display.
	if ( ! empty( $person->email_alt ) ) {
		$email = $person->email_alt;
	} else {
		$email = $person->email;
	}

	// Get the phone number to display.
	if ( ! empty( $person->phone_alt ) ) {
		$phone = $person->phone_alt;
	} else {
		$phone = $person->phone;
	}

	ob_start();
	?>
	<article class="wsu-person">

		<?php if ( $photo ) { ?>
		<figure class="photo">
			<a href="<?php echo esc_url( $person->link ); ?>"><img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $person->title->rendered ); ?>" /></a>
		</figure>
		<?php } ?>

		<div class="wsu-person-info">

			<div class="name"><?php echo esc_html( $person->title->rendered ); ?></div>

			<?php foreach ( $titles as $title ) { ?>
			<div class="title"><?php echo esc_html( $title ); ?></div>
			<?php } ?>

			 <div class="phone">
				<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12">
					<use href="#person-card-icon_phone" />
				</svg>
				<a href="tel:+1-<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a>
			</div>

			<div class="email">
				<svg xmlns="http://www.w3.org/2000/svg" width="14" height="11">
					<use href="#person-card-icon_email" />
				</svg>
				<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
			</div>

			<?php if ( ! empty( $person->website ) ) { ?>
			<div class="website">
				<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12">
					<use href="#person-card-icon_link" />
				</svg>
				<a href="<?php echo esc_url( $person->website ); ?>"><?php echo esc_url( $person->website ); ?></a>
			</div>
			<?php } ?>

			<div class="profile-link">
				<a href="<?php echo esc_url( $person->link ); ?>">View full profile</a>
			</div>

		</div>

	</article>

	<?php
	$html = ob_get_contents();
	ob_end_clean();

	return $html;
}

/**
 * Provides icons for use in profile cards.
 *
 * @since 0.6.3
 */
function people_icons() {
	$post = get_post();

	if ( $post->post_content && has_shortcode( $post->post_content, 'wsuwp_people' ) ) {
		echo \WSU\Murrow\People_Directory\people_card_icons(); // @codingStandardsIgnoreLine
	}
}
