<?php
/**
 * The template for displaying search results.
 *
 * Mirrors the structure of the Projecten and Kennisitems archives:
 * a full-width hero section, breadcrumbs, and a responsive card grid.
 * Each result card shows the post type as a tag, the featured image,
 * title, excerpt and a "Lees meer" link.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Energieburcht
 */

get_header();
?>

<main id="primary" class="site-main search-results-page">

	<?php /* ── Hero ── */ ?>
	<section id="search-results-hero" class="page-hero search-results-hero">
		<div class="container">
			<div class="page-hero-content">
				<h1 class="page-hero-title">
					<?php
					if ( get_search_query() ) {
						/* translators: %s: search query. */
						printf(
							esc_html__( 'Zoekresultaten voor: %s', 'energieburcht' ),
							'<span class="search-results-hero__query">' . esc_html( get_search_query() ) . '</span>'
						);
					} else {
						esc_html_e( 'Zoeken', 'energieburcht' );
					}
					?>
				</h1>

				<div class="search-results-hero__form">
					<?php get_search_form(); ?>
				</div>
			</div><!-- .page-hero-content -->
		</div><!-- .container -->
	</section><!-- #search-results-hero -->

	<?php get_template_part( 'parts/breadcrumbs' ); ?>

	<div class="container boxed-content">

		<?php if ( have_posts() ) : ?>

			<p class="search-results-count">
				<?php
				$found = $GLOBALS['wp_query']->found_posts;
				printf(
					/* translators: %d: number of results. */
					esc_html( _n( '%d resultaat gevonden', '%d resultaten gevonden', $found, 'energieburcht' ) ),
					(int) $found
				);
				?>
			</p>

			<div class="search-results-grid">

				<?php while ( have_posts() ) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'search-result-item' ); ?>>

						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>" class="search-result-thumbnail" tabindex="-1" aria-hidden="true">
								<?php the_post_thumbnail( 'large' ); ?>
							</a>
						<?php endif; ?>

						<div class="search-result-content">

							<span class="search-result-type-tag">
								<?php
								$post_type_obj = get_post_type_object( get_post_type() );
								echo esc_html( $post_type_obj ? $post_type_obj->labels->singular_name : get_post_type() );
								?>
							</span>

							<h2 class="search-result-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>

							<?php if ( has_excerpt() || get_the_excerpt() ) : ?>
								<div class="search-result-excerpt">
									<?php the_excerpt(); ?>
								</div>
							<?php endif; ?>

							<a href="<?php the_permalink(); ?>" class="search-result-read-more">
								<?php esc_html_e( 'Lees meer', 'energieburcht' ); ?>
							</a>

						</div><!-- .search-result-content -->

					</article>

				<?php endwhile; ?>

			</div><!-- .search-results-grid -->

			<?php
			$total_pages = $GLOBALS['wp_query']->max_num_pages;

			if ( $total_pages > 1 ) :
				$paged = max( 1, get_query_var( 'paged' ) );
			?>
			<nav class="search-results-pagination" aria-label="<?php esc_attr_e( 'Zoekresultaten navigatie', 'energieburcht' ); ?>">
				<?php
				echo paginate_links( array(
					'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
					'format'    => '?paged=%#%',
					'current'   => $paged,
					'total'     => $total_pages,
					'prev_text' => '<span class="search-results-pagination__arrow" aria-hidden="true">&larr;</span><span class="screen-reader-text">' . esc_html__( 'Vorige', 'energieburcht' ) . '</span>',
					'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Volgende', 'energieburcht' ) . '</span><span class="search-results-pagination__arrow" aria-hidden="true">&rarr;</span>',
					'type'      => 'plain',
				) );
				?>
			</nav><!-- .search-results-pagination -->
			<?php endif; ?>

		<?php else : ?>

			<div class="search-no-results">
				<p class="search-no-results__message">
					<?php
					printf(
						/* translators: %s: search query. */
						esc_html__( 'Geen resultaten gevonden voor "%s". Probeer andere zoekwoorden.', 'energieburcht' ),
						esc_html( get_search_query() )
					);
					?>
				</p>
				<?php get_search_form(); ?>
			</div><!-- .search-no-results -->

		<?php endif; ?>

	</div><!-- .container -->

</main><!-- #primary -->

<?php
get_footer();
