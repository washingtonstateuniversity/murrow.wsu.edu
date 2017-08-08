<?php

if ( ! is_front_page() && spine_display_breadcrumbs( 'top' ) ) {
	$breadcrumb_display = apply_filters( 'murrow_filter_breadcrumb', bcn_display( true ) );
	?>
	<section class="row single breadcrumbs breadcrumbs-top gutter pad-top" typeof="BreadcrumbList" vocab="http://schema.org/">
		<div class="column one">
			<?php echo bcn_display( true ); //@codingStandardsIgnoreLine ?>
		</div>
	</section>
	<?php
}
