<?php
/**
 * Asset Enqueueing Class
 *
 * Manages the registration and enqueueing of all front-end stylesheets and
 * JavaScript files. Conditional assets (e.g. back-to-top) are only loaded
 * when the corresponding Customizer setting is active.
 *
 * @package Energieburcht
 * @since   1.0.0
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Energieburcht_Enqueue
 */
final class Energieburcht_Enqueue {

	/**
	 * Single shared instance of this class.
	 *
	 * @var Energieburcht_Enqueue|null
	 */
	private static $instance = null;

	// =========================================================================
	// Singleton boilerplate
	// =========================================================================

	/**
	 * Private constructor — obtain the instance via get_instance().
	 */
	private function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_color_vars' ) );
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_block_assets' ) );
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
	// Asset loading
	// =========================================================================

	/**
	 * Entry point hooked to `wp_enqueue_scripts`.
	 *
	 * Delegates to private helpers to keep each concern isolated.
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		$this->enqueue_styles();
		$this->enqueue_scripts();
	}

	/**
	 * Register and enqueue front-end stylesheets.
	 *
	 * @return void
	 */
	private function enqueue_styles(): void {

		// Root style.css — required by WordPress for theme identification.
		wp_enqueue_style(
			'energieburcht-style',
			get_stylesheet_uri(),
			array(),
			ENERGIEBURCHT_VERSION
		);

		// Main compiled stylesheet — only enqueued when the file exists on disk.
		if ( file_exists( ENERGIEBURCHT_DIR . 'assets/css/main.css' ) ) {
			wp_enqueue_style(
				'energieburcht-main',
				ENERGIEBURCHT_URI . 'assets/css/main.css',
				array( 'energieburcht-style', 'energieburcht-base' ),
				ENERGIEBURCHT_VERSION
			);
		}

		if ( file_exists( ENERGIEBURCHT_DIR . 'assets/css/base.css' ) ) {
			wp_enqueue_style(
				'energieburcht-base',
				ENERGIEBURCHT_URI . 'assets/css/base.css',
				array( 'energieburcht-style' ),
				ENERGIEBURCHT_VERSION
			);
		}
	}

