<?php
/**
 * Main Theme Class
 *
 * Acts as the central controller (Facade) for the theme. Its sole
 * responsibility is to instantiate each feature subsystem in the correct
 * dependency order and expose them via get_module().
 *
 * All feature logic lives in the dedicated sub-module classes, not here.
 *
 * @package Energieburcht
 * @since   1.0.0
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Energieburcht_Theme
 *
 * Usage: Energieburcht_Theme::get_instance();
 *
 * To retrieve a specific sub-module after boot:
 *   Energieburcht_Theme::get_instance()->get_module( 'enqueue' );
 */
final class Energieburcht_Theme {

	/**
	 * Single shared instance of this class.
	 *
	 * @var Energieburcht_Theme|null
	 */
	private static $instance = null;

	/**
	 * Keyed collection of instantiated sub-modules.
	 *
	 * @var array<string, object>
	 */
	private $modules = array();

	// =========================================================================
	// Singleton boilerplate
	// =========================================================================

	/**
	 * Private constructor — obtain the instance via get_instance().
	 */
	private function __construct() {
		$this->load_modules();
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

	/**
	 * Cloning is forbidden on a singleton.
	 */
	private function __clone() {}

	/**
	 * Un-serialising is forbidden on a singleton.
	 *
	 * @throws \Exception Always, to prevent serialization.
	 */
	public function __wakeup() {
		throw new \Exception( esc_html__( 'Singleton instances cannot be unserialized.', 'energieburcht' ) );
	}

	// =========================================================================
	// Module registry
	// =========================================================================

	/**
	 * Instantiate each subsystem in dependency order.
	 *
	 * ─ Adding a new subsystem ─────────────────────────────────────────────────
	 * 1. Create your class file in /inc/ following the naming convention.
	 * 2. Add the singleton call below with a descriptive key.
	 * ─────────────────────────────────────────────────────────────────────────
	 *
	 * @return void
	 */
	private function load_modules(): void {
		$this->modules = array(
			'setup'      => Energieburcht_Theme_Setup::get_instance(),
			'enqueue'    => Energieburcht_Enqueue::get_instance(),
			'widgets'    => Energieburcht_Widgets::get_instance(),
			'customizer' => Energieburcht_Customizer::get_instance(),
			'post_types'    => Energieburcht_Post_Types::get_instance(),
			'page_settings' => Energieburcht_Page_Settings::get_instance(),
		);
	}

	/**
	 * Retrieve a specific subsystem instance by its registered key.
	 *
	 * @param  string      $module_key Key registered in load_modules() (e.g. 'enqueue').
	 * @return object|null             Module instance, or null if the key is not registered.
	 */
	public function get_module( string $module_key ) {
		return isset( $this->modules[ $module_key ] ) ? $this->modules[ $module_key ] : null;
	}
}
