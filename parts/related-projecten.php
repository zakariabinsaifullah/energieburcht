<?php
/**
 * Template part for the "Meer projecten?" related posts section.
 *
 * Displays 3 randomly-chosen published Projecten posts (excluding the
 * current post) followed by a link to the full Projecten archive.
 * Reuses parts/projecten-item.php for each card so styles stay consistent.
 *
 * All text and colour values come from the Customizer section
 * Projecten → Related Projects.
 *
 * Loaded by single-projecten.php via:
 *   get_template_part( 'parts/related', 'projecten' )
 *
 * @package Energieburcht
 */

$related_query = new WP_Query(
	array(
		'post_type'      => 'projecten',
		'posts_per_page' => 3,
		'post__not_in'   => array( get_the_ID() ),
		'orderby'        => 'rand',
		'post_status'    => 'publish',
	)
);

if ( ! $related_query->have_posts() ) {
	return;
}

// ── Customizer values ─────────────────────────────────────────────────────────
$bg           = get_theme_mod( 'energieburcht_cpt_projecten_related_bg',              '#f8f9fa' );
$title_text   = get_theme_mod( 'energieburcht_cpt_projecten_related_title',           __( 'Meer projecten?', 'energieburcht' ) );
$btn_label    = get_theme_mod( 'energieburcht_cpt_projecten_related_btn_label',       __( 'Lees meer', 'energieburcht' ) );
$title_color  = get_theme_mod( 'energieburcht_cpt_projecten_related_title_color',     '#003449' );
$btn_color    = get_theme_mod( 'energieburcht_cpt_projecten_related_btn_color',       '#00acdd' );
$btn_hover    = get_theme_mod( 'energieburcht_cpt_projecten_related_btn_hover_color', '#0095c0' );

$archive_url  = get_post_type_archive_link( 'projecten' );
?>

<section
	class="related-projecten"
	aria-label="<?php esc_attr_e( 'Meer projecten', 'energieburcht' ); ?>"
	style="
		background-color: <?php echo esc_attr( $bg ); ?>;
		--related-title-color: <?php echo esc_attr( $title_color ); ?>;
		--related-btn-color: <?php echo esc_attr( $btn_color ); ?>;
		--related-btn-hover-color: <?php echo esc_attr( $btn_hover ); ?>;
	"
>
	<div class="container">

		<div class="related-projecten__header">
			<?php if ( ! empty( $title_text ) ) : ?>
				<h2 class="related-projecten__title">
					<?php echo esc_html( $title_text ); ?>
				</h2>
			<?php endif; ?>

			<?php if ( $archive_url && ! empty( $btn_label ) ) : ?>
				<a href="<?php echo esc_url( $archive_url ); ?>" class="related-projecten__more">
					<?php echo esc_html( $btn_label ); ?>
					<span class="related-projecten__more-arrow" aria-hidden="true">&rarr;</span>
				</a>
			<?php endif; ?>
		</div>

		<div class="projecten-grid related-projecten__grid">
			<?php
			while ( $related_query->have_posts() ) {
				$related_query->the_post();
				get_template_part( 'parts/projecten-item' );
			}
			wp_reset_postdata();
			?>
		</div>

	</div>
</section>
