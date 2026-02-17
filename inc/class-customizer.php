<?php
/**
 * Theme Customizer Class
 *
 * Registers all Customizer panels, sections, settings, and controls for the
 * Energieburcht theme options. The class is deliberately split into one private
 * method per section so each area is easy to locate, extend, or remove.
 *
 * Custom controls live in their own files under inc/controls/ and are loaded
 * on demand by the theme's SPL autoloader.
 *
 * @package Energieburcht
 * @since   1.0.0
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Energieburcht_Customizer
 */
final class Energieburcht_Customizer {

	/**
	 * Single shared instance of this class.
	 *
	 * @var Energieburcht_Customizer|null
	 */
	private static $instance = null;

	// =========================================================================
	// Singleton boilerplate
	// =========================================================================

	/**
	 * Private constructor — obtain the instance via get_instance().
	 */
	private function __construct() {
		add_action( 'customize_register',             array( $this, 'register' ) );
		add_action( 'customize_controls_print_styles', array( $this, 'print_control_styles' ) );
	}

	/**
	 * Return (or lazily create) the single shared instance.
	 *
	 * @return static
	 */
	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/** Cloning is forbidden on a singleton. */
	private function __clone() {}

	// =========================================================================
	// Registration entry point
	// =========================================================================

	/**
	 * Register the panel, sections, settings, and controls.
	 *
	 * Hooked to `customize_register`.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
	 * @return void
	 */
	public function register( WP_Customize_Manager $wp_customize ): void {
		$this->register_panel( $wp_customize );
		$this->register_header_section( $wp_customize );
		$this->register_footer_section( $wp_customize );
		$this->register_copyright_section( $wp_customize );
		$this->register_back_to_top_section( $wp_customize );
	}

	// =========================================================================
	// Panel
	// =========================================================================

	/**
	 * Register the top-level "Theme Options" panel.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
	 * @return void
	 */
	private function register_panel( WP_Customize_Manager $wp_customize ): void {
		$wp_customize->add_panel(
			'energieburcht_theme_options',
			array(
				'title'    => esc_html__( 'Theme Options', 'energieburcht' ),
				'priority' => 130, // After the default Widgets panel.
			)
		);
	}

	// =========================================================================
	// Header section
	// =========================================================================

	/**
	 * Register the Header section with its settings and controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
	 * @return void
	 */
	private function register_header_section( WP_Customize_Manager $wp_customize ): void {
		$wp_customize->add_section(
			'energieburcht_header_options',
			array(
				'title'    => esc_html__( 'Header', 'energieburcht' ),
				'panel'    => 'energieburcht_theme_options',
				'priority' => 5,
			)
		);

		// ── Enable / disable header search ────────────────────────────────────
		$wp_customize->add_setting(
			'energieburcht_header_search_enable',
			array(
				'default'           => false,
				'transport'         => 'refresh',
				'sanitize_callback' => 'wp_validate_boolean',
			)
		);

		$wp_customize->add_control(
			'energieburcht_header_search_enable',
			array(
				'type'    => 'checkbox',
				'label'   => esc_html__( 'Enable Header Search', 'energieburcht' ),
				'section' => 'energieburcht_header_options',
			)
		);
	}

	// =========================================================================
	// Footer section
	// =========================================================================

