<?php
/**
 * Widget Areas Class
 *
 * Registers all sidebar / widget areas used throughout the theme. Each area
 * corresponds to a distinct layout region — one header area and up to four
 * footer columns.
 *
 * @package Energieburcht
 * @since   1.0.0
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Energieburcht_Widgets
 */
final class Energieburcht_Widgets {

	/**
	 * Single shared instance of this class.
	 *
	 * @var Energieburcht_Widgets|null
	 */
	private static $instance = null;

	// =========================================================================
	// Singleton boilerplate
	// =========================================================================

	/**
	 * Private constructor — obtain the instance via get_instance().
	 */
	private function __construct() {
		add_action( 'widgets_init', array( $this, 'register_sidebars' ) );
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
	// Widget area registration
	// =========================================================================

	/**
	 * Register all theme widget areas.
	 *
	 * @return void
	 */
	public function register_sidebars(): void {

		// ── Header right ──────────────────────────────────────────────────────
		register_sidebar(
			array(
				'name'          => esc_html__( 'Header Right', 'energieburcht' ),
				'id'            => 'header-right',
				'description'   => esc_html__( 'Widgets displayed in the top-right of the header (e.g. search, CTA, language switcher).', 'energieburcht' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="screen-reader-text">',
				'after_title'   => '</h2>',
			)
		);

		// ── Footer Brand ──────────────────────────────────────────────────────
		register_sidebar(
			array(
				'name'          => esc_html__( 'Footer Brand', 'energieburcht' ),
				'id'            => 'footer-brand',
				'description'   => esc_html__( 'First widget area displayed in the footer.', 'energieburcht' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<div class="widget-title">',
				'after_title'   => '</div>',
			)
		);

		// ── Single Projecten sidebar ──────────────────────────────────────────
		register_sidebar(
			array(
				'name'          => esc_html__( 'Projecten Sidebar', 'energieburcht' ),
				'id'            => 'projecten-sidebar',
				'description'   => esc_html__( 'Widgets displayed in the sidebar on single Projecten pages.', 'energieburcht' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<div class="widget-title">',
				'after_title'   => '</div>',
			)
		);

		// ── Footer columns (1 – 4) ────────────────────────────────────────────
		// Using a loop avoids repetitive register_sidebar() calls and makes it
		// trivial to add or remove columns in the future.
		for ( $column = 1; $column <= 4; $column++ ) {
			register_sidebar(
				array(
					/* translators: %d: Column number (1–4). */
					'name'          => sprintf( esc_html__( 'Footer Column %d', 'energieburcht' ), $column ),
					'id'            => 'footer-' . $column,
					/* translators: %d: Column number (1–4). */
					'description'   => sprintf( esc_html__( 'Widgets for footer column %d.', 'energieburcht' ), $column ),
					'before_widget' => '<section id="%1$s" class="widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<div class="widget-title">',
					'after_title'   => '</div>',
				)
			);
		}
	}
}
