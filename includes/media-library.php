<?php

namespace WSU\Murrow\Media_Library;

add_filter( 'attachment_fields_to_edit', 'WSU\Murrow\Media_Library\add_credit_field', 10, 2 );
add_action( 'edit_attachment', 'WSU\Murrow\Media_Library\save_credit_field' );

/**
 * Capture a media item's photo credit.
 *
 * @since 0.3.0
 *
 * @param array    $form_fields The form fields to output for the media item.
 * @param \WP_Post $post        The media item being edited.
 *
 * @return array
 */
function add_credit_field( $form_fields, $post ) {
	$credits = get_post_meta( $post->ID, '_photo_credit', true );

	$form_fields['photo_credit'] = array(
		'value' => $credits ? wp_kses_post( $credits ) : '',
		'label' => 'Photo Credit',
		'input' => 'textarea',
	);

	return $form_fields;
}

/**
 * Save photo credit data to a media item.
 *
 * @since 0.3.0
 *
 * @param int $attachment_id
 */
function save_credit_field( $attachment_id ) {
	if ( ! isset( $_POST['attachments'][ $attachment_id ]['photo_credit'] ) ) { // @codingStandardsIgnoreLine
		return;
	}

	$credit = wp_kses_post( $_REQUEST['attachments'][ $attachment_id ]['photo_credit'] );
	update_post_meta( $attachment_id, '_photo_credit', $credit );
}
