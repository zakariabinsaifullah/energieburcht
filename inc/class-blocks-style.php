<?php
/**
 * Register Blocks Styles
 *
 *
 * @package Energieburcht
 * @since   1.0.0
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Energieburcht_Blocks_Style
 */
final class Energieburcht_Blocks_Style {

	/**
	 * Single shared instance of this class.
	 *
	 * @var Energieburcht_Blocks_Style|null
	 */
	private static $instance = null;

	// =========================================================================
	// Singleton boilerplate
	// =========================================================================

	/**
	 * Private constructor — obtain the instance via get_instance().
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'register_block_styles' ) );
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
	// Block Styles registration
	// =========================================================================

	/**
	 * Register all theme block styles.
	 *
	 * @return void
	 */
	public function register_block_styles(): void {
		// Paragraph: Widget Title
		register_block_style( 
            'core/paragraph', 
            array(
                'name' => 'widget-title',
                'label' => esc_html__( 'Widget Title', 'energieburcht' )
            ) 
        );

		// Button: Blueish
		register_block_style( 
            'core/button', 
            array(
                'name' => 'blueish',
                'label' => esc_html__( 'Blueish', 'energieburcht' )
            ) 
        );

		// Button: Redish
		register_block_style( 
            'core/button', 
            array(
                'name' => 'redish',
                'label' => esc_html__( 'Redish', 'energieburcht' )
            ) 
        );

		// Button: Link
		register_block_style( 
            'core/button', 
            array(
                'name' => 'link',
                'label' => esc_html__( 'Link', 'energieburcht' )
            ) 
        );

		/**
		 * Kadence Blocks Styles
		 * 
		 * Note: These styles are registered in the Kadence Blocks plugin, but we need to re-register them here to ensure they are available in our theme.
		 */
		register_block_style( 
			'kadence/posts', // kadence-posts 
			array(
				'name' => 'energieburcht',
				'label' => esc_html__( 'Energieburcht', 'energieburcht' )
			) 
		);

		register_block_style( 
			'kadence/advancedgallery', // kadence-advancedgallery
			array(
				'name' => 'energieburcht',
				'label' => esc_html__( 'Energieburcht', 'energieburcht' )
			) 
		);
	}
}