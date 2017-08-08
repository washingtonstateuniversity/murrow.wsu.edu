<?php

namespace WSU\Murrow\Breadcrumb;

add_action( 'bcn_after_fill', 'WSU\Murrow\Breadcrumb\remove_current_item' );

/**
 * Remove the current (last) item from the breadcrumb trail before output.
 *
 * Forked with thanks from https://github.com/mtekk/Breadcrumb-NavXT-Extensions
 *
 * @since 0.3.7
 *
 * @param \bcn_breadcrumb_trail $trail The object containing the breadcrumb trail.
 */
function remove_current_item( $trail ) {
	$types = $trail->breadcrumbs[0]->get_types();

	if ( is_array( $types ) && in_array( 'current-item', $types, true ) ) {
		array_shift( $trail->breadcrumbs );
	}
}
