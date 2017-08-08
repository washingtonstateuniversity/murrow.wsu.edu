<?php

namespace WSU\Murrow\People_Directory;

add_filter( 'wsuwp_people_default_rewrite_slug', 'WSU\Murrow\People_Directory\rewrite_arguments' );

/**
 * Filter the rewrite arguments passed to register_post_type by the people directory.
 *
 * @param array|bool $rewrite False by default. Array if previously filtered.
 *
 * @return array
 */
function rewrite_arguments( $rewrite ) {
	return array(
		'slug' => 'people',
		'with_front' => false,
	);
}

add_filter( 'wsuwp_search_post_data', 'WSU\Murrow\People_Directory\search_data', 10, 2 );
/**
 * Filter the data sent to Elasticsearch for a profile record.
 *
 * @since 0.3.4
 *
 * @param array    $data
 * @param \WP_Post $post
 *
 * @return array
 */
function search_data( $data, $post ) {
	$nid = get_post_meta( $post->ID, '_wsuwp_profile_ad_nid', true );
	$person = \WSUWP_People_Post_Type::get_rest_data( $nid );
	$person = \WSUWP_Person_Display::get_data( $person );

	// Replace the default post content sent to search with the person's about data.
	$data['content'] = $person['about'];
	$data['url'] = get_permalink( $post->ID );

	return $data;
}
