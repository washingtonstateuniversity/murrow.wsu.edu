<?php

namespace WSU\Murrow\Feature_Video;

add_action( 'add_meta_boxes', 'WSU\Murrow\Feature_Video\add_meta_boxes' );
add_action( 'save_post', 'WSU\Murrow\Feature_Video\save_post', 10, 2 );
add_action( 'rest_api_init', 'WSU\Murrow\Feature_Video\register_api_field' );
add_filter( 'wsu_content_syndicate_host_data', 'WSU\Murrow\Feature_Video\modify_content_syndicate_data', 10, 3 );

/**
 * Add meta box used to control featured videos.
 *
 * @since 0.0.2
 *
 * @param string $post_type
 */
function add_meta_boxes( $post_type ) {
	if ( 'post' !== $post_type ) {
		return;
	}

	add_meta_box( 'murrow-feature-video', 'Featured Video', 'WSU\Murrow\Feature_Video\display_feature_video_meta_box', null );
}

/**
 * Display the meta box used to capture featured video.
 *
 * @since 0.0.2
 *
 * @param \WP_Post $post
 */
function display_feature_video_meta_box( $post ) {
	$video_url = get_post_meta( $post->ID, '_murrow_feature_video', true );

	wp_nonce_field( 'save-murrow-video', '_murrow_video_nonce' );
	?>
	<label for="feature-video-url">Feature Video URL:</label>
	<input style="width:80%;" type="text" value="<?php echo esc_url( $video_url ); ?>" id="feature-video-url" name="feature_video_url" />
	<?php
}

/**
 * Attach a featured video to a post.
 *
 * @since 0.0.2
 *
 * @param int     $post_id
 * @param \WP_Post $post
 */
function save_post( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( 'post' !== $post->post_type ) {
		return;
	}

	if ( 'auto-draft' === $post->post_status ) {
		return;
	}

	if ( ! isset( $_POST['feature_video_url'] ) || ! wp_verify_nonce( $_POST['_murrow_video_nonce'], 'save-murrow-video' ) ) {
		return;
	}

	update_post_meta( $post_id, '_murrow_feature_video', esc_url_raw( $_POST['feature_video_url'] ) );
}

/**
 * Register an additional REST API field for featured video.
 *
 * @since 0.0.2
 */
function register_api_field() {
	$args = array(
		'get_callback' => 'WSU\Murrow\Feature_Video\get_api_data',
		'update_callback' => 'esc_url_raw',
		'schema' => null,
	);
	register_rest_field( 'post', 'feature_video', $args );
}

/**
 * Return data for the featured video REST API field.
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
	if ( 'feature_video' !== $field ) {
		return '';
	}

	$feature_video = get_post_meta( $object['id'], '_murrow_feature_video', true );

	if ( empty( $feature_video ) ) {
		return '';
	}

	return esc_url( $feature_video );
}

/**
 * Attach feature video data to a content syndicate request.
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
		$feature_video = get_post_meta( $subset->ID, '_murrow_feature_video', true );
		if ( empty( $feature_video ) ) {
			$subset->feature_video = '';
		} else {
			$subset->feature_video = esc_url( $feature_video );
		}
	} elseif ( isset( $post->feature_video ) && ! empty( $post->feature_video ) ) {
		$subset->feature_video = $post->feature_video;
	} else {
		$subset->feature_video = '';
	}

	return $subset;
}
