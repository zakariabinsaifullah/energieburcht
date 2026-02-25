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
		add_action( 'init', array( $this, 'register_patterns_category' ) );
		add_action( 'init', array( $this, 'register_eb_pattern_posts_as_patterns' ) );
		add_action( 'init', array( $this, 'register_directory_patterns' ) );
		add_filter( 'render_block_core/button', array( $this, 'add_button_hover_colors' ), 10, 2 );
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
		add_editor_style( 'assets/css/base.css' );

		// Enable wide and full alignments.
		add_theme_support( 'align-wide' );

		// ── Navigation menu locations ─────────────────────────────────────────
		register_nav_menus(
			array(
				'primary'        => esc_html__( 'Primary Menu', 'energieburcht' ),
				'copyright-menu' => esc_html__( 'Copyright Menu', 'energieburcht' ),
			)
		);

		// Add support for excerpts on pages.
		add_post_type_support( 'page', 'excerpt' );

		// Add support for page templates.
		add_theme_support( 'page-templates' );
	}

	/**
	 * Register custom patterns category.
	 *
	 * @return void
	 */
	public function register_patterns_category(): void {
		register_block_pattern_category( 
			'energieburcht', 
			array( 
				'label' => esc_html__( 'Energieburcht', 'energieburcht' ) 
			) 
		);
	}

	/**
	 * Register EB patterns as WordPress block patterns.
	 *
	 * Queries all published 'eb_pattern' posts and registers them
	 * as block patterns in the 'energieburcht' category.
	 *
	 * @return void
	 */
	public function register_eb_pattern_posts_as_patterns(): void {
		if ( ! function_exists( 'register_block_pattern' ) ) {
			return;
		}

		$patterns = get_posts( array(
			'post_type'      => 'eb_pattern',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		) );

		foreach ( $patterns as $pattern ) {
			register_block_pattern(
				'energieburcht/' . $pattern->post_name,
				array(
					'title'      => $pattern->post_title,
					'categories' => array( 'energieburcht' ),
					'content'    => $pattern->post_content,
				)
			);
		}
	}

	/**
	 * Register block patterns from the patterns/ directory.
	 *
	 * Scans the patterns/ folder and registers any PHP files as block patterns,
	 * parsing header data similar to WordPress core but bypassing caching issues.
	 *
	 * @return void
	 */
	public function register_directory_patterns(): void {
		if ( ! function_exists( 'register_block_pattern' ) ) {
			return;
		}

		$dirpath = get_theme_file_path( '/patterns/' );

		if ( ! file_exists( $dirpath ) ) {
			return;
		}

		$files = glob( $dirpath . '*.php' );
		if ( ! $files ) {
			return;
		}

		$default_headers = array(
			'title'         => 'Title',
			'slug'          => 'Slug',
			'description'   => 'Description',
			'viewportWidth' => 'Viewport Width',
			'inserter'      => 'Inserter',
			'categories'    => 'Categories',
			'keywords'      => 'Keywords',
			'blockTypes'    => 'Block Types',
			'postTypes'     => 'Post Types',
			'templateTypes' => 'Template Types',
		);

		$registry = WP_Block_Patterns_Registry::get_instance();

		foreach ( $files as $file ) {
			$pattern_data = get_file_data( $file, $default_headers );

			if ( empty( $pattern_data['slug'] ) ) {
				continue;
			}

			if ( $registry->is_registered( $pattern_data['slug'] ) ) {
				continue;
			}

			// Properties that require array processing.
			foreach ( array( 'categories', 'keywords', 'blockTypes', 'postTypes', 'templateTypes' ) as $key ) {
				if ( ! empty( $pattern_data[ $key ] ) ) {
					$pattern_data[ $key ] = array_filter(
						array_map(
							'trim',
							explode( ',', $pattern_data[ $key ] )
						)
					);
				} else {
					unset( $pattern_data[ $key ] );
				}
			}

			// Properties that require booleans.
			if ( ! empty( $pattern_data['inserter'] ) ) {
				$pattern_data['inserter'] = in_array(
					strtolower( $pattern_data['inserter'] ),
					array( 'yes', 'true' ),
					true
				);
			} else {
				unset( $pattern_data['inserter'] );
			}

			if ( ! empty( $pattern_data['viewportWidth'] ) ) {
				$pattern_data['viewportWidth'] = (int) $pattern_data['viewportWidth'];
			}

			// Capture pattern content.
			ob_start();
			include $file;
			$pattern_data['content'] = ob_get_clean();

			if ( ! $pattern_data['content'] ) {
				continue;
			}

			register_block_pattern( $pattern_data['slug'], $pattern_data );
		}
	}

	/**
	 * Inject custom hover CSS variables into core/button.
	 *
	 * @param string $block_content The block content.
	 * @param array  $block         The full block, including name and attributes.
	 * @return string Modified block content.
	 */
	public function add_button_hover_colors( string $block_content, array $block ): string {
		$attrs = $block['attrs'] ?? array();

		// Check if we have Custom Hover Colors set
		$hover_bg    = $attrs['hoverBackgroundColor'] ?? '';
		$hover_text  = $attrs['hoverTextColor'] ?? '';

		if ( empty( $hover_bg ) && empty( $hover_text ) ) {
			return $block_content;
		}

		$style_string = '';
		if ( ! empty( $hover_bg ) ) {
			$style_string .= '--eb-custom-hover-bg: ' . esc_attr( $hover_bg ) . '; ';
		}
		if ( ! empty( $hover_text ) ) {
			$style_string .= '--eb-custom-hover-color: ' . esc_attr( $hover_text ) . '; ';
		}

		// Inject the variables directly into the first HTML tag (the wrapper or link).
		// We use preg_replace to elegantly insert the CSS variables.
		if ( str_contains( $block_content, 'style="' ) ) {
			// Prepend to existing style attribute to ensure our vars are parsed correctly
			$block_content = preg_replace(
				'/(<[^>]+?style=")/',
				'$1' . $style_string,
				$block_content,
				1
			);
		} else {
			// Add a new style attribute
			$block_content = preg_replace(
				'/(<[a-zA-Z0-9]+)/',
				'$1 style="' . trim( $style_string ) . '"',
				$block_content,
				1
			);
		}

		return $block_content;
	}
}