	/**
	 * Register the Footer section with its settings and controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
	 * @return void
	 */
	private function register_footer_section( WP_Customize_Manager $wp_customize ): void {
		$wp_customize->add_section(
			'energieburcht_footer_options',
			array(
				'title'    => esc_html__( 'Footer', 'energieburcht' ),
				'panel'    => 'energieburcht_theme_options',
				'priority' => 10,
			)
		);

		// ── Column count ──────────────────────────────────────────────────────
		$wp_customize->add_setting(
			'energieburcht_footer_columns',
			array(
				'default'           => 4,
				'transport'         => 'refresh',
				'sanitize_callback' => array( $this, 'sanitize_footer_columns' ),
			)
		);

		$wp_customize->add_control(
			'energieburcht_footer_columns',
			array(
				'type'        => 'select',
				'label'       => esc_html__( 'Footer Columns', 'energieburcht' ),
				'description' => esc_html__( 'Number of widget columns displayed in the footer.', 'energieburcht' ),
				'section'     => 'energieburcht_footer_options',
				'choices'     => array(
					1 => esc_html__( '1 Column', 'energieburcht' ),
					2 => esc_html__( '2 Columns', 'energieburcht' ),
					3 => esc_html__( '3 Columns', 'energieburcht' ),
					4 => esc_html__( '4 Columns', 'energieburcht' ),
				),
			)
		);

		// ── Background color ──────────────────────────────────────────────────
		$wp_customize->add_setting(
			'energieburcht_footer_bg_color',
			array(
				'default'           => '#003449',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_footer_bg_color',
				array(
					'label'   => esc_html__( 'Background Color', 'energieburcht' ),
					'section' => 'energieburcht_footer_options',
				)
			)
		);

		// ── Text color ────────────────────────────────────────────────────────
		$wp_customize->add_setting(
			'energieburcht_footer_text_color',
			array(
				'default'           => '#ffffff',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_footer_text_color',
				array(
					'label'   => esc_html__( 'Text Color', 'energieburcht' ),
					'section' => 'energieburcht_footer_options',
				)
			)
		);

		// ── Link color ────────────────────────────────────────────────────────
		$wp_customize->add_setting(
			'energieburcht_footer_link_color',
			array(
				'default'           => '#ffffff',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_footer_link_color',
				array(
					'label'   => esc_html__( 'Link Color', 'energieburcht' ),
					'section' => 'energieburcht_footer_options',
				)
			)
		);

		// ── Widget gap — Desktop ──────────────────────────────────────────────
		$wp_customize->add_setting(
			'energieburcht_footer_gap_desktop',
			array(
				'default'           => 50,
				'transport'         => 'refresh',
				'sanitize_callback' => 'absint',
			)
		);

		$wp_customize->add_control(
			new Energieburcht_Customize_Range_Control(
				$wp_customize,
				'energieburcht_footer_gap_desktop',
				array(
					'label'       => esc_html__( 'Widget Gap – Desktop (px)', 'energieburcht' ),
					'section'     => 'energieburcht_footer_options',
					'input_attrs' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				)
			)
		);

		// ── Widget gap — Tablet ───────────────────────────────────────────────
		$wp_customize->add_setting(
			'energieburcht_footer_gap_tablet',
			array(
				'default'           => 30,
				'transport'         => 'refresh',
				'sanitize_callback' => 'absint',
			)
		);

		$wp_customize->add_control(
			new Energieburcht_Customize_Range_Control(
				$wp_customize,
				'energieburcht_footer_gap_tablet',
				array(
					'label'       => esc_html__( 'Widget Gap – Tablet (px)', 'energieburcht' ),
					'section'     => 'energieburcht_footer_options',
					'input_attrs' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				)
			)
		);

		// ── Widget gap — Mobile ───────────────────────────────────────────────
		$wp_customize->add_setting(
			'energieburcht_footer_gap_mobile',
			array(
				'default'           => 20,
				'transport'         => 'refresh',
				'sanitize_callback' => 'absint',
			)
		);

		$wp_customize->add_control(
			new Energieburcht_Customize_Range_Control(
				$wp_customize,
				'energieburcht_footer_gap_mobile',
				array(
					'label'       => esc_html__( 'Widget Gap – Mobile (px)', 'energieburcht' ),
					'section'     => 'energieburcht_footer_options',
					'input_attrs' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				)
			)
		);

		// ── Footer logo (image upload) ────────────────────────────────────────
		$wp_customize->add_setting(
			'energieburcht_footer_logo',
			array(
				'default'           => '',
				'transport'         => 'refresh',
				'sanitize_callback' => 'absint', // Value is a WordPress attachment ID.
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'energieburcht_footer_logo',
				array(
					'label'     => esc_html__( 'Footer Logo', 'energieburcht' ),
					'section'   => 'energieburcht_footer_options',
					'mime_type' => 'image',
				)
			)
		);
	}

	// =========================================================================
	// Copyright section
	// =========================================================================

