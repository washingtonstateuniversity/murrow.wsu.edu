<?php

namespace WSU\Murrow\Featured_Stories;

add_action( 'add_meta_boxes', 'WSU\Murrow\Featured_Stories\add_meta_boxes', 10 );
add_action( 'save_post', 'WSU\Murrow\Featured_Stories\save_post', 10, 2 );

/**
 * Adds meta boxes used to manage featured stories.
 *
 * @param string $post_type
 */
function add_meta_boxes( $post_type ) {
	if ( 'post' !== $post_type ) {
		return;
	}

	add_meta_box( 'murrow_featured_meta', 'Featured Status', 'WSU\Murrow\Featured_Stories\display_meta_box', 'post', 'side', 'high' );
}

/**
 * Displays the meta box used to capture a post's featured status.
 *
 * @param \WP_Post $post
 */
function display_meta_box( $post ) {
	$featured = get_post_meta( $post->ID, '_murrow_featured', true );

	if ( 'yes' !== $featured ) {
		$featured = 'no';
	}

	?>
	<label for="featured-status-select">Featured Status:</label>
	<select id="featured-status-select" name="featured_status_select">
		<option value="no" <?php selected( 'no', $featured ); ?>>No</option>
		<option value="yes" <?php selected( 'yes', $featured ); ?>>Yes</option>
	</select>
	<?php
}

/**
 * Saves the featured status of a post.
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

	if ( ! isset( $_POST['featured_status_select'] ) ) { // @codingStandardsIgnoreLine
		return;
	}

	if ( 'yes' === $_POST['featured_status_select'] ) { // @codingStandardsIgnoreLine
		update_post_meta( $post_id, '_murrow_featured', 'yes' );
	} else {
		delete_post_meta( $post_id, '_murrow_featured' );
	}
}
