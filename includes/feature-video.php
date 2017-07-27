<?php

namespace WSU\Murrow\Feature_Video;

add_action( 'add_meta_boxes', 'WSU\Murrow\Feature_Video\add_meta_boxes' );
add_action( 'save_post', 'WSU\Murrow\Feature_Video\save_post', 10, 2 );

/**
 * Add meta box used to control featured videos.
 *
 * @since 0.0.3
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
 * @since 0.0.3
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
 * @since 0.0.3
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
