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
    }
}
