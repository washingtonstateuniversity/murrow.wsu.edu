<?php
$murrow_global_top_menu_args = array(
	'theme_location' => 'global-top-menu',
	'menu' => 'global-top-menu',
	'container' => false,
	'fallback_cb' => false,
	'menu_class' => null,
	'menu_id' => null,
	'items_wrap' => '<ul>%3$s</ul>',
	'depth' => 1,
);

?>
	<div id="contact-search" class="site-search">
		<?php wp_nav_menu( $murrow_global_top_menu_args ); ?>
	</div>
<?php

get_template_part( 'spine' );
