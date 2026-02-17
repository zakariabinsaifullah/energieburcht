<?php
/**
 * Theme Setup Class
 *
 * Registers core WordPress theme supports, navigation menu locations,
 * and the block-editor stylesheet. All declarations are deferred to the
 * `after_setup_theme` action so child themes can safely override them.
 *
 * @package Energieburcht
 * @since   1.0.0
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Energieburcht_Theme_Setup
 */
final class Energieburcht_Theme_Setup {

	/**
	 * Single shared instance of this class.
	 *
	 * @var Energieburcht_Theme_Setup|null
	 */
	private static $instance = null;

	// =========================================================================
	// Singleton boilerplate
	// =========================================================================

	/**
	 * Private constructor — obtain the instance via get_instance().
	 */
	private function __construct() {
		add_action( 'after_setup_theme', array( $this, 'setup' ) );
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
	// Theme supports
	// =========================================================================

	/**
	 * Register all core theme features.
	 *
	 * Hooked to `after_setup_theme` (priority 10) so child themes that hook
	 * at a higher priority number can remove or replace any declaration.
	 *
	 * @return void
	 */
	public function setup(): void {

		// Auto-insert RSS feed links in <head>.
		add_theme_support( 'automatic-feed-links' );

		// Delegate <title> tag management to WordPress core.
		add_theme_support( 'title-tag' );

		// Enable featured images (post thumbnails) on all post types.
		add_theme_support( 'post-thumbnails' );

		// Output valid HTML5 markup for core-generated elements.
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

		// Allow Customizer widgets to refresh without a full page reload.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Custom logo with flexible (non-cropped) dimensions.
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

		// Block-editor stylesheet — `editor-styles` must be declared before
		// calling add_editor_style().
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/editor-style.css' );

		// ── Navigation menu locations ─────────────────────────────────────────
		register_nav_menus(
			array(
				'primary'        => esc_html__( 'Primary Menu', 'energieburcht' ),
				'footer'         => esc_html__( 'Footer Menu', 'energieburcht' ),
				'copyright-menu' => esc_html__( 'Copyright Menu', 'energieburcht' ),
			)
		);
	}
}
