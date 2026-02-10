<?php
/**
 * Enqueue Class
 *
 * @package energieburcht
 */

class Energieburcht_Enqueue {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    /**
     * Enqueue scripts and styles.
     */
    public function enqueue_scripts() {
        // Enqueue the main stylesheet.
        wp_enqueue_style( 'energieburcht-style', get_stylesheet_uri(), array(), ENERGIEBURCHT_VERSION );

        // Enqueue main CSS file if it exists.
        if ( file_exists( get_template_directory() . '/assets/css/main.css' ) ) {
            wp_enqueue_style( 'energieburcht-main-css', get_template_directory_uri() . '/assets/css/main.css', array(), ENERGIEBURCHT_VERSION );
        }

        // Enqueue main JS file if it exists.
        if ( file_exists( get_template_directory() . '/assets/js/main.js' ) ) {
            wp_enqueue_script( 'energieburcht-main', get_template_directory_uri() . '/assets/js/main.js', array(), ENERGIEBURCHT_VERSION, true );
        }
    }
}
