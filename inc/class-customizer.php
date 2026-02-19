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
	// Colour palette — single source of truth
	// =========================================================================

	/**
	 * Returns the theme colour palette definition.
	 *
	 * Each entry mirrors the colour slugs declared in theme.json so the two
	 * systems stay in sync. The Enqueue class reads this same list to output
	 * the corresponding CSS custom properties.
	 *
	 * @return array<int, array{slug: string, label: string, css_var: string, default: string}>
	 */
	public static function get_color_palette(): array {
		return array(
			array( 'slug' => 'black',      'label' => 'Black',      'css_var' => '--eb-black',      'default' => '#000000' ),
			array( 'slug' => 'dark-gray',  'label' => 'Dark Gray',  'css_var' => '--eb-dark-gray',  'default' => '#212529' ),
			array( 'slug' => 'blue',       'label' => 'Blue',       'css_var' => '--eb-blue',       'default' => '#00ACDD' ),
			array( 'slug' => 'cyan',       'label' => 'Cyan',       'css_var' => '--eb-cyan',       'default' => '#0095c0' ),
			array( 'slug' => 'light-blue', 'label' => 'Light Blue', 'css_var' => '--eb-light-blue', 'default' => '#e0f0f5' ),
			array( 'slug' => 'navy',       'label' => 'Navy',       'css_var' => '--eb-navy',       'default' => '#003449' ),
			array( 'slug' => 'light-gray', 'label' => 'Light Gray', 'css_var' => '--eb-light-gray', 'default' => '#9BABAE' ),
			array( 'slug' => 'red',        'label' => 'Red',        'css_var' => '--eb-red',        'default' => '#E31C23' ),
			array( 'slug' => 'white',      'label' => 'White',      'css_var' => '--eb-white',      'default' => '#FFFFFF' ),
			array( 'slug' => 'off-white',  'label' => 'Off White',  'css_var' => '--eb-off-white',  'default' => '#EFEFEF' ),
		);
	}

	/**
	 * Returns the element colour definitions — the single source of truth
	 * shared between the element-colour Customizer controls and the CSS
	 * custom-property output in Energieburcht_Enqueue.
	 *
	 * Each value is keyed by a snake_case identifier that maps directly to a
	 * CSS variable name: 'body_text' → '--eb-body-text'.
	 *
	 * Defaults reference palette variables (var(--eb-*)) so element colours
	 * stay linked to the palette automatically unless the user overrides them.
	 *
	 * @return array<string, array{label: string, default: string, section: string}>
	 */
	public static function get_element_colors(): array {
		return array(
			// Body
			'body_text'        => array( 'label' => __( 'Body Text',        'energieburcht' ), 'default' => 'var(--eb-dark-gray)', 'section' => 'body' ),
			'body_bg'          => array( 'label' => __( 'Body Background',  'energieburcht' ), 'default' => 'var(--eb-white)',     'section' => 'body' ),
			// Headings (per level)
			'h1_color'         => array( 'label' => __( 'H1',               'energieburcht' ), 'default' => 'var(--eb-black)',     'section' => 'headings' ),
			'h2_color'         => array( 'label' => __( 'H2',               'energieburcht' ), 'default' => 'var(--eb-black)',     'section' => 'headings' ),
			'h3_color'         => array( 'label' => __( 'H3',               'energieburcht' ), 'default' => 'var(--eb-black)',     'section' => 'headings' ),
			'h4_color'         => array( 'label' => __( 'H4',               'energieburcht' ), 'default' => 'var(--eb-black)',     'section' => 'headings' ),
			// Buttons
			'btn_bg'           => array( 'label' => __( 'Background',       'energieburcht' ), 'default' => 'var(--eb-blue)',      'section' => 'buttons' ),
			'btn_color'        => array( 'label' => __( 'Text Color',       'energieburcht' ), 'default' => 'var(--eb-white)',     'section' => 'buttons' ),
			'btn_hover_bg'     => array( 'label' => __( 'Hover Background', 'energieburcht' ), 'default' => 'var(--eb-cyan)',      'section' => 'buttons' ),
			'btn_hover_color'  => array( 'label' => __( 'Hover Text Color', 'energieburcht' ), 'default' => 'var(--eb-white)',     'section' => 'buttons' ),
			// Links
			'link_color'              => array( 'label' => __( 'Link Color',              'energieburcht' ), 'default' => 'var(--eb-blue)',       'section' => 'links' ),
			'link_hover_color'        => array( 'label' => __( 'Link Hover Color',        'energieburcht' ), 'default' => 'var(--eb-cyan)',       'section' => 'links' ),
			// Desktop Navigation
			'nav_bg'                  => array( 'label' => __( 'Nav Background',          'energieburcht' ), 'default' => 'var(--eb-white)',      'section' => 'nav-desktop' ),
			'nav_border'              => array( 'label' => __( 'Nav Border',              'energieburcht' ), 'default' => 'var(--eb-off-white)',  'section' => 'nav-desktop' ),
			'nav_link_color'          => array( 'label' => __( 'Link Color',              'energieburcht' ), 'default' => 'var(--eb-navy)',       'section' => 'nav-desktop' ),
			'nav_link_hover_border'   => array( 'label' => __( 'Link Hover Border',       'energieburcht' ), 'default' => 'var(--eb-light-blue)', 'section' => 'nav-desktop' ),
			'nav_dropdown_bg'         => array( 'label' => __( 'Dropdown Background',     'energieburcht' ), 'default' => 'var(--eb-white)',       'section' => 'nav-desktop' ),
			'nav_dropdown_border'     => array( 'label' => __( 'Dropdown Border',         'energieburcht' ), 'default' => 'var(--eb-off-white)',   'section' => 'nav-desktop' ),
			'nav_dropdown_hover_bg'     => array( 'label' => __( 'Dropdown Item Hover',     'energieburcht' ), 'default' => 'var(--eb-light-blue)', 'section' => 'nav-desktop' ),
			'nav_dropdown_link_color'   => array( 'label' => __( 'Dropdown Link Color',     'energieburcht' ), 'default' => 'var(--eb-navy)',       'section' => 'nav-desktop' ),
			'nav_chevron_color'         => array( 'label' => __( 'Chevron Arrow Color',    'energieburcht' ), 'default' => 'var(--eb-blue)',       'section' => 'nav-desktop' ),
			// Mobile Navigation
			'mob_nav_bg'              => array( 'label' => __( 'Panel Background',        'energieburcht' ), 'default' => 'var(--eb-white)',      'section' => 'nav-mobile' ),
			'mob_nav_link_color'      => array( 'label' => __( 'Link Color',              'energieburcht' ), 'default' => 'var(--eb-navy)',       'section' => 'nav-mobile' ),
			'mob_nav_item_border'     => array( 'label' => __( 'Item Separator',          'energieburcht' ), 'default' => 'var(--eb-off-white)',  'section' => 'nav-mobile' ),
			'mob_nav_icon_color'      => array( 'label' => __( 'Arrow Icon Color',        'energieburcht' ), 'default' => 'var(--eb-blue)',       'section' => 'nav-mobile' ),
			'mob_nav_submenu_bg'      => array( 'label' => __( 'Sub-menu Background',     'energieburcht' ), 'default' => 'var(--eb-white)',      'section' => 'nav-mobile' ),
			'mob_nav_toggle_bg'       => array( 'label' => __( 'Toggle Button Background','energieburcht' ), 'default' => 'var(--eb-white)',      'section' => 'nav-mobile' ),
			'mob_nav_toggle_open_bg'  => array( 'label' => __( 'Toggle Open Background',  'energieburcht' ), 'default' => 'var(--eb-off-white)', 'section' => 'nav-mobile' ),
			'mob_nav_toggle_icon_color' => array( 'label' => __( 'Toggle Icon Color',     'energieburcht' ), 'default' => 'var(--eb-blue)',       'section' => 'nav-mobile' ),
		);
	}

	/**
	 * Derive the wp_theme_mod option key from an element colour key.
	 *
	 * e.g. 'btn_hover_bg' → 'energieburcht_element_btn_hover_bg'
	 *
	 * @param  string $key Element key from get_element_colors().
	 * @return string      Corresponding theme_mod option key.
	 */
	public static function element_setting_key( string $key ): string {
		return 'energieburcht_element_' . $key;
	}

	/**
	 * Sanitise an element colour value.
	 *
	 * Accepts either:
	 *  - A var(--eb-*) reference pointing to a known palette variable, or
	 *  - A valid hex colour string (#rrggbb / #rgb).
	 *
	 * @param  mixed $value Raw value from the Customizer.
	 * @return string       Sanitised value, or empty string on failure.
	 */
	public function sanitize_element_color( $value ): string {
		if ( is_string( $value ) && preg_match( '/^var\(--eb-[a-z-]+\)$/', $value ) ) {
			$known_vars = array_column( self::get_color_palette(), 'css_var' );
			$var_name   = substr( $value, 4, -1 ); // strip 'var(' and ')'
			if ( in_array( $var_name, $known_vars, true ) ) {
				return $value;
			}
		}
		return sanitize_hex_color( $value ) ?: '';
	}

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
		$this->register_element_color_sections( $wp_customize );
		$this->register_header_section( $wp_customize );
		$this->register_page_section( $wp_customize );
		$this->register_typography_section( $wp_customize );
		$this->register_footer_section( $wp_customize );
		$this->register_copyright_section( $wp_customize );
		$this->register_back_to_top_section( $wp_customize );
	}

	/**
	 * Register the Typography section (New).
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
	 * @return void
	 */
	private function register_typography_section( WP_Customize_Manager $wp_customize ): void {
		$wp_customize->add_section(
			'energieburcht_typography_options',
			array(
				'title'    => esc_html__( 'Typography', 'energieburcht' ),
				'panel'    => 'energieburcht_theme_options',
				'priority' => 5, // Top priority
			)
		);

		// Shared Presets from theme.json
		$presets = array(
			array( 'name' => 'Small',       'value' => '0.875rem' ),
			array( 'name' => 'Medium',      'value' => '1rem' ),
			array( 'name' => 'Large',       'value' => 'clamp(1.125rem, 2vw, 1.25rem)' ),
			array( 'name' => 'XL',          'value' => 'clamp(1.25rem, 2.5vw, 1.5rem)' ),
			array( 'name' => 'XXL',         'value' => 'clamp(1.5rem, 3vw, 2rem)' ),
			array( 'name' => 'XXXL',        'value' => 'clamp(2rem, 4vw, 2.5rem)' ),
			array( 'name' => 'Super',       'value' => 'clamp(2.25rem, 5vw, 3rem)' ),
		);

		// 1. Body / Paragraph
		$wp_customize->add_setting( 'energieburcht_typography_body', array(
			'default'           => '1rem', // Medium
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Energieburcht_Customize_Typography_Control(
			$wp_customize,
			'energieburcht_typography_body',
			array(
				'label'       => esc_html__( 'Body Text (Paragraph)', 'energieburcht' ),
				'section'     => 'energieburcht_typography_options',
				'input_attrs' => array( 'presets' => $presets ),
			)
		) );

		// 2. Heading H1
		$wp_customize->add_setting( 'energieburcht_typography_h1', array(
			'default'           => 'clamp(2.25rem, 5vw, 3rem)', // Super
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Energieburcht_Customize_Typography_Control(
			$wp_customize,
			'energieburcht_typography_h1',
			array(
				'label'       => esc_html__( 'Heading H1', 'energieburcht' ),
				'section'     => 'energieburcht_typography_options',
				'input_attrs' => array( 'presets' => $presets ),
			)
		) );

		// 3. Excerpt (New)
		$wp_customize->add_setting( 'energieburcht_typography_excerpt', array(
			'default'           => 'clamp(1.125rem, 2vw, 1.25rem)', // Large
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Energieburcht_Customize_Typography_Control(
			$wp_customize,
			'energieburcht_typography_excerpt',
			array(
				'label'       => esc_html__( 'Excerpt', 'energieburcht' ),
				'section'     => 'energieburcht_typography_options',
				'input_attrs' => array( 'presets' => $presets ),
			)
		) );

		// 4. Heading H2
		$wp_customize->add_setting( 'energieburcht_typography_h2', array(
			'default'           => 'clamp(2rem, 4vw, 2.5rem)', // XXXL
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Energieburcht_Customize_Typography_Control(
			$wp_customize,
			'energieburcht_typography_h2',
			array(
				'label'       => esc_html__( 'Heading H2', 'energieburcht' ),
				'section'     => 'energieburcht_typography_options',
				'input_attrs' => array( 'presets' => $presets ),
			)
		) );

		// 6. Heading H3
		$wp_customize->add_setting( 'energieburcht_typography_h3', array(
			'default'           => 'clamp(1.5rem, 3vw, 2rem)', // XXL
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Energieburcht_Customize_Typography_Control(
			$wp_customize,
			'energieburcht_typography_h3',
			array(
				'label'       => esc_html__( 'Heading H3', 'energieburcht' ),
				'section'     => 'energieburcht_typography_options',
				'input_attrs' => array( 'presets' => $presets ),
			)
		) );

		// 7. Heading H4
		$wp_customize->add_setting( 'energieburcht_typography_h4', array(
			'default'           => 'clamp(1.25rem, 2.5vw, 1.5rem)', // XL
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Energieburcht_Customize_Typography_Control(
			$wp_customize,
			'energieburcht_typography_h4',
			array(
				'label'       => esc_html__( 'Heading H4', 'energieburcht' ),
				'section'     => 'energieburcht_typography_options',
				'input_attrs' => array( 'presets' => $presets ),
			)
		) );

		// 8. Button
		$wp_customize->add_setting( 'energieburcht_typography_button', array(
			'default'           => '1rem', // Medium
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Energieburcht_Customize_Typography_Control(
			$wp_customize,
			'energieburcht_typography_button',
			array(
				'label'       => esc_html__( 'Button', 'energieburcht' ),
				'section'     => 'energieburcht_typography_options',
				'input_attrs' => array( 'presets' => $presets ),
			)
		) );
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
		// Top-level Theme Options panel.
		$wp_customize->add_panel(
			'energieburcht_theme_options',
			array(
				'title'    => esc_html__( 'Theme Options', 'energieburcht' ),
				'priority' => 130, // After the default Widgets panel.
			)
		);

		// Colors sub-panel — lives inside Theme Options and groups all
		// palette + element colour sections in one place.
		$wp_customize->add_panel(
			'energieburcht_colors_panel',
			array(
				'title'    => esc_html__( 'Colors', 'energieburcht' ),
				'panel'    => 'energieburcht_theme_options',
				'priority' => 2,
			)
		);
	}

	// =========================================================================
	// Element colour sections (Body / Headings / Buttons / Links)
	// =========================================================================

	/**
	 * Register one Customizer section per element group, each containing a
	 * Palette Colour Control for every colour property in that group.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
	 * @return void
	 */
	private function register_element_color_sections( WP_Customize_Manager $wp_customize ): void {

		// Section meta: slug → [title, priority]
		$sections = array(
			'body'        => array( esc_html__( 'Body',         'energieburcht' ), 20 ),
			'headings'    => array( esc_html__( 'Headings',     'energieburcht' ), 30 ),
			'buttons'     => array( esc_html__( 'Buttons',      'energieburcht' ), 40 ),
			'links'       => array( esc_html__( 'Links',        'energieburcht' ), 50 ),
			'nav-desktop' => array( esc_html__( 'Desktop Nav',  'energieburcht' ), 60 ),
			'nav-mobile'  => array( esc_html__( 'Mobile Nav',   'energieburcht' ), 70 ),
		);

		foreach ( $sections as $slug => list( $title, $priority ) ) {
			$wp_customize->add_section(
				'energieburcht_colors_' . $slug,
				array(
					'title'    => $title,
					'panel'    => 'energieburcht_colors_panel',
					'priority' => $priority,
				)
			);
		}

		// Add a setting + Palette Colour Control for every element colour entry.
		foreach ( self::get_element_colors() as $key => $element ) {
			$setting_key = self::element_setting_key( $key );

			$wp_customize->add_setting(
				$setting_key,
				array(
					'default'           => $element['default'],
					'transport'         => 'refresh',
					'sanitize_callback' => array( $this, 'sanitize_element_color' ),
				)
			);

			$wp_customize->add_control(
				new Energieburcht_Customize_Palette_Color_Control(
					$wp_customize,
					$setting_key,
					array(
						'label'   => $element['label'],
						'section' => 'energieburcht_colors_' . $element['section'],
					)
				)
			);
		}
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

		// ── Header Search Colors ──────────────────────────────────────────────

		// Search Background
		$wp_customize->add_setting(
			'energieburcht_header_search_bg_color',
			array(
				'default'           => '#EFEFEF', // var(--eb-off-white)
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_header_search_bg_color',
				array(
					'label'   => esc_html__( 'Search Background', 'energieburcht' ),
					'section' => 'energieburcht_header_options',
				)
			)
		);

		// Search Text
		$wp_customize->add_setting(
			'energieburcht_header_search_text_color',
			array(
				'default'           => '#003449', // var(--eb-navy)
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_header_search_text_color',
				array(
					'label'   => esc_html__( 'Search Text Color', 'energieburcht' ),
					'section' => 'energieburcht_header_options',
				)
			)
		);

		// Search Icon
		$wp_customize->add_setting(
			'energieburcht_header_search_icon_color',
			array(
				'default'           => '#003449', // var(--eb-navy)
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_header_search_icon_color',
				array(
					'label'   => esc_html__( 'Search Icon Color', 'energieburcht' ),
					'section' => 'energieburcht_header_options',
				)
			)
		);

		// Search Icon Hover
		$wp_customize->add_setting(
			'energieburcht_header_search_icon_hover_color',
			array(
				'default'           => '#00ACDD', // var(--eb-blue)
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_header_search_icon_hover_color',
				array(
					'label'   => esc_html__( 'Search Icon Hover Color', 'energieburcht' ),
					'section' => 'energieburcht_header_options',
				)
			)
		);

		// Search Icon Hover Background
		$wp_customize->add_setting(
			'energieburcht_header_search_icon_hover_bg_color',
			array(
				'default'           => '#efebeb',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_header_search_icon_hover_bg_color',
				array(
					'label'   => esc_html__( 'Search Icon Hover BG', 'energieburcht' ),
					'section' => 'energieburcht_header_options',
				)
			)
		);

		// ── Logo Width ────────────────────────────────────────────────────────
		// Hooks into core 'title_tagline' section (Site Identity).
		$wp_customize->add_setting(
			'energieburcht_logo_width',
			array(
				'default'           => 60,
				'transport'         => 'refresh',
				'sanitize_callback' => 'absint',
			)
		);

		$wp_customize->add_control(
			new Energieburcht_Customize_Range_Control(
				$wp_customize,
				'energieburcht_logo_width',
				array(
					'label'       => esc_html__( 'Logo Width (px)', 'energieburcht' ),
					'section'     => 'title_tagline',
					'input_attrs' => array(
						'min'  => 20,
						'max'  => 200,
						'step' => 1,
					),
				)
			)
		);
	}

	// =========================================================================
	// Footer section
	// =========================================================================

	// =========================================================================
	// Page section
	// =========================================================================

	/**
	 * Register the Page section with its settings and controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
	 * @return void
	 */
	private function register_page_section( WP_Customize_Manager $wp_customize ): void {
		$wp_customize->add_section(
			'energieburcht_page_options',
			array(
				'title'    => esc_html__( 'Page Options', 'energieburcht' ),
				'panel'    => 'energieburcht_theme_options',
				'priority' => 8, // Between Header and Footer
			)
		);

		// ── Enable / disable page title ───────────────────────────────────────
		$wp_customize->add_setting(
			'energieburcht_page_title_enable',
			array(
				'default'           => false,
				'transport'         => 'refresh',
				'sanitize_callback' => 'wp_validate_boolean',
			)
		);

		$wp_customize->add_control(
			'energieburcht_page_title_enable',
			array(
				'type'    => 'checkbox',
				'label'   => esc_html__( 'Show Page Title', 'energieburcht' ),
				'section' => 'energieburcht_page_options',
			)
		);

		// ── Hero Section ──────────────────────────────────────────────────────

		// Hero Enable
		$wp_customize->add_setting(
			'energieburcht_hero_enable',
			array(
				'default'           => false,
				'transport'         => 'refresh',
				'sanitize_callback' => 'wp_validate_boolean',
			)
		);

		$wp_customize->add_control(
			'energieburcht_hero_enable',
			array(
				'type'    => 'checkbox',
				'label'   => esc_html__( 'Enable Hero Section', 'energieburcht' ),
				'section' => 'energieburcht_page_options',
			)
		);

		// ── Hero Content ──────────────────────────────────────────────────────
		$wp_customize->add_setting( 'energieburcht_hero_content_sep', array( 'transport' => 'refresh', 'sanitize_callback' => '__return_empty_string' ) );
		$wp_customize->add_control(
			new Energieburcht_Customize_Separator_Control(
				$wp_customize,
				'energieburcht_hero_content_sep',
				array(
					'label'           => esc_html__( 'Hero Content', 'energieburcht' ),
					'section'         => 'energieburcht_page_options',
					'active_callback' => array( $this, 'callback_hero_enabled' ),
				)
			)
		);

		// Hero Elements (Title, Excerpt, CTA)
		// We'll use a simple multi-checkbox approach by registering 3 separate settings for simplicity
		// unless a multi-checkbox control exists. Standard WP doesn't have one, so 3 settings is safer.

		// Show Title
		$wp_customize->add_setting(
			'energieburcht_hero_show_title',
			array(
				'default'           => true,
				'transport'         => 'refresh',
				'sanitize_callback' => 'wp_validate_boolean',
			)
		);
		$wp_customize->add_control(
			'energieburcht_hero_show_title',
			array(
				'type'            => 'checkbox',
				'label'           => esc_html__( 'Show Hero Title', 'energieburcht' ),
				'section'         => 'energieburcht_page_options',
				'active_callback' => array( $this, 'callback_hero_enabled' ),
			)
		);

		// Show Excerpt
		$wp_customize->add_setting(
			'energieburcht_hero_show_excerpt',
			array(
				'default'           => true,
				'transport'         => 'refresh',
				'sanitize_callback' => 'wp_validate_boolean',
			)
		);
		$wp_customize->add_control(
			'energieburcht_hero_show_excerpt',
			array(
				'type'            => 'checkbox',
				'label'           => esc_html__( 'Show Hero Excerpt', 'energieburcht' ),
				'section'         => 'energieburcht_page_options',
				'active_callback' => array( $this, 'callback_hero_enabled' ),
			)
		);

		// Show CTA
		$wp_customize->add_setting(
			'energieburcht_hero_show_cta',
			array(
				'default'           => true,
				'transport'         => 'refresh',
				'sanitize_callback' => 'wp_validate_boolean',
			)
		);
		$wp_customize->add_control(
			'energieburcht_hero_show_cta',
			array(
				'type'            => 'checkbox',
				'label'           => esc_html__( 'Show Hero CTA', 'energieburcht' ),
				'section'         => 'energieburcht_page_options',
				'active_callback' => array( $this, 'callback_hero_enabled' ),
			)
		);

		// ── Hero Typography ───────────────────────────────────────────────────
		$wp_customize->add_setting( 'energieburcht_hero_typography_sep', array( 'transport' => 'refresh', 'sanitize_callback' => '__return_empty_string' ) );
		$wp_customize->add_control(
			new Energieburcht_Customize_Separator_Control(
				$wp_customize,
				'energieburcht_hero_typography_sep',
				array(
					'label'           => esc_html__( 'Hero Typography', 'energieburcht' ),
					'section'         => 'energieburcht_page_options',
					'active_callback' => array( $this, 'callback_hero_enabled' ),
				)
			)
		);

		// Title Color
		$wp_customize->add_setting(
			'energieburcht_hero_title_color',
			array(
				'default'           => '#ffffff',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_hero_title_color',
				array(
					'label'           => esc_html__( 'Title Color', 'energieburcht' ),
					'section'         => 'energieburcht_page_options',
					'active_callback' => array( $this, 'callback_hero_title_visible' ),
				)
			)
		);

		// Excerpt Color
		$wp_customize->add_setting(
			'energieburcht_hero_excerpt_color',
			array(
				'default'           => '#ffffff',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_hero_excerpt_color',
				array(
					'label'           => esc_html__( 'Excerpt Color', 'energieburcht' ),
					'section'         => 'energieburcht_page_options',
					'active_callback' => array( $this, 'callback_hero_excerpt_visible' ),
				)
			)
		);
		// ── Hero CTA ──────────────────────────────────────────────────────────
		$wp_customize->add_setting( 'energieburcht_hero_cta_sep', array( 'transport' => 'refresh', 'sanitize_callback' => '__return_empty_string' ) );
		$wp_customize->add_control(
			new Energieburcht_Customize_Separator_Control(
				$wp_customize,
				'energieburcht_hero_cta_sep',
				array(
					'label'           => esc_html__( 'Hero CTA', 'energieburcht' ),
					'section'         => 'energieburcht_page_options',
					'active_callback' => array( $this, 'callback_hero_cta_visible' ),
				)
			)
		);

		// CTA Text Color
		$wp_customize->add_setting(
			'energieburcht_hero_cta_text_color',
			array(
				'default'           => '#ffffff',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_hero_cta_text_color',
				array(
					'label'           => esc_html__( 'CTA Text Color', 'energieburcht' ),
					'section'         => 'energieburcht_page_options',
					'active_callback' => array( $this, 'callback_hero_cta_visible' ),
				)
			)
		);

		// CTA Background Color
		$wp_customize->add_setting(
			'energieburcht_hero_cta_bg_color',
			array(
				'default'           => '#00acdd',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_hero_cta_bg_color',
				array(
					'label'           => esc_html__( 'CTA Background Color', 'energieburcht' ),
					'section'         => 'energieburcht_page_options',
					'active_callback' => array( $this, 'callback_hero_cta_visible' ),
				)
			)
		);

		// CTA Hover Text Color
		$wp_customize->add_setting(
			'energieburcht_hero_cta_text_hover_color',
			array(
				'default'           => '#ffffff',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_hero_cta_text_hover_color',
				array(
					'label'           => esc_html__( 'CTA Text Hover Color', 'energieburcht' ),
					'section'         => 'energieburcht_page_options',
					'active_callback' => array( $this, 'callback_hero_cta_visible' ),
				)
			)
		);

		// CTA Hover Background Color
		$wp_customize->add_setting(
			'energieburcht_hero_cta_bg_hover_color',
			array(
				'default'           => '#26b8e2',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_hero_cta_bg_hover_color',
				array(
					'label'           => esc_html__( 'CTA Background Hover Color', 'energieburcht' ),
					'section'         => 'energieburcht_page_options',
					'active_callback' => array( $this, 'callback_hero_cta_visible' ),
				)
			)
		);


		// ── Hero Background ───────────────────────────────────────────────────
		$wp_customize->add_setting( 'energieburcht_hero_bg_sep', array( 'transport' => 'refresh', 'sanitize_callback' => '__return_empty_string' ) );
		$wp_customize->add_control(
			new Energieburcht_Customize_Separator_Control(
				$wp_customize,
				'energieburcht_hero_bg_sep',
				array(
					'label'           => esc_html__( 'Hero Background', 'energieburcht' ),
					'section'         => 'energieburcht_page_options',
					'active_callback' => array( $this, 'callback_hero_enabled' ),
				)
			)
		);

		// Hero Background Type
		$wp_customize->add_setting(
			'energieburcht_hero_bg_type',
			array(
				'default'           => 'color',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_key',
			)
		);
		$wp_customize->add_control(
			'energieburcht_hero_bg_type',
			array(
				'type'            => 'radio',
				'label'           => esc_html__( 'Background Type', 'energieburcht' ),
				'section'         => 'energieburcht_page_options',
				'active_callback' => array( $this, 'callback_hero_enabled' ),
				'choices'         => array(
					'color'    => esc_html__( 'Solid Color', 'energieburcht' ),
					'gradient' => esc_html__( 'Gradient', 'energieburcht' ),
					'image'    => esc_html__( 'Featured Image', 'energieburcht' ),
				),
			)
		);

		// Hero Background Color
		$wp_customize->add_setting(
			'energieburcht_hero_bg_color',
			array(
				'default'           => '#003449', // Navy
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_hero_bg_color',
				array(
					'label'           => esc_html__( 'Background Color', 'energieburcht' ),
					'section'         => 'energieburcht_page_options',
					'active_callback' => array( $this, 'callback_hero_bg_color' ),
				)
			)
		);

		// Hero Gradient Start
		$wp_customize->add_setting(
			'energieburcht_hero_gradient_start',
			array(
				'default'           => '#003449',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_hero_gradient_start',
				array(
					'label'           => esc_html__( 'Gradient Start Color', 'energieburcht' ),
					'section'         => 'energieburcht_page_options',
					'active_callback' => array( $this, 'callback_hero_bg_gradient' ),
				)
			)
		);

		// Hero Gradient End
		$wp_customize->add_setting(
			'energieburcht_hero_gradient_end',
			array(
				'default'           => '#00ACDD',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'energieburcht_hero_gradient_end',
				array(
					'label'           => esc_html__( 'Gradient End Color', 'energieburcht' ),
					'section'         => 'energieburcht_page_options',
					'active_callback' => array( $this, 'callback_hero_bg_gradient' ),
				)
			)
		);

		// Hero Overlay Color (Alpha supported)
		$wp_customize->add_setting(
			'energieburcht_hero_overlay_color',
			array(
				'default'           => 'rgba(0, 52, 73, 0.7)',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_text_field', // Needs custom sanitization for alpha, using text for now or existing alpha control
			)
		);
		// Assuming Alpha Control exists based on previous conversations, if not fallback to Color Control
		if ( class_exists( 'Energieburcht_Customize_Alpha_Color_Control' ) ) {
			$wp_customize->add_control(
				new Energieburcht_Customize_Alpha_Color_Control(
					$wp_customize,
					'energieburcht_hero_overlay_color',
					array(
						'label'   => esc_html__( 'Overlay Color', 'energieburcht' ),
						'section' => 'energieburcht_page_options',
						'active_callback' => array( $this, 'callback_hero_bg_image' ), // Also good for gradient overlay if needed, but primarily image overlay
					)
				)
			);
		} else {
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'energieburcht_hero_overlay_color',
					array(
						'label'   => esc_html__( 'Overlay Color', 'energieburcht' ),
						'section' => 'energieburcht_page_options',
						'description' => esc_html__( 'Alpha control not found, using solid color fallback.', 'energieburcht' ),
						'active_callback' => array( $this, 'callback_hero_bg_image' ),
					)
				)
			);
		}
	}

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

		// ── Widget gap (Responsive) ──────────────────────────────────────────
		$wp_customize->add_setting(
			'energieburcht_footer_gap',
			array(
				'default'           => json_encode( array(
					'desktop' => 50,
					'tablet'  => 30,
					'mobile'  => 20,
					'unit'    => 'px'
				) ),
				'transport'         => 'refresh',
				'sanitize_callback' => array( $this, 'sanitize_responsive_range' ),
			)
		);

		$wp_customize->add_control(
			new Energieburcht_Customize_Responsive_Range_Control(
				$wp_customize,
				'energieburcht_footer_gap',
				array(
					'label'       => esc_html__( 'Widget Gap', 'energieburcht' ),
					'section'     => 'energieburcht_footer_options',
					'input_attrs' => array(
						'min'   => 0,
						'max'   => 150,
						'step'  => 1,
						'units' => array( 'px', 'em', 'rem' ),
					),
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

	/**
	 * Sanitize Responsive Range Value
	 *
	 * @param string|array $value JSON string or array.
	 * @return string JSON Encoded string.
	 */
	public function sanitize_responsive_range( $value ) {
		// If it's already a string, decode it first to ensure valid JSON structure
		if ( is_string( $value ) ) {
			$decoded = json_decode( $value, true );
			if ( is_array( $decoded ) ) {
				$value = $decoded;
			}
		}

		$sanitized = array(
			'desktop' => '',
			'tablet'  => '',
			'mobile'  => '',
			'unit'    => 'px',
		);

		if ( is_array( $value ) ) {
			$sanitized['desktop'] = isset( $value['desktop'] ) ? sanitize_text_field( $value['desktop'] ) : '';
			$sanitized['tablet']  = isset( $value['tablet'] ) ? sanitize_text_field( $value['tablet'] ) : '';
			$sanitized['mobile']  = isset( $value['mobile'] ) ? sanitize_text_field( $value['mobile'] ) : '';
			$sanitized['unit']    = isset( $value['unit'] ) ? sanitize_text_field( $value['unit'] ) : 'px';
		}

		return json_encode( $sanitized );
	}



	// =========================================================================
	// Active Callbacks
	// =========================================================================

	/**
	 * Callback: Is the Hero Section enabled?
	 *
	 * @param WP_Customize_Control $control The control instance.
	 * @return bool
	 */
	public function callback_hero_enabled( $control ): bool {
		return (bool) $control->manager->get_setting( 'energieburcht_hero_enable' )->value();
	}

	/**
	 * Callback: Is Hero enabled AND Title enabled?
	 *
	 * @param WP_Customize_Control $control The control instance.
	 * @return bool
	 */
	public function callback_hero_title_visible( $control ): bool {
		if ( ! $this->callback_hero_enabled( $control ) ) {
			return false;
		}
		return (bool) $control->manager->get_setting( 'energieburcht_hero_show_title' )->value();
	}



	/**
	 * Callback: Is Hero enabled AND Excerpt enabled?
	 *
	 * @param WP_Customize_Control $control The control instance.
	 * @return bool
	 */
	public function callback_hero_excerpt_visible( $control ): bool {
		if ( ! $this->callback_hero_enabled( $control ) ) {
			return false;
		}
		return (bool) $control->manager->get_setting( 'energieburcht_hero_show_excerpt' )->value();
	}

	/**
	 * Callback: Is Hero enabled AND CTA enabled?
	 *
	 * @param WP_Customize_Control $control The control instance.
	 * @return bool
	 */
	public function callback_hero_cta_visible( $control ): bool {
		if ( ! $this->callback_hero_enabled( $control ) ) {
			return false;
		}
		return (bool) $control->manager->get_setting( 'energieburcht_hero_show_cta' )->value();
	}

	/**
	 * Callback: Is Hero enabled AND BG Type is Color?
	 *
	 * @param WP_Customize_Control $control The control instance.
	 * @return bool
	 */
	public function callback_hero_bg_color( $control ): bool {
		if ( ! $this->callback_hero_enabled( $control ) ) {
			return false;
		}
		return 'color' === $control->manager->get_setting( 'energieburcht_hero_bg_type' )->value();
	}

	/**
	 * Callback: Is Hero enabled AND BG Type is Gradient?
	 *
	 * @param WP_Customize_Control $control The control instance.
	 * @return bool
	 */
	public function callback_hero_bg_gradient( $control ): bool {
		if ( ! $this->callback_hero_enabled( $control ) ) {
			return false;
		}
		return 'gradient' === $control->manager->get_setting( 'energieburcht_hero_bg_type' )->value();
	}

	/**
	 * Callback: Is Hero enabled AND BG Type is Image?
	 *
	 * @param WP_Customize_Control $control The control instance.
	 * @return bool
	 */
	public function callback_hero_bg_image( $control ): bool {
		if ( ! $this->callback_hero_enabled( $control ) ) {
			return false;
		}
		return 'image' === $control->manager->get_setting( 'energieburcht_hero_bg_type' )->value();
	}
}
