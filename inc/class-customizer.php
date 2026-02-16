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

        // Footer Columns Setting
        $wp_customize->add_setting( 'energieburcht_footer_columns', array(
            'default'           => 4,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ) );

        // Footer Columns Control
        $wp_customize->add_control( 'energieburcht_footer_columns', array(
            'label'       => __( 'Footer Columns', 'energieburcht' ),
            'description' => __( 'Select the number of widget columns to display in the footer.', 'energieburcht' ),
            'section'     => 'energieburcht_footer_options',
            'type'        => 'select',
            'choices'     => array(
                1 => __( '1 Column', 'energieburcht' ),
                2 => __( '2 Columns', 'energieburcht' ),
                3 => __( '3 Columns', 'energieburcht' ),
                4 => __( '4 Columns', 'energieburcht' ),
            ),
            'settings'    => 'energieburcht_footer_columns',
        ) );

        // Footer Background Color Setting
        $wp_customize->add_setting( 'energieburcht_footer_bg_color', array(
            'default'           => '#003449',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ) );

        // Footer Background Color Control
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'energieburcht_footer_bg_color', array(
            'label'       => __( 'Background Color', 'energieburcht' ),
            'section'     => 'energieburcht_footer_options',
            'settings'    => 'energieburcht_footer_bg_color',
        ) ) );

        // Footer Text Color Setting
        $wp_customize->add_setting( 'energieburcht_footer_text_color', array(
            'default'           => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ) );

        // Footer Text Color Control
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'energieburcht_footer_text_color', array(
            'label'       => __( 'Text Color', 'energieburcht' ),
            'section'     => 'energieburcht_footer_options',
            'settings'    => 'energieburcht_footer_text_color',
        ) ) );

        // Footer Link Color Setting
        $wp_customize->add_setting( 'energieburcht_footer_link_color', array(
            'default'           => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ) );

        // Footer Link Color Control
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'energieburcht_footer_link_color', array(
            'label'       => __( 'Link Color', 'energieburcht' ),
            'section'     => 'energieburcht_footer_options',
            'settings'    => 'energieburcht_footer_link_color',
        ) ) );

        // Footer Widget Gap (Desktop)
        $wp_customize->add_setting( 'energieburcht_footer_gap_desktop', array(
            'default'           => 50,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( new Energieburcht_Customize_Range_Control( $wp_customize, 'energieburcht_footer_gap_desktop', array(
            'label'       => __( 'Widget Gap (Desktop)', 'energieburcht' ),
            'section'     => 'energieburcht_footer_options',
            'input_attrs' => array(
                'min'  => 0,
                'max'  => 100,
                'step' => 1,
            ),
            'settings'    => 'energieburcht_footer_gap_desktop',
        ) ) );

        // Footer Widget Gap (Tablet)
        $wp_customize->add_setting( 'energieburcht_footer_gap_tablet', array(
            'default'           => 30,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( new Energieburcht_Customize_Range_Control( $wp_customize, 'energieburcht_footer_gap_tablet', array(
            'label'       => __( 'Widget Gap (Tablet)', 'energieburcht' ),
            'section'     => 'energieburcht_footer_options',
            'input_attrs' => array(
                'min'  => 0,
                'max'  => 100,
                'step' => 1,
            ),
            'settings'    => 'energieburcht_footer_gap_tablet',
        ) ) );

        // Footer Widget Gap (Mobile)
        $wp_customize->add_setting( 'energieburcht_footer_gap_mobile', array(
            'default'           => 20,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( new Energieburcht_Customize_Range_Control( $wp_customize, 'energieburcht_footer_gap_mobile', array(
            'label'       => __( 'Widget Gap (Mobile)', 'energieburcht' ),
            'section'     => 'energieburcht_footer_options',
            'input_attrs' => array(
                'min'  => 0,
                'max'  => 100,
                'step' => 1,
            ),
            'settings'    => 'energieburcht_footer_gap_mobile',
        ) ) );

        // Footer Logo Setting
        $wp_customize->add_setting( 'energieburcht_footer_logo', array(
            'default'           => '',
            'sanitize_callback' => 'absint', // Logo is an attachment ID
            'transport'         => 'refresh',
        ) );

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

        // Copyright Section
        $wp_customize->add_section( 'energieburcht_copyright_options', array(
            'title'       => __( 'Copyright', 'energieburcht' ),
            'panel'       => 'energieburcht_theme_options',
            'priority'    => 20,
        ) );

        // Copyright Text Setting
        $wp_customize->add_setting( 'energieburcht_copyright_text', array(
            'default'           => '[copyright] Energieburcht [year]',
            'sanitize_callback' => 'wp_kses_post', // Allow HTML
            'transport'         => 'refresh',
        ) );

        // Copyright Text Control
        $wp_customize->add_control( new Energieburcht_Customize_Editor_Control( $wp_customize, 'energieburcht_copyright_text', array(
            'label'       => __( 'Copyright Text', 'energieburcht' ),
            'description' => __( 'Use <code>[copyright]</code> for &copy; symbol and <code>[year]</code> for current year. HTML tags are allowed.', 'energieburcht' ),
            'section'     => 'energieburcht_copyright_options',
            'settings'    => 'energieburcht_copyright_text',
        ) ) );

        // Copyright Text Color Setting
        $wp_customize->add_setting( 'energieburcht_copyright_text_color', array(
            'default'           => '#9babae',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ) );

        // Copyright Text Color Control
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'energieburcht_copyright_text_color', array(
            'label'       => __( 'Text Color', 'energieburcht' ),
            'section'     => 'energieburcht_copyright_options',
            'settings'    => 'energieburcht_copyright_text_color',
        ) ) );

        // Copyright Background Color Setting
        $wp_customize->add_setting( 'energieburcht_copyright_bg_color', array(
            'default'           => '#003449',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ) );

        // Copyright Background Color Control
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'energieburcht_copyright_bg_color', array(
            'label'       => __( 'Background Color', 'energieburcht' ),
            'section'     => 'energieburcht_copyright_options',
            'settings'    => 'energieburcht_copyright_bg_color',
        ) ) );

        // Copyright Border Top Color Control
        // Using Color Control. Note: WP color control might strip alpha if not careful, but usually handles hex/rgba.
        // If strict hex is needed, we'd need a custom control for alpha. 
        // For now, let's use standard color control which supports alpha in recent WP versions if 'mode' => 'alpha' logic is supported or just standard color.
        // Actually, WP_Customize_Color_Control doesn't support alpha out of the box in older versions, but let's try. 
        // Providing a text input as fallback if color picker is restrictive? 
        // Let's stick to standard color control. If user wants transparency they might need a hex code with alpha or we assume solid.
        // However, the default is rgba. Let's use a text control for border color to allow rgba if color control fails, OR just use color control and expect Hex.
        // Let's use Color Control.
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'energieburcht_copyright_border_top_color', array(
            'label'       => __( 'Border Top Color', 'energieburcht' ),
            'section'     => 'energieburcht_copyright_options',
            'settings'    => 'energieburcht_copyright_border_top_color',
        ) ) );

        // Back to Top Section
        $wp_customize->add_section( 'energieburcht_back_to_top', array(
            'title'       => __( 'Back to Top', 'energieburcht' ),
            'panel'       => 'energieburcht_theme_options',
            'priority'    => 30,
        ) );

        // Back to Top Enable
        $wp_customize->add_setting( 'energieburcht_back_to_top_enable', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'energieburcht_back_to_top_enable', array(
            'label'       => __( 'Enable Back to Top', 'energieburcht' ),
            'section'     => 'energieburcht_back_to_top',
            'type'        => 'checkbox',
            'settings'    => 'energieburcht_back_to_top_enable',
        ) );

        // Back to Top Background Color
        $wp_customize->add_setting( 'energieburcht_back_to_top_bg_color', array(
            'default'           => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'energieburcht_back_to_top_bg_color', array(
            'label'       => __( 'Background Color', 'energieburcht' ),
            'section'     => 'energieburcht_back_to_top',
            'settings'    => 'energieburcht_back_to_top_bg_color',
        ) ) );

        // Back to Top Icon Color
        $wp_customize->add_setting( 'energieburcht_back_to_top_icon_color', array(
            'default'           => '#003449',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'energieburcht_back_to_top_icon_color', array(
            'label'       => __( 'Icon Color', 'energieburcht' ),
            'section'     => 'energieburcht_back_to_top',
            'settings'    => 'energieburcht_back_to_top_icon_color',
        ) ) );
    }
}

