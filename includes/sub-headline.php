<?php

namespace WSU\Murrow\Sub_Headline;

add_action( 'rest_api_init', 'WSU\Murrow\Sub_Headline\register_api_field' );
add_filter( 'wsu_content_syndicate_host_data', 'WSU\Murrow\Sub_Headline\modify_content_syndicate_data', 10, 3 );
add_action( 'edit_form_after_title', 'WSU\Murrow\Sub_Headline\subhead_input' );
add_action( 'save_post', 'WSU\Murrow\Sub_Headline\save_post', 10, 2 );

/**
 * Adds an input for capturing a subhead below the post title field.
 *
 * @param \WP_Post $post Post object.
 */
function subhead_input( $post ) {
	if ( 'post' !== $post->post_type ) {
		return;
	}

	wp_nonce_field( 'save-murrow-subhead', '_murrow_subhead_nonce' );

	$subhead = get_post_meta( $post->ID, '_murrow_subhead', true );
	$subhead_editor_settings = array(
		'media_buttons' => false,
		'textarea_rows' => 2,
		'teeny' => true,
		'quicktags' => false,
	);

	?><h2>Subhead</h2><?php

	wp_editor( $subhead, '_murrow_subhead', $subhead_editor_settings );
}

/**
 * Saves additional data associated with a post.
 *
 * @param int     $post_id
 * @param \WP_Post $post
 */
function save_post( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( 'auto-draft' === $post->post_status ) {
		return;
	}

	if ( ! isset( $_POST['_murrow_subhead_nonce'] ) || ! wp_verify_nonce( $_POST['_murrow_subhead_nonce'], 'save-murrow-subhead' ) ) {
		return;
	}

	if ( isset( $_POST['_murrow_subhead'] ) ) {
		update_post_meta( $post_id, '_murrow_subhead', wp_kses_post( $_POST['_murrow_subhead'] ) );
	} else {
		delete_post_meta( $post_id, '_murrow_subhead' );
	}
}

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
