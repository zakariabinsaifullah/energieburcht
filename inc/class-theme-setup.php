<?php
/**
 * Theme Setup Class
 *
 * @package energieburcht
 */

class Energieburcht_Theme_Setup {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'after_setup_theme', array( $this, 'setup' ) );
    }

    /**
     * Sets up theme defaults and registers support for various WordPress features.
     */
    public function setup() {
        // Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );

        // Let WordPress manage the document title.
        add_theme_support( 'title-tag' );

        // Enable support for Post Thumbnails on posts and pages.
        add_theme_support( 'post-thumbnails' );

        // Register menus.
        register_nav_menus(
            array(
                'primary' => esc_html__( 'Primary Menu', 'energieburcht' ),
                'footer'  => esc_html__( 'Footer Menu', 'energieburcht' ),
                'copyright-menu' => esc_html__( 'Copyright Menu', 'energieburcht' ),
            )
        );

        // Switch default core markup for search form, comment form, and comments to output valid HTML5.
        add_theme_support(
            'html5',
            array(
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
                'style',
                'script',
            )
        );

        // Add theme support for selective refresh for widgets.
        add_theme_support( 'customize-selective-refresh-widgets' );

        // Add support for core custom logo.
        add_theme_support(
            'custom-logo',
            array(
                'height'      => 250,
                'width'       => 250,
                'flex-width'  => true,
                'flex-height' => true,
            )
        );
        
        // Add support for editor styles.
        add_theme_support( 'editor-styles' );
        add_editor_style( 'assets/css/editor-style.css' );
    }
}
