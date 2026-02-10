<?php
/**
 * Customizer Class
 *
 * @package energieburcht
 */

class Energieburcht_Customizer {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'customize_register', array( $this, 'register_settings' ) );
    }

    /**
     * Register customizer settings.
     *
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     */
    public function register_settings( $wp_customize ) {
        
        // Add Theme Options Panel
        $wp_customize->add_panel( 'energieburcht_theme_options', array(
            'title'       => __( 'Theme Options', 'energieburcht' ),
            'priority'    => 130, // After Widgets
        ) );

        // Add Footer Section
        $wp_customize->add_section( 'energieburcht_footer_options', array(
            'title'       => __( 'Footer', 'energieburcht' ),
            'panel'       => 'energieburcht_theme_options',
            'priority'    => 10,
        ) );

        // Footer Logo Setting
        $wp_customize->add_setting( 'energieburcht_footer_logo', array(
            'default'           => '',
            'sanitize_callback' => 'absint', // Logo is an attachment ID
            'transport'         => 'refresh',
        ) );

        // Footer Logo Control
        $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'energieburcht_footer_logo', array(
            'label'       => __( 'Footer Logo', 'energieburcht' ),
            'section'     => 'energieburcht_footer_options',
            'mime_type'   => 'image',
            'settings'    => 'energieburcht_footer_logo',
        ) ) );

        // Add Header Section
        $wp_customize->add_section( 'energieburcht_header_options', array(
            'title'       => __( 'Header', 'energieburcht' ),
            'panel'       => 'energieburcht_theme_options',
            'priority'    => 5, // Top of panel
        ) );

        // Header Search Enable Setting
        $wp_customize->add_setting( 'energieburcht_header_search_enable', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );

        // Header Search Enable Control
        $wp_customize->add_control( 'energieburcht_header_search_enable', array(
            'label'       => __( 'Enable Header Search', 'energieburcht' ),
            'section'     => 'energieburcht_header_options',
            'type'        => 'checkbox',
            'settings'    => 'energieburcht_header_search_enable',
        ) );
    }
}
