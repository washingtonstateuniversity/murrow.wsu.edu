<?php
/**
 * Template Name: Builder Template
 */

get_header();
?>
	<main id="wsuwp-main" class="spine-blank-template">

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'parts/headers' ); ?>

			<div id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php

				// If a featured image is assigned to the post, output it as a figure with a background image accordingly.
				if ( spine_has_featured_image() ) {
					$featured_image_src = spine_get_featured_image_src();
					$featured_image_position = get_post_meta( get_the_ID(), '_featured_image_position', true );

					if ( ! $featured_image_position || sanitize_html_class( $featured_image_position ) !== $featured_image_position ) {
						$featured_image_position = '';
					}

					?>
					<div class="featured-image-wrapper">
						<figure class="featured-image <?php echo esc_attr( $featured_image_position ); ?>" style="background-image: url('<?php echo esc_url( $featured_image_src ); ?>');"><?php spine_the_featured_image(); ?></figure>
					</div><?php
				}

				/**
				 * `the_content` is fired on builder template pages while it is saved
				 * rather than while it is output in order for some advanced tags to
				 * survive the process and to avoid autop issues.
				 */
				remove_filter( 'the_content', 'wpautop', 10 );
				add_filter( 'wsu_content_syndicate_host_data', 'spine_filter_local_content_syndicate_item', 10, 3 );
				the_content();
				remove_filter( 'wsu_content_syndicate_host_data', 'spine_filter_local_content_syndicate_item', 10 );
				add_filter( 'the_content', 'wpautop', 10 );

				?>
			</div><!-- #post -->

			<?php
		endwhile;
		endif;

		get_template_part( 'parts/footers' );
		?>
	</main>
<?php get_footer();
