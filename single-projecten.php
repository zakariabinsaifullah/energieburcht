<?php
/**
 * The template for displaying single Projecten posts.
 *
 * WordPress template hierarchy resolves this file for any singular request
 * to the 'projecten' post type before falling back to single.php or index.php.
 *
 * Layout:
 *  1. Hero   — featured image (container-width) with bottom gradient + h1 title.
 *  2. Body   — narrow content wrapper (~90 % of container).
 *             If the Projecten Sidebar has active widgets: 75 / 25 two-column
 *             layout (content left, sidebar right).
 *             If the sidebar is empty: full-width content.
 *  3. Related — "Meer projecten?" row + 3 random Projecten cards.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Energieburcht
 */

get_header();
?>

<main id="primary" class="site-main single-projecten">

	<?php while ( have_posts() ) : the_post(); ?>

		<?php /* ── 1. Hero ───────────────────────────────────────────────── */ ?>
		<div class="container">
			<?php get_template_part( 'parts/hero-single', 'projecten' ); ?>
		</div>

		<?php get_template_part( 'parts/breadcrumbs' ); ?>

		<?php /* ── 2. Body ───────────────────────────────────────────────── */ ?>
		<div class="container boxed-content">
			<div class="single-projecten-wrap">

				<?php if ( is_active_sidebar( 'projecten-sidebar' ) ) : ?>

					<div class="single-projecten-layout">

						<div class="single-projecten-content entry-content">
							<?php the_content(); ?>
						</div>

						<aside class="single-projecten-sidebar" aria-label="<?php esc_attr_e( 'Projecten sidebar', 'energieburcht' ); ?>">
							<?php dynamic_sidebar( 'projecten-sidebar' ); ?>
						</aside>

					</div><!-- .single-projecten-layout -->

				<?php else : ?>

					<div class="single-projecten-content entry-content">
						<?php the_content(); ?>
					</div>

				<?php endif; ?>

			</div><!-- .single-projecten-wrap -->
		</div><!-- .container -->

	<?php endwhile; ?>

	<?php /* ── 3. Related projects ────────────────────────────────────────── */ ?>
	<?php get_template_part( 'parts/related', 'projecten' ); ?>

</main><!-- #primary -->

<?php
get_footer();
