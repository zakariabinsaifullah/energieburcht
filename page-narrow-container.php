<?php
/**
 * Template Name: Narrow Container
 *
 * Custom page template that keeps the full-width hero section but constrains
 * the main content area to 80 % of the standard container width.
 *
 * @package Energieburcht
 */

get_header();
?>

	<main id="primary" class="site-main">

		<?php get_template_part( 'parts/hero' ); ?>

		<?php get_template_part( 'parts/breadcrumbs' ); ?>

		<div class="container boxed-content">
			<div class="narrow-container">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'parts/content', 'page' );
				endwhile;
				?>
			</div><!-- .narrow-container -->
		</div><!-- .container -->

	</main><!-- #primary -->

<?php
get_footer();
