<?php

namespace WSU\Murrow\University_Center_Objects;

add_action( 'after_setup_theme', 'WSU\Murrow\University_Center_Objects\theme_support' );
add_filter( 'wsuwp_uc_people_content_type', 'WSU\Murrow\University_Center_Objects\people_content_type' );
add_filter( 'wsuwp_uc_project_content_type', 'WSU\Murrow\University_Center_Objects\project_content_type' );
add_filter( 'wsuwp_uc_entity_type_taxonomy_enabled', '__return_false' );
add_filter( 'wsuwp_uc_topic_taxonomy_enabled', '__return_false' );

/**
 * Filter the post type used for people in University Center Objects
 * by this theme.
 *
 * @since 0.0.1
 *
 * @return string
 */
function people_content_type() {
	return 'wsuwp_people_profile';
}

/**
 * Filter the post type used for "projects" in University Center Objects
 * by this theme.
 *
 * @since 0.0.1
 *
 * @return string
 */
function project_content_type() {
	return 'post';
}

/**
 * Add explicit support for University Center Objects people and projects.
 * This disables support for the built in entities and publications normally
 * provided by the plugin.
 *
 * @since 0.0.1
 */
function theme_support() {
	add_theme_support( 'wsuwp_uc_person' );
	add_theme_support( 'wsuwp_uc_project' );
}
