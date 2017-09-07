<section class="row single gutter">

	<?php while ( have_posts() ) : the_post(); ?>

		<?php get_template_part( 'articles/post', get_post_type() ) ?>

	<?php endwhile; ?>

</section>
