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

add_action( 'wp_footer', 'WSU\Murrow\People_Directory\icons' );
/**
 * Provides icons for use in profile cards.
 *
 * @since 0.3.4
 */
function icons() {
	$post = get_post();
	$template = get_post_meta( $post->ID, '_wp_page_template', true );

	if ( ! $template || 'templates/people.php' !== $template ) {
		return;
	}
	?>
	<svg xmlns="http://www.w3.org/2000/svg" class="icon-set">

		<symbol id="person-card-icon_phone" viewBox="0 0 15 15">
			<title>Phone number</title>
			<path d="M14.7 13C14.3 14.4 12.5 15.1 11.2 15 9.4 14.8 7.5 13.9 6 12.9 3.9 11.3 1.8 9 0.7 6.5 -0.2 4.7-0.4 2.6 0.9 1 1.3 0.4 1.8 0 2.6 0 3.6 0 3.7 0.5 4.1 1.5 4.4 2.1 4.7 2.8 4.9 3.6 5.3 4.9 4 4.9 3.8 6 3.7 6.7 4.6 7.6 4.9 8.1 5.7 9.1 6.5 9.9 7.5 10.5 8.1 10.9 9 11.5 9.7 11.2 10.7 10.6 10.6 8.9 12 9.5 12.7 9.8 13.4 10.2 14.1 10.6 15.2 11.2 15.1 11.8 14.7 13 14.4 13.9 15 12.1 14.7 13" fill="#ca1237"/>
		</symbol>

		<symbol id="person-card-icon_email" viewBox="0 0 20 15">
			<title>Email address</title>
			<path d="M18 2.3L10 9.3 2 2.3 2 2 18 2 18 2.3ZM2 13L2 4.9 10 12 18 5 18 13 2 13ZM0 15L20 15 20 0 0 0 0 15Z" fill="#ca1237"/>
		</symbol>

		<symbol id="person-card-icon_link" viewBox="0 0 20 20">
			<title>Website address</title>
			<path d="M18.2 1.8C15.9-0.6 12.1-0.6 9.7 1.8L7.2 4.3 8.6 5.7 11.2 3.2C12.7 1.6 15.3 1.6 16.8 3.2 18.4 4.7 18.4 7.3 16.8 8.8L14.3 11.4 15.7 12.8 18.2 10.3C20.6 7.9 20.6 4.1 18.2 1.8L18.2 1.8ZM8.8 16.8C8.1 17.6 7.1 18 6 18 4.9 18 3.9 17.6 3.2 16.8 1.6 15.3 1.6 12.7 3.2 11.2L5.7 8.6 4.3 7.2 1.8 9.7C-0.6 12.1-0.6 15.9 1.8 18.2 2.9 19.4 4.5 20 6 20 7.6 20 9.1 19.4 10.3 18.2L12.8 15.7 11.4 14.3 8.8 16.8ZM12.3 6.3L13.7 7.7 7.7 13.7 6.3 12.3 12.3 6.3Z" fill="#ca1237"/>
		</symbol>

		<symbol id="person-card-icon_arrow-right" viewBox="0 0 11 20">
			<title>Full profile</title>
			<path d="M1.4 0L0 1.4 8.3 10 7.4 10.9 7.4 10.9 0 18.6 1.4 20C3.4 17.9 9.1 12 11 10 9.6 8.5 11 10 1.4 0" fill="#ca1237"/>
		</symbol>

	</svg>
	<?php
}
