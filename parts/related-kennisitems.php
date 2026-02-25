<?php
/**
 * Template part for the "Gerelateerde kennisitems" related posts section.
 *
 * Displays 3 posts from the same kennisitems-categorie taxonomy terms as the
 * current post (random order, excluding the current post). Falls back to 3
 * random kennisitems posts if no category-matched posts are found.
 *
 * Reuses parts/kennisitems-item.php for each card so styles stay consistent
 * with the archive grid.
 *
 * Loaded by single-kennisitems.php via:
 *   get_template_part( 'parts/related', 'kennisitems' )
 *
 * @package Energieburcht
 */

// ── Determine current post's categories ───────────────────────────────────────
$current_terms = get_the_terms( get_the_ID(), 'kennisitems-categorie' );
$term_ids      = ( $current_terms && ! is_wp_error( $current_terms ) )
	? wp_list_pluck( $current_terms, 'term_id' )
	: array();

// ── Query: same category first, then fallback ─────────────────────────────────
$query_args = array(
	'post_type'      => 'kennisitems',
	'posts_per_page' => 3,
	'post__not_in'   => array( get_the_ID() ),
	'orderby'        => 'rand',
	'post_status'    => 'publish',
);

if ( ! empty( $term_ids ) ) {
	$query_args['tax_query'] = array(
		array(
			'taxonomy' => 'kennisitems-categorie',
			'field'    => 'term_id',
			'terms'    => $term_ids,
		),
	);
}

$related_query = new WP_Query( $query_args );

// Fallback: any kennisitems when no category matches.
if ( ! $related_query->have_posts() && ! empty( $term_ids ) ) {
	unset( $query_args['tax_query'] );
	$related_query = new WP_Query( $query_args );
}

if ( ! $related_query->have_posts() ) {
	return;
}

// ── Customizer values: section header ─────────────────────────────────────────
$bg          = get_theme_mod( 'energieburcht_cpt_kennisitems_related_bg',              '#f8f9fa' );
$title_text  = get_theme_mod( 'energieburcht_cpt_kennisitems_related_title',           __( 'Gerelateerde kennisitems', 'energieburcht' ) );
$btn_label   = get_theme_mod( 'energieburcht_cpt_kennisitems_related_btn_label',       __( 'Lees meer', 'energieburcht' ) );
$title_color = get_theme_mod( 'energieburcht_cpt_kennisitems_related_title_color',     '#003449' );
$btn_color   = get_theme_mod( 'energieburcht_cpt_kennisitems_related_btn_color',       '#00acdd' );
$btn_hover   = get_theme_mod( 'energieburcht_cpt_kennisitems_related_btn_hover_color', '#0095c0' );

// ── Customizer values: card styles (same settings as the archive page) ─────────
$item_bg         = get_theme_mod( 'energieburcht_cpt_kennisitems_item_bg',         '#ffffff' );
$card_title      = get_theme_mod( 'energieburcht_cpt_kennisitems_title_color',     '#003449' );
$excerpt_color   = get_theme_mod( 'energieburcht_cpt_kennisitems_excerpt_color',   '#212529' );
$card_btn        = get_theme_mod( 'energieburcht_cpt_kennisitems_btn_color',       '#00acdd' );
$card_btn_hover  = get_theme_mod( 'energieburcht_cpt_kennisitems_btn_hover_color', '#ffffff' );
$card_btn_hov_bg = get_theme_mod( 'energieburcht_cpt_kennisitems_btn_hover_bg',    '#00acdd' );

$archive_url = get_post_type_archive_link( 'kennisitems' );
?>

<section
	class="related-kennisitems"
	aria-label="<?php esc_attr_e( 'Gerelateerde kennisitems', 'energieburcht' ); ?>"
	style="
		background-color: <?php echo esc_attr( $bg ); ?>;
		--ki-related-title-color:        <?php echo esc_attr( $title_color ); ?>;
		--ki-related-btn-color:          <?php echo esc_attr( $btn_color ); ?>;
		--ki-related-btn-hover-color:    <?php echo esc_attr( $btn_hover ); ?>;
		--kennisitems-item-bg:           <?php echo esc_attr( $item_bg ); ?>;
		--kennisitems-title-color:       <?php echo esc_attr( $card_title ); ?>;
		--kennisitems-excerpt-color:     <?php echo esc_attr( $excerpt_color ); ?>;
		--kennisitems-btn-color:         <?php echo esc_attr( $card_btn ); ?>;
		--kennisitems-btn-hover-color:   <?php echo esc_attr( $card_btn_hover ); ?>;
		--kennisitems-btn-hover-bg:      <?php echo esc_attr( $card_btn_hov_bg ); ?>;
	"
>
	<div class="container">

		<div class="related-kennisitems__header">
			<?php if ( ! empty( $title_text ) ) : ?>
				<h2 class="related-kennisitems__title">
					<?php echo esc_html( $title_text ); ?>
				</h2>
			<?php endif; ?>

			<?php if ( $archive_url && ! empty( $btn_label ) ) : ?>
				<a href="<?php echo esc_url( $archive_url ); ?>" class="related-kennisitems__more">
					<?php echo esc_html( $btn_label ); ?>
					<span class="related-kennisitems__more-arrow" aria-hidden="true">&rarr;</span>
				</a>
			<?php endif; ?>
		</div>

		<div class="kennisitems-grid related-kennisitems__grid">
			<?php
			while ( $related_query->have_posts() ) {
				$related_query->the_post();
				get_template_part( 'parts/kennisitems-item' );
			}
			wp_reset_postdata();
			?>
		</div>

	</div>
</section>
