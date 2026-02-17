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
}
