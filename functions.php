<?php

include_once __DIR__ . '/includes/search.php';
include_once __DIR__ . '/includes/university-center-objects.php';
include_once __DIR__ . '/includes/sub-headline.php';
include_once __DIR__ . '/includes/feature-video.php';
include_once __DIR__ . '/includes/people-directory.php';
include_once __DIR__ . '/includes/content-syndicate.php';
include_once __DIR__ . '/includes/media-library.php';
include_once __DIR__ . '/includes/breadcrumb.php';
include_once __DIR__ . '/includes/editor.php';
include_once __DIR__ . '/includes/featured-stories.php';

add_filter( 'spine_child_theme_version', 'murrow_theme_version' );
function murrow_theme_version() {
	return '0.6.10';
}

// Disable background image selection for posts and pages.
add_filter( 'spine_post_supports_background_image', '__return_false' );
add_filter( 'spine_page_supports_background_image', '__return_false' );

// Disable thumbnail image selection for posts and pages.
add_filter( 'spine_post_supports_thumbnail_image', '__return_false' );
add_filter( 'spine_page_supports_thumbnail_image', '__return_false' );

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

add_filter( 'bu_filter_list_section_defaults', 'murrow_bu_navigation_sub_menu_class' );
/**
 * Add a sub-menu class to child menus in the main navigation.
 *
 * @since 0.0.4
 *
 * @param array $atts
 *
 * @return array
 */
function murrow_bu_navigation_sub_menu_class( $atts ) {
	$atts['container_class'] = 'sub-menu';

	return $atts;
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

add_filter( 'bcn_breadcrumb_url', 'murrow_modify_breadcrumb_url', 10, 3 );
/**
 * Removes the URL from pages that are assigned the section label template.
 *
 * @since 0.3.4
 *
 * @param string $url
 * @param array  $type
 * @param int    $id
 *
 * @return string|null null if a section label template, untouched URL string if not.
 */
function murrow_modify_breadcrumb_url( $url, $type, $id ) {
	if ( 1 < count( $type ) && 'post-page' === $type[1] ) {
		$slug = get_page_template_slug( $id );

		if ( 'templates/section-label.php' === $slug ) {
			return null;
		}
	}

	return $url;
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

add_action( 'after_setup_theme', 'murrow_nav_menu_register' );
/**
 * Register additional menus used by the theme.
 */
function murrow_nav_menu_register() {
	register_nav_menu( 'global-top-menu', 'Global Top Menu' );
	register_nav_menu( 'site-footer-menu', 'Footer Menu' );
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

add_action( 'wp_footer', 'murrow_tracking_pixel' );

/**
 * Add tracking script(s) to the Murrow site.
 *
 * @since 0.6.9
 */
function murrow_tracking_pixel() {
	if ( 'murrow.wsu.edu' === get_site()->domain && '/' === get_site()->path && is_page( 'learn-more' ) ) {

		/**
		 * Tracking script provided by AdRoll for use on murrow.wsu.edu/learn-more/
		 */
		?>
		<!--
		* Marketing script for murrow.wsu.edu/learn-more/
		*
		* WSU contact: Corrie Wilder - corrie.wilder@wsu.edu
		* Script provider: AdRoll https://www.adroll.com/about/privacy
		* Campaign expiration: TBD
		-->
		<script type="text/javascript">
			adroll_adv_id = "OYAYJRVMBVGRZAXYJ6G6G5";
			adroll_pix_id = "WVGH62TJ4REALLAVY7WGYK";
			(function () {
				var _onload = function(){
					if (document.readyState && !/loaded|complete/.test(document.readyState)){setTimeout(_onload, 10);return}
					if (!window.__adroll_loaded){__adroll_loaded=true;setTimeout(_onload, 50);return}
					var scr = document.createElement("script");
					var host = "https://s.adroll.com";
					scr.setAttribute('async', 'true');
					scr.type = "text/javascript";
					scr.src = host + "/j/roundtrip.js";
					((document.getElementsByTagName('head') || [null])[0] ||
						document.getElementsByTagName('script')[0].parentNode).appendChild(scr);
				};
				if (window.addEventListener) {window.addEventListener('load', _onload, false);}
				else {window.attachEvent('onload', _onload)}
			}());
		</script>
		<?php
	}
}
