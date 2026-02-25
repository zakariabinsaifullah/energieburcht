<?php
/**
 * Template part: Yoast SEO breadcrumb bar.
 *
 * Renders a full-width breadcrumb strip (Yoast SEO) on every page except
 * the front page. Silently skips when:
 *  - current page is the front page (is_front_page())
 *  - Yoast SEO is not active (yoast_breadcrumb function absent)
 *  - Yoast breadcrumb display is disabled in SEO → Search Appearance settings
 *
 * Include in templates with:
 *   get_template_part( 'parts/breadcrumbs' );
 *
 * @package Energieburcht
 */

if ( is_front_page() ) {
	return;
}

if ( ! function_exists( 'yoast_breadcrumb' ) ) {
	return;
}
?>

<div class="eb-breadcrumbs-wrap">
	<div class="container">
		<?php
		yoast_breadcrumb(
			'<nav class="eb-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'energieburcht' ) . '">',
			'</nav>'
		);
		?>
	</div>
</div>
