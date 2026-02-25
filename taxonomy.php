<?php
/**
 * Global Taxonomy Archive Template.
 *
 * WordPress falls back to this file for any taxonomy term archive that is not
 * handled by a more specific template (e.g. taxonomy-{taxonomy}.php or
 * taxonomy-{taxonomy}-{term}.php).
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package Energieburcht
 */

// ── Customizer values — card styles ───────────────────────────────────────────
$item_bg         = get_theme_mod( 'energieburcht_tax_item_bg',         '#ffffff' );
$title_color     = get_theme_mod( 'energieburcht_tax_title_color',     '#003449' );
$excerpt_color   = get_theme_mod( 'energieburcht_tax_excerpt_color',   '#212529' );
$btn_color       = get_theme_mod( 'energieburcht_tax_btn_color',       '#ffffff' );
$btn_bg          = get_theme_mod( 'energieburcht_tax_btn_bg',          '#00acdd' );
$btn_hover_color = get_theme_mod( 'energieburcht_tax_btn_hover_color', '#ffffff' );
$btn_hover_bg    = get_theme_mod( 'energieburcht_tax_btn_hover_bg',    '#0095c0' );

// ── Customizer values — pagination ────────────────────────────────────────────
$pag_color        = get_theme_mod( 'energieburcht_tax_pag_color',        '#003449' );
$pag_active_color = get_theme_mod( 'energieburcht_tax_pag_active_color', '#ffffff' );
$pag_active_bg    = get_theme_mod( 'energieburcht_tax_pag_active_bg',    '#00acdd' );

// ── Read more text ────────────────────────────────────────────────────────────
$read_more_text = get_theme_mod( 'energieburcht_tax_read_more_text', __( 'Lees meer', 'energieburcht' ) );

// ── Results label (Dutch) ─────────────────────────────────────────────────────
$results_singular = get_theme_mod( 'energieburcht_tax_results_singular', __( 'resultaat', 'energieburcht' ) );
$results_plural   = get_theme_mod( 'energieburcht_tax_results_plural',   __( 'resultaten', 'energieburcht' ) );

get_header();
?>

<style>
.archive-taxonomy {
	--tax-item-bg:          <?php echo esc_attr( $item_bg ); ?>;
	--tax-title-color:      <?php echo esc_attr( $title_color ); ?>;
	--tax-excerpt-color:    <?php echo esc_attr( $excerpt_color ); ?>;
	--tax-btn-color:        <?php echo esc_attr( $btn_color ); ?>;
	--tax-btn-bg:           <?php echo esc_attr( $btn_bg ); ?>;
	--tax-btn-hover-color:  <?php echo esc_attr( $btn_hover_color ); ?>;
	--tax-btn-hover-bg:     <?php echo esc_attr( $btn_hover_bg ); ?>;
	--tax-pag-color:        <?php echo esc_attr( $pag_color ); ?>;
	--tax-pag-active-color: <?php echo esc_attr( $pag_active_color ); ?>;
	--tax-pag-active-bg:    <?php echo esc_attr( $pag_active_bg ); ?>;
}
</style>

<main id="primary" class="site-main archive-taxonomy">

	<?php get_template_part( 'parts/hero-archive', 'taxonomy' ); ?>

	<?php get_template_part( 'parts/breadcrumbs' ); ?>

	<div class="container boxed-content">

		<?php if ( have_posts() ) : ?>

			<div class="tax-archive-meta">
				<span class="tax-archive-count">
					<?php
					$total = $GLOBALS['wp_query']->found_posts;
					/* translators: 1: number of results, 2: singular or plural label */
					printf(
						'%1$d %2$s',
						(int) $total,
						esc_html( 1 === (int) $total ? $results_singular : $results_plural )
					);
					?>
				</span>
			</div><!-- .tax-archive-meta -->

			<div class="tax-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'tax-item' ); ?>>

						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>" class="tax-thumbnail" tabindex="-1" aria-hidden="true">
								<?php the_post_thumbnail( 'large' ); ?>
							</a>
						<?php endif; ?>

						<div class="tax-content">

							<h2 class="tax-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>

							<?php if ( has_excerpt() ) : ?>
								<div class="tax-excerpt">
									<?php the_excerpt(); ?>
								</div>
							<?php endif; ?>

							<a href="<?php the_permalink(); ?>" class="tax-read-more">
								<?php echo esc_html( $read_more_text ); ?>
							</a>

						</div><!-- .tax-content -->

					</article>
				<?php endwhile; ?>
			</div><!-- .tax-grid -->

			<?php
			$total_pages = $GLOBALS['wp_query']->max_num_pages;

			if ( $total_pages > 1 ) :
				$paged = max( 1, get_query_var( 'paged' ) );
			?>
			<div class="tax-pagination-wrap">
				<p class="tax-pagination-info">
					<?php
					printf(
						'%1$d %2$s',
						(int) $total,
						esc_html( 1 === (int) $total ? $results_singular : $results_plural )
					);
					?>
				</p>
			<nav id="tax-pagination" class="tax-pagination" aria-label="<?php esc_attr_e( 'Archive navigation', 'energieburcht' ); ?>">
				<?php
				echo paginate_links( array(
					'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
					'format'    => '?paged=%#%',
					'current'   => $paged,
					'total'     => $total_pages,
					'prev_text' => '<span class="tax-pagination__arrow" aria-hidden="true">&larr;</span><span class="screen-reader-text">' . esc_html__( 'Previous', 'energieburcht' ) . '</span>',
					'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next', 'energieburcht' ) . '</span><span class="tax-pagination__arrow" aria-hidden="true">&rarr;</span>',
					'type'      => 'plain',
				) );
				?>
			</nav><!-- #tax-pagination -->
			</div><!-- .tax-pagination-wrap -->
			<?php endif; ?>

		<?php else : ?>

			<?php get_template_part( 'parts/content', 'none' ); ?>

		<?php endif; ?>

	</div><!-- .container -->

</main><!-- #primary -->

<?php
get_footer();