if ( class_exists( 'WP_Customize_Control' ) ) {
    /**
     * Custom Editor Control (Textarea with HTML support description)
     */
    class Energieburcht_Customize_Editor_Control extends WP_Customize_Control {
        public $type = 'energieburcht-editor';
        
        public function render_content() {
            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <div class="customize-control-notifications-container"></div>
                <textarea class="large-text code" rows="5" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
                <?php if ( ! empty( $this->description ) ) : ?>
                    <span class="description customize-control-description"><?php echo $this->description; ?></span>
                <?php endif; ?>
            </label>
            <?php
        }
    }

    /**
     * Custom Range Control with Value Display
     */
    class Energieburcht_Customize_Range_Control extends WP_Customize_Control {
        public $type = 'energieburcht-range';
        
        public function render_content() {
            $input_id = '_customize-input-' . $this->id;
            $default = $this->setting->default;
            ?>
            <div class="energieburcht-range-control">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                    <label for="<?php echo esc_attr( $input_id ); ?>" class="customize-control-title"><?php echo esc_html( $this->label ); ?></label>
                    <div class="energieburcht-range-reset" title="<?php esc_attr_e( 'Reset', 'energieburcht' ); ?>" data-default="<?php echo esc_attr( $default ); ?>">
                        <span class="dashicons dashicons-image-rotate"></span>
                    </div>
                </div>
                
                <div class="range-control-wrapper">
                    <input type="range" 
                           id="<?php echo esc_attr( $input_id ); ?>"
                           <?php $this->link(); ?> 
                           min="<?php echo esc_attr( $this->input_attrs['min'] ?? 0 ); ?>" 
                           max="<?php echo esc_attr( $this->input_attrs['max'] ?? 100 ); ?>" 
                           step="<?php echo esc_attr( $this->input_attrs['step'] ?? 1 ); ?>" 
                           value="<?php echo esc_attr( $this->value() ); ?>" 
                           class="energieburcht-range-input"
                           oninput="this.nextElementSibling.value = this.value">
                    <input type="number" 
                           min="<?php echo esc_attr( $this->input_attrs['min'] ?? 0 ); ?>" 
                           max="<?php echo esc_attr( $this->input_attrs['max'] ?? 100 ); ?>" 
                           value="<?php echo esc_attr( $this->value() ); ?>" 
                           class="energieburcht-range-number"
                           oninput="this.previousElementSibling.value = this.value; this.previousElementSibling.dispatchEvent(new Event('input')); this.previousElementSibling.dispatchEvent(new Event('change'));">
                </div>

                <?php if ( ! empty( $this->description ) ) : ?>
                    <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
                <?php endif; ?>
                
                <style>
                    .energieburcht-range-control {
                        margin-bottom: 15px;
                    }
                    .energieburcht-range-reset {
                        cursor: pointer;
                        color: #a7aaad;
                        transition: color 0.1s ease-in-out;
                    }
                    .energieburcht-range-reset:hover {
                        color: #2271b1;
                    }
                    .range-control-wrapper {
                        display: flex;
                        align-items: center;
                        gap: 15px;
                        background: #fff;
                        border: 1px solid #dcdcde;
                        padding: 5px 10px;
                        border-radius: 4px;
                    }
                    .energieburcht-range-input {
                        flex-grow: 1;
                        cursor: pointer;
                        -webkit-appearance: none;
                        height: 4px;
                        background: #dcdcde;
                        border-radius: 2px;
                        outline: none;
                        margin: 0;
                    }
                    .energieburcht-range-input::-webkit-slider-thumb {
                        -webkit-appearance: none;
                        appearance: none;
                        width: 16px;
                        height: 16px;
                        border-radius: 50%;
                        background: #2271b1;
                        cursor: pointer;
                        border: 2px solid #fff;
                        box-shadow: 0 1px 2px rgba(0,0,0,0.2);
                        transition: transform 0.1s;
                    }
                    .energieburcht-range-input::-webkit-slider-thumb:hover {
                        transform: scale(1.1);
                        background: #135e96;
                    }
                    .energieburcht-range-number {
                        width: 50px !important;
                        text-align: center;
                        border: none !important;
                        background: transparent !important;
                        font-weight: 600;
                        color: #1d2327;
                        padding: 0 !important;
                        -moz-appearance: textfield;
                    }
                    .energieburcht-range-number::-webkit-outer-spin-button,
                    .energieburcht-range-number::-webkit-inner-spin-button {
                        -webkit-appearance: none;
                        margin: 0;
                    }
                    .energieburcht-range-number:focus {
                        box-shadow: none !important;
                        outline: none !important;
                    }
                </style>
                
                <script>
                    (function() {
                        // Simple inline script to handle reset clicks for this specific control instance type
                        // Using event delegation on the document body or just attaching to the element found in this render block won't work perfectly 
                        // because Customizer dynamically loads controls. 
                        // However, we can use a small self-executing function that binds to existing elements, but better yet, let's use onclick attribute on the icon for simplicity 
                        // or improved delegation.
                    })();
                    
                    // Binding click event to the reset button
                    jQuery(document).ready(function($) {
                        $('.energieburcht-range-reset').off('click').on('click', function() {
                            var $button = $(this);
                            var defaultVal = $button.data('default');
                            var $wrapper = $button.closest('.energieburcht-range-control');
                            var $range = $wrapper.find('input[type="range"]');
                            var $number = $wrapper.find('input[type="number"]');
                            
                            $range.val(defaultVal).trigger('input').trigger('change');
                            $number.val(defaultVal);
                        });
                    });
                </script>
            </div>
            <?php
        }
    }
}