	/**
	 * Register the Copyright section with its settings and controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
	 * @return void
	 */
	private function register_copyright_section( WP_Customize_Manager $wp_customize ): void {
		$wp_customize->add_section(
			'energieburcht_copyright_options',
			array(
				'title'    => esc_html__( 'Copyright', 'energieburcht' ),
				'panel'    => 'energieburcht_theme_options',
				'priority' => 20,
			)
		);

		// ── Copyright text (supports [copyright] and [year] tokens) ──────────
		$wp_customize->add_setting(
			'energieburcht_copyright_text',
			array(
				'default'           => '[copyright] Energieburcht [year]',
				'transport'         => 'refresh',
				'sanitize_callback' => 'wp_kses_post', // Allow safe HTML.
			)
		);

		$wp_customize->add_control(
			new Energieburcht_Customize_Editor_Control(
				$wp_customize,
				'energieburcht_copyright_text',
				array(
					'label'       => esc_html__( 'Copyright Text', 'energieburcht' ),
					'description' => __( 'Use <code>[copyright]</code> for the &copy; symbol and <code>[year]</code> for the current year. HTML is allowed.', 'energieburcht' ),
					'section'     => 'energieburcht_copyright_options',
				)
			)
		);

		// ── Text color ────────────────────────────────────────────────────────
		$wp_customize->add_setting(
			'energieburcht_copyright_text_color',
			array(
				'default'           => '#9babae',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_copyright_text_color',
				array(
					'label'   => esc_html__( 'Text Color', 'energieburcht' ),
					'section' => 'energieburcht_copyright_options',
				)
			)
		);

		// ── Background color ──────────────────────────────────────────────────
		$wp_customize->add_setting(
			'energieburcht_copyright_bg_color',
			array(
				'default'           => '#003449',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_copyright_bg_color',
				array(
					'label'   => esc_html__( 'Background Color', 'energieburcht' ),
					'section' => 'energieburcht_copyright_options',
				)
			)
		);

		// ── Border-top color ──────────────────────────────────────────────────
		// Bug fix: the original code registered a control for this setting but
		// omitted the corresponding add_setting() call, so the value was never
		// persisted. Both are now registered correctly.
		$wp_customize->add_setting(
			'energieburcht_copyright_border_top_color',
			array(
				'default'           => '#003449',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_copyright_border_top_color',
				array(
					'label'   => esc_html__( 'Border Top Color', 'energieburcht' ),
					'section' => 'energieburcht_copyright_options',
				)
			)
		);
	}

	// =========================================================================
	// Back-to-top section
	// =========================================================================

	/**
	 * Register the Back to Top section with its settings and controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
	 * @return void
	 */
	private function register_back_to_top_section( WP_Customize_Manager $wp_customize ): void {
		$wp_customize->add_section(
			'energieburcht_back_to_top',
			array(
				'title'    => esc_html__( 'Back to Top', 'energieburcht' ),
				'panel'    => 'energieburcht_theme_options',
				'priority' => 30,
			)
		);

		// ── Enable toggle ─────────────────────────────────────────────────────
		$wp_customize->add_setting(
			'energieburcht_back_to_top_enable',
			array(
				'default'           => false,
				'transport'         => 'refresh',
				'sanitize_callback' => 'wp_validate_boolean',
			)
		);

		$wp_customize->add_control(
			'energieburcht_back_to_top_enable',
			array(
				'type'    => 'checkbox',
				'label'   => esc_html__( 'Enable Back to Top Button', 'energieburcht' ),
				'section' => 'energieburcht_back_to_top',
			)
		);

		// ── Button background color ───────────────────────────────────────────
		$wp_customize->add_setting(
			'energieburcht_back_to_top_bg_color',
			array(
				'default'           => '#ffffff',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_back_to_top_bg_color',
				array(
					'label'   => esc_html__( 'Button Background Color', 'energieburcht' ),
					'section' => 'energieburcht_back_to_top',
				)
			)
		);

		// ── Icon color ────────────────────────────────────────────────────────
		$wp_customize->add_setting(
			'energieburcht_back_to_top_icon_color',
			array(
				'default'           => '#003449',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_back_to_top_icon_color',
				array(
					'label'   => esc_html__( 'Icon Color', 'energieburcht' ),
					'section' => 'energieburcht_back_to_top',
				)
			)
		);
	}

	// =========================================================================
	// Customizer control styles
	// =========================================================================

	/**
	 * Output shared CSS for custom controls once, in the Customizer <head>.
	 *
	 * Hooked to `customize_controls_print_styles` so the rules are printed a
	 * single time regardless of how many range-control instances appear on the
	 * page. Previously these rules were duplicated inline per control instance.
	 *
	 * @return void
	 */
	public function print_control_styles(): void {
		?>
		<style id="energieburcht-range-control-styles">
			.energieburcht-range-control { margin-bottom: 15px; }

			.energieburcht-range-reset {
				cursor: pointer;
				color: #a7aaad;
				transition: color 0.1s ease-in-out;
			}
			.energieburcht-range-reset:hover { color: #2271b1; }

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
				box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
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
		<?php
	}

	// =========================================================================
	// Sanitization callbacks
	// =========================================================================

	/**
	 * Sanitize the footer columns select value.
	 *
	 * Ensures the saved value is one of the four allowed integer choices.
	 *
	 * @param  mixed $value Raw value from the Customizer.
	 * @return int          Validated integer (1–4), or the default of 4.
	 */
	public function sanitize_footer_columns( $value ): int {
		$value = absint( $value );
		return in_array( $value, array( 1, 2, 3, 4 ), true ) ? $value : 4;
	}
}
