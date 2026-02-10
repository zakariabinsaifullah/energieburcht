<?php
/**
 * Energieburcht functions and definitions
 *
 * @package energieburcht
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define theme constants.
define( 'ENERGIEBURCHT_VERSION', '1.0.0' );
define( 'ENERGIEBURCHT_DIR', get_template_directory() );
define( 'ENERGIEBURCHT_URI', get_template_directory_uri() );

/**
 * Autoloader for theme classes.
 *
 * @param string $class_name Class name.
 */
function energieburcht_autoloader( $class_name ) {
	if ( strpos( $class_name, 'Energieburcht_') !== 0 ) {
		return;
	}

	$file_name = str_replace( 
		array( 'energieburcht_', '_' ), 
		array( '', '-' ), 
		strtolower( $class_name ) 
	);
    
	$file = ENERGIEBURCHT_DIR . '/inc/class-' . $file_name . '.php';

	if ( file_exists( $file ) ) {
		require_once $file;
	}
}
spl_autoload_register( 'energieburcht_autoloader' );

/**
 * Initialize the theme.
 */
new Energieburcht_Theme_Setup();
new Energieburcht_Enqueue();
new Energieburcht_Widgets();
new Energieburcht_Customizer();
