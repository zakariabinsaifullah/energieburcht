<?php
/**
 * The template for displaying the Kennisitems archive.
 *
 * Posts are grouped by their kennisitems-categorie taxonomy term. Each category
 * renders as a full-width row with a two-column header (category title left,
 * "Lees meer" archive link right) above a 3-column grid of the 3 latest posts
 * for that category.
 *
 * WordPress template hierarchy resolves this file for any request to the
 * 'kennisitems' post type archive before falling back to archive.php or index.php.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Energieburcht
 */

// ── Customizer values — card styles ───────────────────────────────────────────
$item_bg         = get_theme_mod( 'energieburcht_cpt_kennisitems_item_bg',              '#ffffff' );
$title_color     = get_theme_mod( 'energieburcht_cpt_kennisitems_title_color',          '#003449' );
$excerpt_color   = get_theme_mod( 'energieburcht_cpt_kennisitems_excerpt_color',        '#212529' );
$btn_color       = get_theme_mod( 'energieburcht_cpt_kennisitems_btn_color',            '#00acdd' );
$btn_hover_color = get_theme_mod( 'energieburcht_cpt_kennisitems_btn_hover_color',      '#ffffff' );
$btn_hover_bg    = get_theme_mod( 'energieburcht_cpt_kennisitems_btn_hover_bg',         '#00acdd' );

// ── Customizer values — category header ───────────────────────────────────────
$cat_title_color      = get_theme_mod( 'energieburcht_cpt_kennisitems_cat_title_color',      '#003449' );
$cat_link_color       = get_theme_mod( 'energieburcht_cpt_kennisitems_cat_link_color',        '#00acdd' );
$cat_link_hover_color = get_theme_mod( 'energieburcht_cpt_kennisitems_cat_link_hover_color',  '#0095c0' );

// ── All non-empty kennisitems categories ──────────────────────────────────────
$kennisitems_cats = get_terms( array(
	'taxonomy'   => 'kennisitems-categorie',
	'hide_empty' => true,
	'orderby'    => 'name',
	'order'      => 'ASC',
) );

$has_categories = ! empty( $kennisitems_cats ) && ! is_wp_error( $kennisitems_cats );

get_header();
?>

<style>
.archive-kennisitems {
	--kennisitems-item-bg:              <?php echo esc_attr( $item_bg ); ?>;
	--kennisitems-title-color:          <?php echo esc_attr( $title_color ); ?>;
	--kennisitems-excerpt-color:        <?php echo esc_attr( $excerpt_color ); ?>;
	--kennisitems-btn-color:            <?php echo esc_attr( $btn_color ); ?>;
	--kennisitems-btn-hover-color:      <?php echo esc_attr( $btn_hover_color ); ?>;
	--kennisitems-btn-hover-bg:         <?php echo esc_attr( $btn_hover_bg ); ?>;
	--kennisitems-cat-title-color:      <?php echo esc_attr( $cat_title_color ); ?>;
	--kennisitems-cat-link-color:       <?php echo esc_attr( $cat_link_color ); ?>;
	--kennisitems-cat-link-hover-color: <?php echo esc_attr( $cat_link_hover_color ); ?>;
}
</style>

<main id="primary" class="site-main archive-kennisitems">

	<?php get_template_part( 'parts/hero-archive', 'kennisitems' ); ?>

	<?php get_template_part( 'parts/breadcrumbs' ); ?>

	<div class="container boxed-content">

		<?php if ( $has_categories ) : ?>

			<?php foreach ( $kennisitems_cats as $cat ) : ?>

				<?php
				$cat_query = new WP_Query( array(
					'post_type'      => 'kennisitems',
					'posts_per_page' => 3,
					'orderby'        => 'date',
					'order'          => 'DESC',
					'post_status'    => 'publish',
					'tax_query'      => array(
						array(
							'taxonomy' => 'kennisitems-categorie',
							'field'    => 'term_id',
							'terms'    => $cat->term_id,
						),
					),
				) );

				if ( ! $cat_query->have_posts() ) {
					continue;
				}

				$cat_archive_url = get_term_link( $cat );
				?>

				<div class="kennisitems-categorie-section">

					<div class="kennisitems-categorie-header">
						<h2 class="kennisitems-cat-title">
							<?php echo esc_html( $cat->name ); ?>
						</h2>
						<?php if ( ! is_wp_error( $cat_archive_url ) ) : ?>
							<a href="<?php echo esc_url( $cat_archive_url ); ?>" class="kennisitems-cat-more">
								<?php esc_html_e( 'Lees meer', 'energieburcht' ); ?>
								<span class="kennisitems-cat-more__arrow" aria-hidden="true">&rarr;</span>
							</a>
						<?php endif; ?>
					</div><!-- .kennisitems-categorie-header -->

					<div class="kennisitems-grid">
						<?php
						while ( $cat_query->have_posts() ) {
							$cat_query->the_post();
							get_template_part( 'parts/kennisitems-item' );
						}
						wp_reset_postdata();
						?>
					</div><!-- .kennisitems-grid -->

				</div><!-- .kennisitems-categorie-section -->

			<?php endforeach; ?>

		<?php else : ?>

			<?php get_template_part( 'parts/content', 'none' ); ?>

		<?php endif; ?>

	</div><!-- .container -->

</main><!-- #primary -->

<?php
get_footer();
