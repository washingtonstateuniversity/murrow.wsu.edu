<?php

namespace WSU\Murrow\Sub_Headline;

add_action( 'rest_api_init', 'WSU\Murrow\Sub_Headline\register_api_field' );
add_filter( 'wsu_content_syndicate_host_data', 'WSU\Murrow\Sub_Headline\modify_content_syndicate_data', 10, 3 );

/**
 * Register an additional REST API field for sub headlines.
 *
 * @since 0.0.2
 */
function register_api_field() {
	$args = array(
		'get_callback' => 'WSU\Murrow\Sub_Headline\get_api_data',
		'update_callback' => 'wp_kses_post',
		'schema' => null,
	);
	register_rest_field( 'post', 'sub_headline', $args );
}

/**
 * Return data for the sub headlines REST API field.
 *
 * @since 0.0.2
 *
 * @param array            $object
 * @param string           $field
 * @param \WP_REST_Request $request
 *
 * @return mixed|string
 */
function get_api_data( $object, $field, $request ) {
	if ( 'sub_headline' !== $field ) {
		return '';
	}

	$subhead = get_post_meta( $object['id'], '_murrow_subhead', true );

	if ( empty( $subhead ) ) {
		return '';
	}

	return wp_kses_post( $subhead );
}

/**
 * Attach sub headline data to a content syndicate request.
 *
 * @since 0.0.2
 *
 * @param object $subset Data attached to this result.
 * @param object $post   Data for an individual post retrieved via `wp-json/posts` from a remote host.
 * @param array  $atts   Array of attributes passed with wp_json shortcode.
 *
 * @return object Modified result data.
 */
function modify_content_syndicate_data( $subset, $post, $atts ) {
	if ( get_site()->domain === $atts['host'] ) {
		$subhead = get_post_meta( $subset->ID, '_murrow_subhead', true );
		if ( empty( $subhead ) ) {
			$subset->sub_headline = '';
		} else {
			$subset->sub_headline = wp_kses_post( $subhead );
		}
	} elseif ( isset( $post->sub_headline ) && ! empty( $post->sub_headline ) ) {
		$subset->sub_headline = $post->sub_headline;
	} else {
		$subset->sub_headline = '';
	}

	return $subset;
}
