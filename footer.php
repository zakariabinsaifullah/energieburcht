<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @package energieburcht
 */


$footer_bg_color   = get_theme_mod( 'energieburcht_footer_bg_color' );
$footer_text_color = get_theme_mod( 'energieburcht_footer_text_color' );
$footer_link_color = get_theme_mod( 'energieburcht_footer_link_color' );

$footer_gap_data = get_theme_mod( 'energieburcht_footer_gap' );
$footer_gap_desktop = 50;
$footer_gap_tablet  = 30;
$footer_gap_mobile  = 20;
$footer_gap_unit    = 'px';

// Parse the JSON data from the new control
if ( $footer_gap_data ) {
	// If it's a JSON string, decode it
	if ( is_string( $footer_gap_data ) ) {
		$decoded = json_decode( $footer_gap_data, true );
		if ( is_array( $decoded ) ) {
			$footer_gap_data = $decoded;
		}
	}
	
	if ( is_array( $footer_gap_data ) ) {
		$footer_gap_desktop = isset( $footer_gap_data['desktop'] ) && '' !== $footer_gap_data['desktop'] ? $footer_gap_data['desktop'] : $footer_gap_desktop;
		$footer_gap_tablet  = isset( $footer_gap_data['tablet'] ) && '' !== $footer_gap_data['tablet'] ? $footer_gap_data['tablet'] : $footer_gap_tablet;
		$footer_gap_mobile  = isset( $footer_gap_data['mobile'] ) && '' !== $footer_gap_data['mobile'] ? $footer_gap_data['mobile'] : $footer_gap_mobile;
		$footer_gap_unit    = isset( $footer_gap_data['unit'] ) ? $footer_gap_data['unit'] : 'px';
	}
} else {
    // Fallback/Migration: Try to get old individual values if the new one hasn't been saved yet
	$footer_gap_desktop = get_theme_mod( 'energieburcht_footer_gap_desktop', 50 );
	$footer_gap_tablet  = get_theme_mod( 'energieburcht_footer_gap_tablet', 30 );
	$footer_gap_mobile  = get_theme_mod( 'energieburcht_footer_gap_mobile', 20 );
}

$style = '';
if ( $footer_bg_color ) {
    $style .= '--footer-bg-color: ' . esc_attr( $footer_bg_color ) . ';';
}
if ( $footer_text_color ) {
    $style .= '--footer-text-color: ' . esc_attr( $footer_text_color ) . ';';
}
if ( $footer_link_color ) {
    $style .= '--footer-link-color: ' . esc_attr( $footer_link_color ) . ';';
}
if ( '' !== $footer_gap_desktop ) {
    $style .= '--footer-widget-gap: ' . intval( $footer_gap_desktop ) . esc_attr( $footer_gap_unit ) . ';';
}
if ( '' !== $footer_gap_tablet ) {
    $style .= '--footer-widget-gap-tablet: ' . intval( $footer_gap_tablet ) . esc_attr( $footer_gap_unit ) . ';';
}
if ( '' !== $footer_gap_mobile ) {
    $style .= '--footer-widget-gap-mobile: ' . intval( $footer_gap_mobile ) . esc_attr( $footer_gap_unit ) . ';';
}

$copyright_text_color = get_theme_mod( 'energieburcht_copyright_text_color' );
?>

    <div class="footer-widgets-wrapper" <?php if ( $style ) : ?> style="<?php echo esc_attr( $style ); ?>" <?php endif; ?>>

        <div class="container">
            <?php
            $footer_columns = get_theme_mod( 'energieburcht_footer_columns', 4 );
            ?>
            <div class="footer-widgets-columns columns-<?php echo intval( $footer_columns ); ?>">
                <?php
                for ( $i = 1; $i <= $footer_columns; $i++ ) {
                    if ( is_active_sidebar( 'footer-' . $i ) ) {
                        ?>
                        <div class="footer-column footer-column-<?php echo intval( $i ); ?>">
                            <?php dynamic_sidebar( 'footer-' . $i ); ?>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
<?php
    $copyright_bg_color = get_theme_mod( 'energieburcht_copyright_bg_color' );
    $copyright_border_top_color = get_theme_mod( 'energieburcht_copyright_border_top_color' );

    $copyright_style = '';
    if ( $copyright_text_color ) {
        $copyright_style .= '--copyright-text-color: ' . esc_attr( $copyright_text_color ) . ';';
    }
    if ( $copyright_bg_color ) {
        $copyright_style .= '--copyright-bg-color: ' . esc_attr( $copyright_bg_color ) . ';';
    }

    // Back to Top Styles
    $back_to_top_enable = get_theme_mod( 'energieburcht_back_to_top_enable', false );
    $back_to_top_bg     = get_theme_mod( 'energieburcht_back_to_top_bg_color', '#ffffff' );
    $back_to_top_icon   = get_theme_mod( 'energieburcht_back_to_top_icon_color', '#003449' );
    
    $back_to_top_style = '';
    if ( $back_to_top_enable ) {
        if ( $back_to_top_bg ) {
            $back_to_top_style .= '--back-to-top-bg: ' . esc_attr( $back_to_top_bg ) . ';';
        }
        if ( $back_to_top_icon ) {
            $back_to_top_style .= '--back-to-top-color: ' . esc_attr( $back_to_top_icon ) . ';';
        }
    }
?>

	<footer id="colophon" class="site-footer" <?php if ( $copyright_style ) : ?> style="<?php echo esc_attr( $copyright_style ); ?>" <?php endif; ?>>
        <?php
        $copyright_text = get_theme_mod( 'energieburcht_copyright_text', '[copyright] Energieburcht - [year]' );
        
        // Process placeholders
        // Replace [copyright] token with the © entity and [year] with the
        // current 4-digit year. gmdate() is used instead of date() because
        // WordPress expects timezone-agnostic date functions in theme code.
        $copyright_text = str_replace( '[copyright]', '&copy;', $copyright_text );
        $copyright_text = str_replace( '[year]', gmdate( 'Y' ), $copyright_text );
        ?>
		<div class="site-info">
            <div class="container">
                <div class="copyright-bar">
                    <div class="copyright-text">
                        <?php echo wp_kses_post( $copyright_text ); ?>
                    </div>
                    
                    <div class="copyright-menu">
                        <?php
                            wp_nav_menu( array(
                                'theme_location' => 'copyright-menu',
                                'menu_id'        => 'copyright-menu',
                                'depth'          => 1,
                                'fallback_cb'    => false,
                            ) );
                        ?>
                    </div>
                </div>
            </div>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php if ( $back_to_top_enable ) : ?>
    <a href="#" id="back-to-top" title="<?php esc_attr_e( 'Back to Top', 'energieburcht' ); ?>" <?php if ( $back_to_top_style ) : ?> style="<?php echo esc_attr( $back_to_top_style ); ?>" <?php endif; ?>>
        <span class="dashicons dashicons-arrow-up-alt2"></span>
    </a>
<?php endif; ?>

<?php wp_footer(); ?>

</body>
</html>
