<?php
/**
 * Template part: Global Taxonomy Archive Hero.
 *
 * Loaded by taxonomy.php via:
 *   get_template_part( 'parts/hero-archive', 'taxonomy' )
 *
 * Title and description are pulled directly from the queried taxonomy term.
 * All colour and background options come from the Customizer
 * "Archive → Hero Section" panel.
 *
 * @package Energieburcht
 */

// Bail early if the hero is disabled.
$hero_enable = get_theme_mod( 'energieburcht_tax_hero_enable', true );

if ( ! $hero_enable ) {
	return;
}

// ── Term data ─────────────────────────────────────────────────────────────────
$term = get_queried_object();

if ( ! $term instanceof WP_Term ) {
	return;
}

$term_name        = $term->name;
$term_description = term_description( $term->term_id, $term->taxonomy );

// ── Typography colours ────────────────────────────────────────────────────────
$title_color = get_theme_mod( 'energieburcht_tax_hero_title_color', '#ffffff' );
$desc_color  = get_theme_mod( 'energieburcht_tax_hero_desc_color',  '#ffffff' );

// ── Background settings ───────────────────────────────────────────────────────
$bg_type       = get_theme_mod( 'energieburcht_tax_hero_bg_type',        'color' );
$bg_color      = get_theme_mod( 'energieburcht_tax_hero_bg_color',       '#003449' );
$grad_start    = get_theme_mod( 'energieburcht_tax_hero_gradient_start', '#003449' );
$grad_end      = get_theme_mod( 'energieburcht_tax_hero_gradient_end',   '#00ACDD' );
$bg_image      = get_theme_mod( 'energieburcht_tax_hero_bg_image',       '' );
$overlay_color = get_theme_mod( 'energieburcht_tax_hero_overlay_color',  'rgba(0, 52, 73, 0.7)' );

// ── Build inline section style ────────────────────────────────────────────────
$section_style = '';
$overlay_style = '';
$has_overlay   = false;

if ( 'color' === $bg_type ) {
	$section_style = 'background-color:' . esc_attr( $bg_color ) . ';';
} elseif ( 'gradient' === $bg_type ) {
	$section_style = 'background:linear-gradient(135deg,' . esc_attr( $grad_start ) . ' 0%,' . esc_attr( $grad_end ) . ' 100%);';
} elseif ( 'image' === $bg_type && ! empty( $bg_image ) ) {
	$section_style = 'background-image:url(' . esc_url( $bg_image ) . ');background-size:cover;background-position:center;';
	$overlay_style = 'background-color:' . esc_attr( $overlay_color ) . ';';
	$has_overlay   = true;
} else {
	// Fallback to solid colour when image type is selected but no image is uploaded.
	$section_style = 'background-color:' . esc_attr( $bg_color ) . ';';
}
?>

<section id="tax-archive-hero" class="page-hero tax-archive-hero" style="<?php echo $section_style; // Already escaped above. ?>">

	<?php if ( $has_overlay ) : ?>
		<div class="page-hero-overlay" style="<?php echo $overlay_style; // Already escaped above. ?>"></div>
	<?php endif; ?>

	<div class="container">
		<div class="page-hero-content">

			<h1 class="page-hero-title"><?php echo esc_html( $term_name ); ?></h1>

			<?php if ( ! empty( $term_description ) ) : ?>
				<div class="page-hero-excerpt">
					<?php echo wp_kses_post( $term_description ); ?>
				</div>
			<?php endif; ?>

		</div><!-- .page-hero-content -->
	</div><!-- .container -->

	<style>
		#tax-archive-hero {
			position: relative;
			padding: 80px 0;
		}
		#tax-archive-hero .page-hero-overlay {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			z-index: 1;
		}
		#tax-archive-hero .container {
			position: relative;
			z-index: 2;
		}
		#tax-archive-hero .page-hero-title {
			color: <?php echo esc_attr( $title_color ); ?>;
		}
		#tax-archive-hero .page-hero-excerpt {
			max-width: 800px;
			color: <?php echo esc_attr( $desc_color ); ?>;
			font-size: var(--eb-typography-excerpt);
		}
	</style>

</section><!-- #tax-archive-hero -->