	/**
	 * Register and enqueue front-end JavaScript files.
	 *
	 * @return void
	 */
	private function enqueue_scripts(): void {

		// Primary theme script: mobile nav, sticky header. Loaded in footer,
		// depends on the jQuery bundled with WordPress.
		wp_enqueue_script(
			'energieburcht-main',
			ENERGIEBURCHT_URI . 'assets/js/main.js',
			array( 'jquery' ),
			ENERGIEBURCHT_VERSION,
			true // Load in footer.
		);

		// Projecten category filter — only on the projecten archive.
		if ( is_post_type_archive( 'projecten' ) ) {
			wp_enqueue_script(
				'energieburcht-projecten-filter',
				ENERGIEBURCHT_URI . 'assets/js/projecten-filter.js',
				array( 'jquery' ),
				ENERGIEBURCHT_VERSION,
				true
			);
			wp_localize_script(
				'energieburcht-projecten-filter',
				'projectenFilter',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'projecten_filter_nonce' ),
					'i18n'    => array(
						'previous' => esc_html__( 'Vorige', 'energieburcht' ),
						'next'     => esc_html__( 'Volgende', 'energieburcht' ),
					),
				)
			);
		}

		// Kennisitems single post — TOC builder + scrollspy.
		if ( is_singular( 'kennisitems' ) ) {
			wp_enqueue_script(
				'energieburcht-kennisitems-single',
				ENERGIEBURCHT_URI . 'assets/js/kennisitems-single.js',
				array(),
				ENERGIEBURCHT_VERSION,
				true // Load in footer.
			);
		}

		// Back-to-top button — conditionally loaded based on Customizer setting.
		if ( get_theme_mod( 'energieburcht_back_to_top_enable', false ) ) {
			// Dashicons is needed for the arrow icon inside the button.
			wp_enqueue_style( 'dashicons' );

			wp_enqueue_script(
				'energieburcht-back-to-top',
				ENERGIEBURCHT_URI . 'assets/js/back-to-top.js',
				array(),
				ENERGIEBURCHT_VERSION,
				true // Load in footer.
			);
		}
	}

	// =========================================================================
	// Colour CSS custom properties
	// =========================================================================

	/**
	 * Build the :root { --eb-* } CSS block from the live Customizer values.
	 *
	 * The palette definition (slugs, CSS variable names, defaults) lives in
	 * Energieburcht_Customizer::get_color_palette() — one source of truth for
	 * both the Customizer controls and this CSS output.
	 *
	 * theme.json references these vars, so WordPress auto-generates:
	 *   --wp--preset--color--black: var(--eb-black)
	 * which chains back to our dynamic value here.
	 *
	 * @return string Minified :root { } CSS block.
	 */
	private function generate_color_vars_css(): string {
		$css = ':root{';

		// ── Palette variables (--eb-black, --eb-blue, …) ─────────────────────
		// Palette colors are fixed in theme.json — use the same hardcoded
		// defaults here so --eb-* vars are always available for main.css.
		foreach ( Energieburcht_Customizer::get_color_palette() as $color ) {
			$css .= $color['css_var'] . ':' . $color['default'] . ';';
		}

		// ── Element variables (--eb-body-text, --eb-btn-bg, …) ───────────────
		foreach ( Energieburcht_Customizer::get_element_colors() as $key => $element ) {
			$setting_key = Energieburcht_Customizer::element_setting_key( $key );
			$value       = get_theme_mod( $setting_key, $element['default'] );

			// Accept palette var() references as-is; otherwise validate as hex.
			if ( ! preg_match( '/^var\(--eb-[a-z-]+\)$/', $value ) ) {
				$value = sanitize_hex_color( $value ) ?: $element['default'];
			}

			// Convert key (e.g. 'btn_hover_bg') to CSS var name ('--eb-btn-hover-bg').
			$css_var = '--eb-' . str_replace( '_', '-', $key );
			$css    .= $css_var . ':' . $value . ';';
		}

		$logo_width = get_theme_mod( 'energieburcht_logo_width', 60 );
		$css       .= '--eb-logo-width:' . absint( $logo_width ) . 'px;';

		// ── Header Search Colors ─────────────────────────────────────────────
		$header_search_vars = array(
			'bg'            => '#EFEFEF',
			'text'          => '#003449',
			'icon'          => '#003449',
			'icon-hover'    => '#00ACDD',
			'icon-hover-bg' => '#efebeb',
		);

		foreach ( $header_search_vars as $key => $default ) {
			$val  = get_theme_mod( 'energieburcht_header_search_' . str_replace( '-', '_', $key ) . '_color', $default );
			$css .= '--eb-header-search-' . $key . ':' . sanitize_hex_color( $val ) . ';';
			$css .= '--eb-header-search-' . $key . ':' . sanitize_hex_color( $val ) . ';';
		}

		// ── Typography ───────────────────────────────────────────────────────
		$css .= '--eb-typography-body:' . get_theme_mod( 'energieburcht_typography_body', '1rem' ) . ';';
		$css .= '--eb-typography-excerpt:' . get_theme_mod( 'energieburcht_typography_excerpt', 'clamp(1.125rem, 2vw, 1.25rem)' ) . ';';
		$css .= '--eb-typography-h1:' . get_theme_mod( 'energieburcht_typography_h1', 'clamp(2.25rem, 5vw, 3rem)' ) . ';';
		$css .= '--eb-typography-h2:' . get_theme_mod( 'energieburcht_typography_h2', 'clamp(1.5rem, 3vw, 2rem)' ) . ';';
		$css .= '--eb-typography-h3:' . get_theme_mod( 'energieburcht_typography_h3', 'clamp(1.25rem, 2.5vw, 1.5rem)' ) . ';';
		$css .= '--eb-typography-h4:' . get_theme_mod( 'energieburcht_typography_h4', 'clamp(1.125rem, 2vw, 1.25rem)' ) . ';';
		$css .= '--eb-typography-button:' . get_theme_mod( 'energieburcht_typography_button', '1rem' ) . ';';

		return $css . '}';
	}

	private function enqueue_color_vars(): void {
		// Method deprecated: Variables are now attached in enqueue_block_assets() directly
	}

	/**
	 * Override the block editor canvas background colour.
	 *
	 * WordPress defaults --wp-editor-canvas-background to #ddd; forcing it to
	 * white keeps the editor canvas consistent with the front end.
	 *
	 * @return void
	 */
	public function enqueue_editor_color_vars(): void {
		// WordPress defaults --wp-editor-canvas-background to #ddd; override
		// it to white so the editor iframe canvas matches the front end.
		wp_add_inline_style( 'wp-block-editor', ':root{--wp-editor-canvas-background:#ffffff;}' );

		// Load Gutenberg block extensions
		if ( file_exists( ENERGIEBURCHT_DIR . 'assets/js/gutenberg/button-hover-colors.js' ) ) {
			wp_enqueue_script(
				'energieburcht-button-hover-colors',
				ENERGIEBURCHT_URI . 'assets/js/gutenberg/button-hover-colors.js',
				array( 'wp-blocks', 'wp-element', 'wp-hooks', 'wp-editor', 'wp-components', 'wp-data' ),
				ENERGIEBURCHT_VERSION,
				true
			);
		}
	}

	/**
	 * Enqueue block assets for both editor and front end.
	 *
	 * @return void
	 */
	public function enqueue_block_assets(): void {
		if ( file_exists( ENERGIEBURCHT_DIR . 'assets/css/blocks.css' ) ) {
			wp_enqueue_style(
				'energieburcht-blocks',
				ENERGIEBURCHT_URI . 'assets/css/blocks.css',
				array(),
				ENERGIEBURCHT_VERSION
			);	
		} else {
			wp_register_style( 'energieburcht-blocks', false );
			wp_enqueue_style( 'energieburcht-blocks' );
		}
		
		wp_add_inline_style( 'energieburcht-blocks', $this->generate_color_vars_css() );
	}

}

