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
	);
}
