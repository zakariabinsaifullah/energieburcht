<?php
/**
 * Energieburcht Theme Bootstrap
 *
 * This file is the sole entry point for the theme. It:
 *   1. Defines theme-wide constants.
 *   2. Registers the SPL autoloader so class files are loaded on demand.
 *   3. Boots the central theme singleton, which in turn initialises all subsystems.
 *
 * No feature logic should live here — delegate everything to a dedicated class.
 *
 * @package Energieburcht
 * @since   1.0.0
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =============================================================================
// Constants
// =============================================================================

/** @var string Theme version — bump on every release to bust asset caches. */
define( 'ENERGIEBURCHT_VERSION', '1.0.0' );

/** @var string Absolute filesystem path to the theme root (trailing slash). */
define( 'ENERGIEBURCHT_DIR', trailingslashit( get_template_directory() ) );

/** @var string Public URL to the theme root (trailing slash). */
define( 'ENERGIEBURCHT_URI', trailingslashit( get_template_directory_uri() ) );

// =============================================================================
// SPL Autoloader
// =============================================================================

/**
 * PSR-0-inspired autoloader that follows WordPress file-naming conventions.
 *
 * Naming convention (all others are derived automatically):
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │  Class name                               │ File path                   │
 * ├─────────────────────────────────────────────────────────────────────────┤
 * │  Energieburcht_Theme                      │ inc/class-theme.php         │
 * │  Energieburcht_Theme_Setup                │ inc/class-theme-setup.php   │
 * │  Energieburcht_Enqueue                    │ inc/class-enqueue.php       │
 * │  Energieburcht_Widgets                    │ inc/class-widgets.php       │
 * │  Energieburcht_Customizer                 │ inc/class-customizer.php    │
 * │  Energieburcht_Customize_Editor_Control   │ inc/controls/class-control-editor.php │
 * │  Energieburcht_Customize_Range_Control    │ inc/controls/class-control-range.php  │
 * └─────────────────────────────────────────────────────────────────────────┘
 *
 * @param string $class_name Fully-qualified class name provided by PHP's autoloader stack.
 * @return void
 */
function energieburcht_autoloader( string $class_name ): void {

	// Only handle classes that belong to this theme.
	if ( 0 !== strpos( $class_name, 'Energieburcht_' ) ) {
		return;
	}

	// Explicit map for classes whose file paths cannot be derived from the
	// standard naming convention (e.g. custom Customizer controls live in a
	// dedicated sub-directory).
	$class_map = array(
		'Energieburcht_Customize_Editor_Control' => ENERGIEBURCHT_DIR . 'inc/controls/class-control-editor.php',
		'Energieburcht_Customize_Range_Control'  => ENERGIEBURCHT_DIR . 'inc/controls/class-control-range.php',
	);

	if ( isset( $class_map[ $class_name ] ) ) {
		require_once $class_map[ $class_name ];
		return;
	}

	// Convention fallback:
	//   "Energieburcht_Theme_Setup" → strip prefix → "Theme_Setup"
	//   → snake_case to kebab-case  → "theme-setup"
	//   → prepend "class-" + ".php" → "class-theme-setup.php"
	$suffix    = substr( $class_name, strlen( 'Energieburcht_' ) );
	$file_name = 'class-' . strtolower( str_replace( '_', '-', $suffix ) ) . '.php';
	$file_path = ENERGIEBURCHT_DIR . 'inc/' . $file_name;

	if ( file_exists( $file_path ) ) {
		require_once $file_path;
	}
}

spl_autoload_register( 'energieburcht_autoloader' );

// =============================================================================
// Bootstrap
// =============================================================================

/**
 * Boot the theme. All subsystems (setup, enqueue, widgets, customizer) are
 * initialised inside Energieburcht_Theme::get_instance() via load_modules().
 */
Energieburcht_Theme::get_instance();
