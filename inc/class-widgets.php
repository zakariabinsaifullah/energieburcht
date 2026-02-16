<?php
/**
 * Widget Registration Class
 *
 * @package energieburcht
 */

class Energieburcht_Widgets {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'widgets_init', array( $this, 'init' ) );
    }

    /**
     * Register widget areas.
     */
    public function init() {
        // Header Right
        register_sidebar(
            array(
                'name'          => esc_html__( 'Header Right', 'energieburcht' ),
                'id'            => 'header-right',
                'description'   => esc_html__( 'Add widgets here for the top right header area (Search, CTA, Language).', 'energieburcht' ),
                'before_widget' => '<section id="%1$s" class="widget %2$s">',
                'after_widget'  => '</section>',
                'before_title'  => '<h2 class="screen-reader-text">',
                'after_title'   => '</h2>',
            )
        );

        // Footer Column 1
        register_sidebar(
            array(
                'name'          => esc_html__( 'Footer Column 1', 'energieburcht' ),
                'id'            => 'footer-1',
                'description'   => esc_html__( 'Add widgets here for the first footer column.', 'energieburcht' ),
                'before_widget' => '<section id="%1$s" class="widget %2$s">',
                'after_widget'  => '</section>',
                'before_title'  => '<h2 class="widget-title">',
                'after_title'   => '</h2>',
            )
        );

        // Footer Column 2
        register_sidebar(
            array(
                'name'          => esc_html__( 'Footer Column 2', 'energieburcht' ),
                'id'            => 'footer-2',
                'description'   => esc_html__( 'Add widgets here for the second footer column.', 'energieburcht' ),
                'before_widget' => '<section id="%1$s" class="widget %2$s">',
                'after_widget'  => '</section>',
                'before_title'  => '<h2 class="widget-title">',
                'after_title'   => '</h2>',
            )
        );

        // Footer Column 3
        register_sidebar(
            array(
                'name'          => esc_html__( 'Footer Column 3', 'energieburcht' ),
                'id'            => 'footer-3',
                'description'   => esc_html__( 'Add widgets here for the third footer column.', 'energieburcht' ),
                'before_widget' => '<section id="%1$s" class="widget %2$s">',
                'after_widget'  => '</section>',
                'before_title'  => '<h2 class="widget-title">',
                'after_title'   => '</h2>',
            )
        );

        // Footer Column 4
        register_sidebar(
            array(
                'name'          => esc_html__( 'Footer Column 4', 'energieburcht' ),
                'id'            => 'footer-4',
                'description'   => esc_html__( 'Add widgets here for the fourth footer column.', 'energieburcht' ),
                'before_widget' => '<section id="%1$s" class="widget %2$s">',
                'after_widget'  => '</section>',
                'before_title'  => '<h2 class="widget-title">',
                'after_title'   => '</h2>',
            )
        );


    }
}
