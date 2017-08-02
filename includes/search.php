<?php

namespace WSU\Murrow\Search;

add_filter( 'wp_nav_menu_items', 'WSU\Murrow\Search\add_search_to_menu', 10, 2 );
add_filter( 'wsuwp_search_public_status', 'WSU\Murrow\Search\search_public_status' );
add_filter( 'query_vars', 'WSU\Murrow\Search\filter_query_variable' );
add_action( 'template_redirect', 'WSU\Murrow\Search\redirect_wp_default_search' );

/**
 * Filters the nav items attached to the global navigation and appends a
 * search form.
 *
 * @param $items
 * @param $args
 *
 * @return string
 */
function add_search_to_menu( $items, $args ) {
	if ( 'global-top-menu' !== $args->theme_location ) {
		return $items;
	}

	return $items . '<li class="search">' . get_search_form( false ) . '</li>';
}

/**
 * Adds support for search indexing while also set as a private site.
 *
 * @return int
 */
function search_public_status() {
	return 1;
}

/**
 * Redirect requests to the default WordPress search to our new URL.
 */
function redirect_wp_default_search() {
	if ( is_search() ) {
		wp_redirect( home_url( '/search/?q=' . get_Query_var( 's' ) ) );
		exit;
	}
}

/**
 * Adds `q` as our search query variable.
 *
 * @param $vars
 *
 * @return array
 */
function filter_query_variable( $vars ) {
	$vars[] = 'q';
	return $vars;
}

/**
 * Filters the content returned by Elastic Search for display in a search
 * results page.
 *
 * @param string $visible_content
 *
 * @return mixed|string
 */
function filter_elastic_content( $visible_content ) {
	$visible_content = preg_replace( '/[\r\n]+/', "\n", $visible_content );
	$visible_content = preg_replace( '/[ \t]+/', ' ', $visible_content );
	$visible_content = strip_tags( $visible_content, '<p><strong><em>' );
	$visible_content = trim( $visible_content );
	$visible_content = substr( $visible_content, 0, 260 );
	$visible_content = force_balance_tags( $visible_content . '....' );
	$visible_content = wpautop( $visible_content, false );

	return $visible_content;
}

/**
 * Processes a search request by passing to the WSU ES server.
 *
 * @param string $var
 *
 * @return array
 */
function get_elastic_response( $var ) {
	$search_key = md5( 'search' . $var );
	$results = wp_cache_get( $search_key, 'search' );

	if ( $results ) {
		return $results;
	}

	$request_url = 'https://elastic.wsu.edu/wsu-web/_search?q=%2bhostname:stage.murrow.wsu.edu%20%2b' . rawurlencode( $var );

	$response = wp_remote_get( $request_url );

	if ( is_wp_error( $response ) ) {
		return array();
	}

	if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return array();
	}

	$response = wp_remote_retrieve_body( $response );
	$response = json_decode( $response );

	if ( isset( $response->hits ) && isset( $response->hits->total ) && 0 === $response->hits->total ) {
		return array(); // no results found.
	}

	$search_results = $response->hits->hits;

	wp_cache_set( $search_key, $search_results, 'search', 3600 );

	return $search_results;
}
