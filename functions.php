<?php

include_once __DIR__ . '/includes/university-center-objects.php';

add_filter( 'spine_child_theme_version', 'murrow_theme_version' );
function murrow_theme_version() {
	return '0.0.1';
}

add_action( 'init', 'murrow_remove_spine_wp_enqueue_scripts' );
function murrow_remove_spine_wp_enqueue_scripts() {
	remove_action( 'wp_enqueue_scripts', 'spine_wp_enqueue_scripts', 20 );
}

add_filter( 'spine_get_campus_home_url', 'murrow_spine_campus_home_url' );
function murrow_spine_campus_home_url() {
	return 'https://murrow.wsu.edu';
}

add_filter( 'spine_get_campus_data', 'murrow_spine_campus_data' );
function murrow_spine_campus_data() {
	return 'Edward R. Murrow College of Communication';
}

add_action( 'wp_enqueue_scripts', 'murrow_spine_wp_enqueue_scripts', 20 );
/**
 * Enqueue scripts and styles required for front end pageviews.
 */
function murrow_spine_wp_enqueue_scripts() {

	$spine_version = spine_get_option( 'spine_version' );
	// This may be an unnecessary check, but we don't want to screw this up.
	if ( 'develop' !== $spine_version && 0 === absint( $spine_version ) ) {
		$spine_version = 1;
	}

	// Much relies on the main stylesheet provided by the WSU Spine.
	wp_enqueue_style( 'wsu-spine', 'https://repo.wsu.edu/spine/' . $spine_version . '/spine.min.css', array(), spine_get_script_version() );
	wp_enqueue_style( 'spine-theme', get_template_directory_uri() . '/style.css', array( 'wsu-spine' ), spine_get_script_version() );
	wp_enqueue_style( 'spine-theme-child', get_stylesheet_directory_uri() . '/style.css', array( 'wsu-spine' ), spine_get_child_version() );
	wp_enqueue_style( 'spine-theme-print', get_template_directory_uri() . '/css/print.css', array(), spine_get_script_version(), 'print' );

	// All theme styles have been output at this time. Plugins and other themes should print styles here, before blocking
	// Javascript resources are output.
	do_action( 'spine_enqueue_styles' );

	$google_font_css_url = '//fonts.googleapis.com/css?family=';
	$count = 0;
	$spine_open_sans = spine_get_open_sans_options();

	// Build the URL used to pull additional Open Sans font weights and styles from Google.
	if ( ! empty( $spine_open_sans ) ) {
		$build_open_sans_css = '';
		foreach ( $spine_open_sans as $font_option ) {
			if ( 0 === $count ) {
				$build_open_sans_css = 'Open+Sans%3A' . $font_option;
			} else {
				$build_open_sans_css .= '%2C' . $font_option;
			}

			$count++;
		}

		if ( 0 !== $count ) {
			$google_font_css_url .= $build_open_sans_css;
		} else {
			$google_font_css_url = '';
		}
	} else {
		$google_font_css_url = '';
	}

	$spine_open_sans_condensed = spine_get_open_sans_condensed_options();

	$condensed_count = 0;
	if ( ! empty( $spine_open_sans_condensed ) ) {
		if ( 0 !== $count ) {
			$build_open_sans_cond_css = '|Open+Sans+Condensed%3A';
		} else {
			$build_open_sans_cond_css = 'Open+Sans+Condensed%3A';
		}

		foreach ( $spine_open_sans_condensed as $font_option ) {
			if ( 0 === $condensed_count ) {
				$build_open_sans_cond_css .= $font_option;
			} else {
				$build_open_sans_cond_css .= '%2C' . $font_option;
			}

			$count++;
			$condensed_count++;
		}

		$google_font_css_url .= $build_open_sans_cond_css;
	}

	// Only enqueue a custom Google Fonts URL if extra options have been selected for Open Sans.
	if ( '' !== $google_font_css_url ) {
		$google_font_css_url .= '&subset=latin,latin-ext';

		// Deregister the default Open Sans URL provided by WordPress core and instead provide our own.
		wp_deregister_style( 'open-sans' );
		wp_enqueue_style( 'open-sans', $google_font_css_url, array(), false );
	}

	// WordPress core provides much of jQuery UI, but not in a nice enough package to enqueue all at once.
	// For this reason, we'll pull the entire package from the Google CDN.
	wp_enqueue_script( 'wsu-jquery-ui-full', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js', array( 'jquery' ) );

	// Much relies on the main Javascript provided by the WSU Spine.
	wp_enqueue_script( 'wsu-spine', get_stylesheet_directory_uri() . '/js/spine.min.js', array( 'wsu-jquery-ui-full' ), spine_get_child_version(), false );

	// Override default options in the WSU Spine.
	$twitter_text = ( is_front_page() ) ? get_option( 'blogname' ) : trim( wp_title( '', false ) );
	$spineoptions = array(
		'social' => array(
			'share_text' => esc_js( spine_get_title() ),
			'twitter_text' => esc_js( $twitter_text ),
			'twitter_handle' => 'wsupullman',
		),
	);

	// If a Twitter account has been added in the Customizer, use that for the via handle.
	$spine_social_options = spine_social_options();
	if ( isset( $spine_social_options['twitter'] ) ) {
		$twitter_array = array_filter( explode( '/', $spine_social_options['twitter'] ) );
		$twitter_handle = array_pop( $twitter_array );
		$spineoptions['social']['twitter_handle'] = esc_js( $twitter_handle );
	}
	wp_localize_script( 'wsu-spine', 'spineoptions', $spineoptions );

	// Enqueue jQuery Cycle2 and Genericons when a page builder template is used.
	if ( is_page_template( 'template-builder.php' ) ) {
		$has_builder_banner = get_post_meta( get_the_ID(), '_has_builder_banner', true );

		if ( $has_builder_banner ) {
			// Enqueue the compilation of jQuery Cycle2 scripts required for the slider
			wp_enqueue_script( 'wsu-cycle', get_template_directory_uri() . '/js/cycle2/jquery.cycle2.min.js', array( 'jquery' ), spine_get_script_version(), true );
			wp_enqueue_style( 'genericons', get_template_directory_uri() . '/styles/genericons/genericons.css', array(), spine_get_script_version() );
		}
	}

	// Enqueue scripting for the entire parent theme.
	wp_enqueue_script( 'wsu-spine-theme-js', get_template_directory_uri() . '/js/spine-theme.js', array( 'jquery' ), spine_get_script_version(), true );
}

add_filter( 'nav_menu_link_attributes', 'murrow_nav_menu_link_attributes', 20, 3 );
/**
 * Alters the anchor HREF on section label pages to output as # when building
 * a site navigation.
 *
 * @param array   $atts
 * @param object  $item
 * @param array   $args
 *
 * @return mixed
 */
function murrow_nav_menu_link_attributes( $atts, $item, $args ) {
	if ( 'site' !== $args->menu ) {
		return $atts;
	}
	if ( 'page' === $item->object && 'post_type' === $item->type ) {
		$slug = get_page_template_slug( $item->object_id );
		if ( 'templates/section-label.php' === $slug ) {
			$atts['href'] = '#';
		}
	}
	return $atts;
}

add_filter( 'nav_menu_css_class', 'murrow_nav_menu_css_class', 20, 3 );
/**
 * Assign a class of `.non-anchor` to any menu items that are custom links
 * with an href of #.
 *
 * @param $classes
 * @param $item
 * @param $args
 *
 * @return array
 */
function murrow_nav_menu_css_class( $classes, $item, $args ) {
	if ( 'site' !== $args->menu ) {
		return $classes;
	}
	if ( 'page' === $item->object && 'post_type' === $item->type ) {
		$slug = get_page_template_slug( $item->object_id );
		if ( 'templates/section-label.php' === $slug ) {
			$classes[] = 'non-anchor';
		}
	}
	if ( 'custom' === $item->object && 'custom' === $item->type && '#' === $item->url ) {
		$classes[] = 'non-anchor';
	}
	return $classes;
}

add_filter( 'nav_menu_item_id', 'murrow_nav_menu_id', 20 );
/**
 * Strips menu item IDs as navigation is built.
 *
 * @param string $id
 *
 * @return bool
 */
function murrow_nav_menu_id( $id ) {
	return false;
}

add_filter( 'wp_nav_menu_items', 'murrow_add_search_form_to_global_top_menu', 10, 2 );
/**
 * Filters the nav items attached to the global navigation and appends a
 * search form.
 *
 * @param $items
 * @param $args
 *
 * @return string
 */
function murrow_add_search_form_to_global_top_menu( $items, $args ) {
	if ( 'global-top-menu' !== $args->theme_location ) {
		return $items;
	}
	return $items . '<li class="search">' . get_search_form( false ) . '</li>';
}

add_action( 'after_setup_theme', 'murrow_nav_menu_register' );
/**
 * Register additional menus used by the theme.
 */
function murrow_nav_menu_register() {
	register_nav_menu( 'global-top-menu', 'Global Top Menu' );
	add_theme_support( 'html5', array( 'search-form' ) );
}

/**
 * Determine what should be displayed in the spine's main header area for the
 * sub and sub sections.
 *
 * @return array List of elements for output in main header.
 */
function murrow_get_main_header() {
	$main_header = array(
		'name' => '',
		'description' => '',
	);
	// Top level pages show their title. Sub pages show their parent's title.
	if ( is_page() ) {
		$_post = get_post();
		if ( $_post->post_parent ) {
			$main_header['description'] = get_the_title( $_post->post_parent );
		} else {
			$main_header['description'] = get_the_title();
		}
	} elseif ( is_singular( 'post' ) ) {
		$main_header['description'] = 'News';
	} elseif ( is_singular( 'tribe_events' ) ) {
		$main_header['description'] = 'Events';
	} elseif ( is_archive() ) {
		if ( is_category() ) {
			$main_header['description'] = 'Category: ' . single_cat_title( '', false );
		} elseif ( is_tag() ) {
			$main_header['description'] = 'Tag: ' . single_tag_title( '', false );
		} elseif ( is_day() ) {
			$month_object = DateTime::createFromFormat( '!m', get_query_var( 'monthnum' ) );
			$month_name = $month_object->format( 'F' );
			$main_header['description'] = 'Archive: ' . $month_name . ' ' . get_query_var( 'day' ) . ', ' . get_query_var( 'year' );
		} elseif ( is_month() ) {
			$month_object = DateTime::createFromFormat( '!m', get_query_var( 'monthnum' ) );
			$month_name = $month_object->format( 'F' );
			$main_header['description'] = 'Archive: ' . $month_name . ' ' . get_query_var( 'year' );
		} elseif ( is_year() ) {
			$main_header['description'] = 'Archive: ' . get_query_var( 'year' );
		} elseif ( is_author() ) {
			$main_header['description'] = get_the_author();
		} elseif ( is_post_type_archive( 'tribe_events' ) ) {
			$main_header['description'] = 'Events';
		} else {
			$main_header['description'] = 'Archives';
		}
	} elseif ( is_404() ) {
		$main_header['description'] = 'Page not found';
	}
	if ( is_search() ) {
		$main_header['description'] = 'Search';
	}
	if ( is_home() ) {
		$page_for_posts = absint( get_option( 'page_for_posts', 0 ) );
		$main_header['description'] = get_the_title( $page_for_posts );
	}
	return $main_header;
}

add_action( 'admin_enqueue_scripts', 'murrow_admin_enqueue_scripts' );
/**
 * Enqueue scripts and styles used in the admin.
 *
 * @param string $hook_suffix
 */
function murrow_admin_enqueue_scripts( $hook_suffix ) {
	if ( 'post' !== get_current_screen()->post_type ) {
		return;
	}

	if ( in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
		wp_enqueue_style( 'murrow-post', get_stylesheet_directory_uri() . '/admin-css/edit-post.css' );
		wp_enqueue_script( 'murrow-post', get_stylesheet_directory_uri() . '/js/admin-edit-post.min.js', array( 'jquery' ), null, true );
	}
}

add_action( 'edit_form_after_title', 'murrow_subhead_input' );
/**
 * Adds an input for capturing a subhead below the post title field.
 *
 * @param WP_Post $post Post object.
 */
function murrow_subhead_input( $post ) {
	if ( 'post' !== $post->post_type ) {
		return;
	}

	wp_nonce_field( 'save-murrow-subhead', '_murrow_subhead_nonce' );

	$subhead = get_post_meta( $post->ID, '_murrow_subhead', true );
	$subhead_editor_settings = array(
		'media_buttons' => false,
		'textarea_rows' => 2,
		'teeny' => true,
		'quicktags' => false,
	);

	?><h2>Subhead</h2><?php

	wp_editor( $subhead, '_murrow_subhead', $subhead_editor_settings );
}

add_action( 'save_post', 'murrow_save_post', 10, 2 );
/**
 * Saves additional data associated with a post.
 *
 * @param int     $post_id
 * @param WP_Post $post
 */
function murrow_save_post( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( 'auto-draft' === $post->post_status ) {
		return;
	}

	if ( ! isset( $_POST['_murrow_subhead_nonce'] ) || ! wp_verify_nonce( $_POST['_murrow_subhead_nonce'], 'save-murrow-subhead' ) ) {
		return;
	}

	if ( isset( $_POST['_murrow_subhead'] ) ) {
		update_post_meta( $post_id, '_murrow_subhead', wp_kses_post( $_POST['_murrow_subhead'] ) );
	} else {
		delete_post_meta( $post_id, '_murrow_subhead' );
	}
}
