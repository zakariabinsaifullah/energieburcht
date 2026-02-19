<?php
/**
 * Template part for displaying the Page Hero Area.
 *
 * @package Energieburcht
 */

// 1. Check Global Enable
$global_enable = get_theme_mod( 'energieburcht_hero_enable', false );

// 2. Check Local Visibility
$local_vis = get_post_meta( get_the_ID(), '_energieburcht_hero_visibility', true );

$should_show = $global_enable;

if ( 'show' === $local_vis ) {
	$should_show = true;
} elseif ( 'hide' === $local_vis ) {
	$should_show = false;
}

if ( ! $should_show ) {
	return;
}

// 3. Get Content Elements
$show_title   = get_theme_mod( 'energieburcht_hero_show_title', true );
$show_excerpt = get_theme_mod( 'energieburcht_hero_show_excerpt', true );
$show_cta     = get_theme_mod( 'energieburcht_hero_show_cta', true );

$title    = get_the_title();
$excerpt  = get_the_excerpt();
$cta_text = get_post_meta( get_the_ID(), '_energieburcht_hero_cta_text', true );
$cta_link = get_post_meta( get_the_ID(), '_energieburcht_hero_cta_link', true );

// 4. Get Style Settings
$bg_type        = get_theme_mod( 'energieburcht_hero_bg_type', 'color' );
$bg_color       = get_theme_mod( 'energieburcht_hero_bg_color', '#003449' );
$grad_start     = get_theme_mod( 'energieburcht_hero_gradient_start', '#003449' );
$grad_end       = get_theme_mod( 'energieburcht_hero_gradient_end', '#00ACDD' );
$overlay_color  = get_theme_mod( 'energieburcht_hero_overlay_color', 'rgba(0, 52, 73, 0.7)' );

// CTA Styles
$cta_text_color       = get_theme_mod( 'energieburcht_hero_cta_text_color', '#ffffff' );
$cta_bg_color         = get_theme_mod( 'energieburcht_hero_cta_bg_color', '#00acdd' );
$cta_text_hover_color = get_theme_mod( 'energieburcht_hero_cta_text_hover_color', '#ffffff' );
$cta_bg_hover_color   = get_theme_mod( 'energieburcht_hero_cta_bg_hover_color', '#26b8e2' );

// Typography Styles
$title_color   = get_theme_mod( 'energieburcht_hero_title_color', '#ffffff' );
$excerpt_color = get_theme_mod( 'energieburcht_hero_excerpt_color', '#ffffff' );

// Build Inline Styles
$style = '';
$overlay_style = '';

if ( 'color' === $bg_type ) {
	$style = "background-color: {$bg_color};";
} elseif ( 'gradient' === $bg_type ) {
	$style = "background: linear-gradient(135deg, {$grad_start} 0%, {$grad_end} 100%);";
} elseif ( 'image' === $bg_type ) {
	if ( has_post_thumbnail() ) {
		$img_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
		$style   = "background-image: url('{$img_url}'); background-size: cover; background-position: center;";
		$overlay_style = "background-color: {$overlay_color};";
	} else {
		// Fallback if no image
		$style = "background-color: {$bg_color};";
	}
}

// Generate unique ID for this hero instance to scope styles (though usually one per page)
$hero_id = 'page-hero-' . get_the_ID();
?>

<section id="<?php echo esc_attr( $hero_id ); ?>" class="page-hero" style="<?php echo esc_attr( $style ); ?>">
	<?php if ( 'image' === $bg_type && has_post_thumbnail() ) : ?>
		<div class="page-hero-overlay" style="<?php echo esc_attr( $overlay_style ); ?>"></div>
	<?php endif; ?>
	
	<div class="container">
		<div class="page-hero-content">
			<?php if ( $show_title ) : ?>
				<h1 class="page-hero-title entry-title"><?php echo wp_kses_post( $title ); ?></h1>
			<?php endif; ?>

			<?php if ( $show_excerpt && has_excerpt() ) : ?>
				<div class="page-hero-excerpt">
					<?php echo wp_kses_post( $excerpt ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $show_cta && ! empty( $cta_text ) && ! empty( $cta_link ) ) : ?>
				<div class="page-hero-cta">
					<a href="<?php echo esc_url( $cta_link ); ?>" class="button hero-btn">
						<?php echo esc_html( $cta_text ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<style>
		#<?php echo esc_attr( $hero_id ); ?> {
			position: relative;
			padding: 80px 0;
			color: #fff;
		}
		#<?php echo esc_attr( $hero_id ); ?> .page-hero-overlay {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			z-index: 1;
		}
		#<?php echo esc_attr( $hero_id ); ?> .container {
			position: relative;
			z-index: 2;
		}
		#<?php echo esc_attr( $hero_id ); ?> .page-hero-title {
			color: <?php echo esc_attr( $title_color ); ?>;
		}
		#<?php echo esc_attr( $hero_id ); ?> .page-hero-excerpt {
			max-width: 800px;
			color: <?php echo esc_attr( $excerpt_color ); ?>;
			font-size: var(--eb-typography-excerpt);
		}
		#<?php echo esc_attr( $hero_id ); ?> .hero-btn {
			background-color: <?php echo esc_attr( $cta_bg_color ); ?>;
			color: <?php echo esc_attr( $cta_text_color ); ?>;
		}
		#<?php echo esc_attr( $hero_id ); ?> .hero-btn:hover {
			background-color: <?php echo esc_attr( $cta_bg_hover_color ); ?>;
			color: <?php echo esc_attr( $cta_text_hover_color ); ?>;
		}
	</style>
</section>
