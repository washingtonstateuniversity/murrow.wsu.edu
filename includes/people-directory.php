<?php

namespace WSU\Murrow\People_Directory;

add_filter( 'wsuwp_people_default_rewrite_slug', 'WSU\Murrow\People_Directory\rewrite_arguments' );
add_filter( 'wsuwp_search_post_data', 'WSU\Murrow\People_Directory\search_data', 10, 2 );
add_action( 'wp_footer', 'WSU\Murrow\People_Directory\icons' );
add_filter( 'wsuwp_people_search_filter_label', 'WSU\Murrow\People_Directory\search_label' );

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

/**
 * Provides icons for use in profile cards.
 *
 * @since 0.3.4
 */
function icons() {
	$post = get_post();
	$template = get_post_meta( $post->ID, '_wp_page_template', true );

	if ( $template && 'templates/people.php' === $template ) {
		echo people_card_icons(); // @codingStandardsIgnoreLine
	}
}

/**
 * Returns the SVG containing the symbols used as icons for people cards.
 *
 * @since 0.6.2
 */
function people_card_icons() {
	ob_start();
	?>
	<svg xmlns="http://www.w3.org/2000/svg" class="people-card-icon-set">

		<symbol id="person-card-icon_phone" viewBox="0 0 15 15">
			<title>Phone icon</title>
			<path d="M14.7 13C14.3 14.4 12.5 15.1 11.2 15 9.4 14.8 7.5 13.9 6 12.9 3.9 11.3 1.8 9 0.7 6.5 -0.2 4.7-0.4 2.6 0.9 1 1.3 0.4 1.8 0 2.6 0 3.6 0 3.7 0.5 4.1 1.5 4.4 2.1 4.7 2.8 4.9 3.6 5.3 4.9 4 4.9 3.8 6 3.7 6.7 4.6 7.6 4.9 8.1 5.7 9.1 6.5 9.9 7.5 10.5 8.1 10.9 9 11.5 9.7 11.2 10.7 10.6 10.6 8.9 12 9.5 12.7 9.8 13.4 10.2 14.1 10.6 15.2 11.2 15.1 11.8 14.7 13 14.4 13.9 15 12.1 14.7 13" fill="#a60f2d"/>
		</symbol>

		<symbol id="person-card-icon_email" viewBox="0 0 20 15">
			<title>Email icon</title>
			<path d="M18 2.3L10 9.3 2 2.3 2 2 18 2 18 2.3ZM2 13L2 4.9 10 12 18 5 18 13 2 13ZM0 15L20 15 20 0 0 0 0 15Z" fill="#a60f2d"/>
		</symbol>

		<symbol id="person-card-icon_link" viewBox="0 0 20 20">
			<title>Website icon</title>
			<path d="M18.2 1.8C15.9-0.6 12.1-0.6 9.7 1.8L7.2 4.3 8.6 5.7 11.2 3.2C12.7 1.6 15.3 1.6 16.8 3.2 18.4 4.7 18.4 7.3 16.8 8.8L14.3 11.4 15.7 12.8 18.2 10.3C20.6 7.9 20.6 4.1 18.2 1.8L18.2 1.8ZM8.8 16.8C8.1 17.6 7.1 18 6 18 4.9 18 3.9 17.6 3.2 16.8 1.6 15.3 1.6 12.7 3.2 11.2L5.7 8.6 4.3 7.2 1.8 9.7C-0.6 12.1-0.6 15.9 1.8 18.2 2.9 19.4 4.5 20 6 20 7.6 20 9.1 19.4 10.3 18.2L12.8 15.7 11.4 14.3 8.8 16.8ZM12.3 6.3L13.7 7.7 7.7 13.7 6.3 12.3 12.3 6.3Z" fill="#a60f2d"/>
		</symbol>

	</svg>
	<?php
	$content = ob_get_clean();

	return $content;
}

/**
 * Change the search input placeholder text.
 *
 * @since 0.6.1
 */
function search_label() {
	return 'Search people';
}
