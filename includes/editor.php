<?php

namespace WSU\Murrow\Editor;

add_filter( 'mce_buttons', 'WSU\Murrow\Editor\add_styleselect_button' );
add_filter( 'tiny_mce_before_init', 'WSU\Murrow\Editor\custom_style_formats' );

/**
 * Add the "Format" button as the second option in the top row of TinyMCE.
 *
 * @param array $buttons
 *
 * @return array
 */
function add_styleselect_button( $buttons ) {
	$button = array_shift( $buttons );
	array_unshift( $buttons, 'styleselect' );
	array_unshift( $buttons, $button );

	return $buttons;
}

/**
 * Add custom style formats to the "Format" drop-down in TinyMCE.
 *
 * @param array $settings
 *
 * @return array
 */
function custom_style_formats( $settings ) {

	$style_formats = array(
		array(
			'title' => 'Pageheader Sub',
			'block' => 'p',
			'classes' => 'pageheader-sub',
		),
	);

	$settings['style_formats'] = wp_json_encode( $style_formats );

	return $settings;
}
