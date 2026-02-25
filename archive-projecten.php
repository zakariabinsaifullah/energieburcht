<?php
/**
 * The template for displaying the Projecten (Projects) archive.
 *
 * WordPress template hierarchy resolves this file for any request to the
 * 'projecten' post type archive before falling back to archive.php or
 * index.php.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Energieburcht
 */

// ── Customizer values — card styles ───────────────────────────────────────────
$item_bg          = get_theme_mod( 'energieburcht_cpt_projecten_item_bg',                 '#ffffff' );
$title_color      = get_theme_mod( 'energieburcht_cpt_projecten_title_color',             '#003449' );
$excerpt_color    = get_theme_mod( 'energieburcht_cpt_projecten_excerpt_color',           '#212529' );
$btn_color        = get_theme_mod( 'energieburcht_cpt_projecten_btn_color',               '#ffffff' );
$btn_bg           = get_theme_mod( 'energieburcht_cpt_projecten_btn_bg',                  '#00acdd' );
$btn_hover_color  = get_theme_mod( 'energieburcht_cpt_projecten_btn_hover_color',         '#ffffff' );
$btn_hover_bg     = get_theme_mod( 'energieburcht_cpt_projecten_btn_hover_bg',            '#0095c0' );

// ── Customizer values — category tag ─────────────────────────────────────────
$cat_color        = get_theme_mod( 'energieburcht_cpt_projecten_cat_color',               '#003449' );
$cat_bg           = get_theme_mod( 'energieburcht_cpt_projecten_cat_bg',                  '#e0f0f5' );

// ── Customizer values — filter bar ───────────────────────────────────────────
$filter_color        = get_theme_mod( 'energieburcht_cpt_projecten_filter_color',         '#003449' );
$filter_bg           = get_theme_mod( 'energieburcht_cpt_projecten_filter_bg',            '#f0f4f8' );
$filter_border       = get_theme_mod( 'energieburcht_cpt_projecten_filter_border',        '#EFEFEF' );
$filter_active_color  = get_theme_mod( 'energieburcht_cpt_projecten_filter_active_color', '#ffffff' );
$filter_active_bg    = get_theme_mod( 'energieburcht_cpt_projecten_filter_active_bg',     '#00acdd' );
$filter_active_border = get_theme_mod( 'energieburcht_cpt_projecten_filter_active_border','#00acdd' );

// ── Category terms for the filter bar ────────────────────────────────────────
$filter_terms = get_terms( array(
	'taxonomy'   => 'projecten-categorie',
	'hide_empty' => true,
	'orderby'    => 'name',
	'order'      => 'ASC',
) );
$has_filters = ! empty( $filter_terms ) && ! is_wp_error( $filter_terms );

get_header();
?>

<style>
.archive-projecten {
	--projecten-item-bg:              <?php echo esc_attr( $item_bg ); ?>;
	--projecten-title-color:          <?php echo esc_attr( $title_color ); ?>;
	--projecten-excerpt-color:        <?php echo esc_attr( $excerpt_color ); ?>;
	--projecten-btn-color:            <?php echo esc_attr( $btn_color ); ?>;
	--projecten-btn-bg:               <?php echo esc_attr( $btn_bg ); ?>;
	--projecten-btn-hover-color:      <?php echo esc_attr( $btn_hover_color ); ?>;
	--projecten-btn-hover-bg:         <?php echo esc_attr( $btn_hover_bg ); ?>;
	--projecten-cat-color:            <?php echo esc_attr( $cat_color ); ?>;
	--projecten-cat-bg:               <?php echo esc_attr( $cat_bg ); ?>;
	--projecten-filter-color:         <?php echo esc_attr( $filter_color ); ?>;
	--projecten-filter-bg:            <?php echo esc_attr( $filter_bg ); ?>;
	--projecten-filter-border:        <?php echo esc_attr( $filter_border ); ?>;
	--projecten-filter-active-color:  <?php echo esc_attr( $filter_active_color ); ?>;
	--projecten-filter-active-bg:     <?php echo esc_attr( $filter_active_bg ); ?>;
	--projecten-filter-active-border: <?php echo esc_attr( $filter_active_border ); ?>;
}
</style>

<main id="primary" class="site-main archive-projecten">

	<?php get_template_part( 'parts/hero-archive', 'projecten' ); ?>

	<?php get_template_part( 'parts/breadcrumbs' ); ?>

	<div class="container boxed-content">

		<?php if ( $has_filters ) : ?>
		<div class="projecten-filter-bar" role="group" aria-label="<?php esc_attr_e( 'Filter projecten op categorie', 'energieburcht' ); ?>">
			<button class="projecten-filter-btn is-active" data-term-id="0">
				<?php esc_html_e( 'Alle', 'energieburcht' ); ?>
			</button>
			<?php foreach ( $filter_terms as $term ) : ?>
				<button class="projecten-filter-btn" data-term-id="<?php echo esc_attr( $term->term_id ); ?>">
					<?php echo esc_html( $term->name ); ?>
				</button>
			<?php endforeach; ?>
		</div><!-- .projecten-filter-bar -->
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>

			<div id="projecten-grid" class="projecten-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'parts/projecten-item' ); ?>
				<?php endwhile; ?>
			</div><!-- #projecten-grid -->

			<?php
			$total_pages = $GLOBALS['wp_query']->max_num_pages;

			if ( $total_pages > 1 ) :
				$paged = max( 1, get_query_var( 'paged' ) );
			?>
			<nav id="projecten-pagination" class="projecten-pagination" aria-label="<?php esc_attr_e( 'Projecten navigation', 'energieburcht' ); ?>">
				<?php
				echo paginate_links( array(
					'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
					'format'    => '?paged=%#%',
					'current'   => $paged,
					'total'     => $total_pages,
					'prev_text' => '<span class="projecten-pagination__arrow" aria-hidden="true">&larr;</span><span class="screen-reader-text">' . esc_html__( 'Previous', 'energieburcht' ) . '</span>',
					'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next', 'energieburcht' ) . '</span><span class="projecten-pagination__arrow" aria-hidden="true">&rarr;</span>',
					'type'      => 'plain',
				) );
				?>
			</nav><!-- #projecten-pagination -->
			<?php endif; ?>

		<?php else : ?>

			<div id="projecten-grid" class="projecten-grid"></div>
			<?php get_template_part( 'parts/content', 'none' ); ?>

		<?php endif; ?>

	</div><!-- .container -->

</main><!-- #primary -->

<?php
get_footer();
