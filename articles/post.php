<?php
$post_share_placement = spine_get_option( 'post_social_placement' );
$subhead = get_post_meta( get_the_ID(), '_murrow_subhead', true );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<?php if ( is_single() ) : ?>
	<header class="article-header">
		<hgroup>
			<h1 class="article-title"><?php the_title(); ?></h1>
			<?php
			if ( $subhead ) {
				echo wp_kses_post( apply_filters( 'the_content', $subhead ) );
				}
			?>
		</hgroup>
		<div class="featured-image-wrapper">
		<?php
		// If a featured image is assigned to the post, output it as a figure with a background image accordingly.
		if ( spine_has_featured_image() ) {
			$featured_image_src = spine_get_featured_image_src();
			$featured_image_position = get_post_meta( get_the_ID(), '_featured_image_position', true );

			if ( ! $featured_image_position || sanitize_html_class( $featured_image_position ) !== $featured_image_position ) {
				$featured_image_position = '';
			}

			?><figure class="featured-image <?php echo $featured_image_position; ?>" style="background-image: url('<?php echo esc_url( $featured_image_src ); ?>');"><?php spine_the_featured_image(); ?></figure><?php
		}
		?>
		</div>
	</header>

	<div class="article-body row side-right">
		<div class="column one">
			<?php the_content(); ?>
		</div>
		<div class="column two">
			<?php
			// Get a list of top level categories.
			$categories = wp_get_object_terms( get_the_ID(), 'category', array(
				'parent' => 0,
			) );

			if ( 0 < count( $categories ) ) {
				echo '<p class="meta-head meta-topic">Topics</p>';
				echo '<ul class="meta-item-list">';

				foreach ( $categories as $category ) {
					$url = get_category_link( $category->term_id );
					echo '<li class="meta-item"><a href="' . esc_url( $url ) .'">' . esc_html( $category->name ) . '</a>';
				}

				echo '</ul>';
			}

			if ( function_exists( 'wsuwp_uc_get_object_people' ) ) {
				$people = wsuwp_uc_get_object_people( get_the_ID() );
			} else {
				$people = array();
			}

			if ( 0 < count( $people ) ) {
				echo '<p class="meta-head meta-people">People</p>';
				echo '<ul class="meta-item-list">';

				foreach ( $people as $person ) {
					echo '<li class="meta-item"><a href="' . esc_url( $person['url'] ) .'">' . esc_html( $person['name'] ) . '</a></li>';
				}

				echo '</ul>';
			}
			?>
		</div>
	</div>

<?php else : // If not is_single() ?>
	<header class="article-header">
		<h2 class="article-title">
			<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h2>
	</header>
<?php endif; ?>

<?php if ( ! is_singular() ) : ?>
	<div class="article-summary">
		<?php

		if ( spine_has_thumbnail_image() ) {
			?><figure class="article-thumbnail"><a href="<?php the_permalink(); ?>"><?php spine_the_thumbnail_image(); ?></a></figure><?php
		} elseif ( spine_has_featured_image() ) {
			?><figure class="article-thumbnail"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'spine-thumbnail_size' ); ?></a></figure><?php
		}

		if ( $subhead ) {
			?><div class="article-subhead"><?php
			echo wp_kses_post( apply_filters( 'the_content', $subhead ) );
			?></div><?php
		}
		?>

		<p class="article-read-more">
			<a href="<?php echo esc_url( get_the_permalink() ); ?>">Read the article</a>
		</p>
	</div><!-- .article-summary -->
<?php endif; ?>

	<footer class="article-footer">

	<?php
	// Display site level categories attached to the post.
	if ( has_category() ) {
		echo '<dl class="categorized">';
		echo '<dt><span class="categorized-default">Categorized</span></dt>';
		foreach ( get_the_category() as $category ) {
			echo '<dd><a href="' . esc_url( get_category_link( $category->cat_ID ) ) . '">' . esc_html( $category->cat_name ) . '</a></dd>';
		}
		echo '</dl>';
	}

	// Display University categories attached to the post.
	if ( taxonomy_exists( 'wsuwp_university_category' ) && has_term( '', 'wsuwp_university_category' ) ) {
		$university_category_terms = get_the_terms( get_the_ID(), 'wsuwp_university_category' );
		if ( ! is_wp_error( $university_category_terms ) ) {
			echo '<dl class="university-categorized">';
			echo '<dt><span class="university-categorized-default">Categorized</span></dt>';

			foreach ( $university_category_terms as $term ) {
				$term_link = get_term_link( $term->term_id, 'wsuwp_university_category' );
				if ( ! is_wp_error( $term_link ) ) {
					echo '<dd><a href="' . esc_url( $term_link ) . '">' . esc_html( $term->name ) . '</a></dd>';
				}
			}
			echo '</dl>';
		}
	}

	// Display University tags attached to the post.
	if ( has_tag() ) {
		echo '<dl class="tagged">';
		echo '<dt><span class="tagged-default">Tagged</span></dt>';
		foreach ( get_the_tags() as $tag ) {
			echo '<dd><a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '">' . esc_html( $tag->name ) . '</a></dd>';
		}
		echo '</dl>';
	}

	// Display University locations attached to the post.
	if ( taxonomy_exists( 'wsuwp_university_location' ) && has_term( '', 'wsuwp_university_location' ) ) {
		$university_location_terms = get_the_terms( get_the_ID(), 'wsuwp_university_location' );
		if ( ! is_wp_error( $university_location_terms ) ) {
			echo '<dl class="university-location">';
			echo '<dt><span class="university-location-default">Location</span></dt>';

			foreach ( $university_location_terms as $term ) {
				$term_link = get_term_link( $term->term_id, 'wsuwp_university_location' );
				if ( ! is_wp_error( $term_link ) ) {
					echo '<dd><a href="' . esc_url( $term_link ) . '">' . esc_html( $term->name ) . '</a></dd>';
				}
			}
			echo '</dl>';
		}
	}

?>

</article>
