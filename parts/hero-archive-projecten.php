<?php
/**
 * Template part for displaying the Projecten Archive Hero Area.
 *
 * Loaded by archive-projecten.php via:
 *   get_template_part( 'parts/hero-archive', 'projecten' )
 *
 * All content (title, description, CTA) and every style option come from
 * Customizer settings — there is no per-post source because this is an archive.
 *
 * @package Energieburcht
 */

// Bail early if the hero is disabled.
$hero_enable = get_theme_mod( 'energieburcht_cpt_projecten_hero_enable', true );

if ( ! $hero_enable ) {
	return;
}

// ── Content settings ──────────────────────────────────────────────────────────
$show_title       = get_theme_mod( 'energieburcht_cpt_projecten_hero_show_title', true );
$title            = get_theme_mod( 'energieburcht_cpt_projecten_hero_title', __( 'Projecten', 'energieburcht' ) );
$show_description = get_theme_mod( 'energieburcht_cpt_projecten_hero_show_description', true );
$description      = get_theme_mod( 'energieburcht_cpt_projecten_hero_description', '' );
$show_cta         = get_theme_mod( 'energieburcht_cpt_projecten_hero_show_cta', true );
$cta_text         = get_theme_mod( 'energieburcht_cpt_projecten_hero_cta_text', '' );
$cta_url          = get_theme_mod( 'energieburcht_cpt_projecten_hero_cta_url', '#' );

// ── Typography colors ─────────────────────────────────────────────────────────
$title_color       = get_theme_mod( 'energieburcht_cpt_projecten_hero_title_color', '#ffffff' );
$description_color = get_theme_mod( 'energieburcht_cpt_projecten_hero_description_color', '#ffffff' );

// ── CTA colors ────────────────────────────────────────────────────────────────
$cta_text_color       = get_theme_mod( 'energieburcht_cpt_projecten_hero_cta_text_color', '#ffffff' );
$cta_bg_color         = get_theme_mod( 'energieburcht_cpt_projecten_hero_cta_bg_color', '#00acdd' );
$cta_text_hover_color = get_theme_mod( 'energieburcht_cpt_projecten_hero_cta_text_hover_color', '#ffffff' );
$cta_bg_hover_color   = get_theme_mod( 'energieburcht_cpt_projecten_hero_cta_bg_hover_color', '#26b8e2' );

// ── Background settings ───────────────────────────────────────────────────────
$bg_type      = get_theme_mod( 'energieburcht_cpt_projecten_hero_bg_type', 'color' );
$bg_color     = get_theme_mod( 'energieburcht_cpt_projecten_hero_bg_color', '#003449' );
$grad_start   = get_theme_mod( 'energieburcht_cpt_projecten_hero_gradient_start', '#003449' );
$grad_end     = get_theme_mod( 'energieburcht_cpt_projecten_hero_gradient_end', '#00ACDD' );
$bg_image     = get_theme_mod( 'energieburcht_cpt_projecten_hero_bg_image', '' );
$overlay_color = get_theme_mod( 'energieburcht_cpt_projecten_hero_overlay_color', 'rgba(0, 52, 73, 0.7)' );

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
	// Fallback to solid color when image type is selected but no image is uploaded.
	$section_style = 'background-color:' . esc_attr( $bg_color ) . ';';
}
?>

<section id="projecten-archive-hero" class="page-hero projecten-archive-hero" style="<?php echo $section_style; // Already escaped above. ?>">

	<?php if ( $has_overlay ) : ?>
		<div class="page-hero-overlay" style="<?php echo $overlay_style; // Already escaped above. ?>"></div>
	<?php endif; ?>

	<div class="container">
		<div class="page-hero-content">

			<?php if ( $show_title && ! empty( $title ) ) : ?>
				<h1 class="page-hero-title"><?php echo wp_kses_post( $title ); ?></h1>
			<?php endif; ?>

			<?php if ( $show_description && ! empty( $description ) ) : ?>
				<div class="page-hero-excerpt">
					<?php echo wp_kses_post( $description ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $show_cta && ! empty( $cta_text ) ) : ?>
				<div class="page-hero-cta">
					<a href="<?php echo esc_url( $cta_url ); ?>" class="button hero-btn">
						<?php echo esc_html( $cta_text ); ?>
					</a>
				</div>
			<?php endif; ?>

		</div><!-- .page-hero-content -->
	</div><!-- .container -->

	<style>
		#projecten-archive-hero {
			position: relative;
			padding: 80px 0;
		}
		#projecten-archive-hero .page-hero-overlay {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			z-index: 1;
		}
		#projecten-archive-hero .container {
			position: relative;
			z-index: 2;
		}
		#projecten-archive-hero .page-hero-title {
			color: <?php echo esc_attr( $title_color ); ?>;
		}
		#projecten-archive-hero .page-hero-excerpt {
			max-width: 800px;
			color: <?php echo esc_attr( $description_color ); ?>;
			font-size: var(--eb-typography-excerpt);
		}
		#projecten-archive-hero .hero-btn {
			background-color: <?php echo esc_attr( $cta_bg_color ); ?>;
			color: <?php echo esc_attr( $cta_text_color ); ?>;
		}
		#projecten-archive-hero .hero-btn:hover {
			background-color: <?php echo esc_attr( $cta_bg_hover_color ); ?>;
			color: <?php echo esc_attr( $cta_text_hover_color ); ?>;
		}
	</style>

</section><!-- #projecten-archive-hero -->
