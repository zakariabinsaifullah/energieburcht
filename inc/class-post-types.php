<?php
/**
 * Custom Post Types & Taxonomies
 *
 * Registers all Custom Post Types and taxonomies for this theme:
 *   - 'oplossingen' (Solutions) CPT + 'oplossingen_type' taxonomy
 *   - 'themas' (Themes) CPT
 *   - 'sectoren' (Sectors) CPT
 *   - 'projecten' (Projects) CPT
 *   - 'whitepapers' (Whitepapers) CPT
 *   - 'webinars' (Webinars) CPT
 *   - 'faq' (FAQ) CPT + 'faq_cat' taxonomy with FAQPage schema output
 *
 * @package Energieburcht
 * @since   1.0.0
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Energieburcht_Post_Types
 */
final class Energieburcht_Post_Types {

	/**
	 * Single shared instance of this class.
	 *
	 * @var Energieburcht_Post_Types|null
	 */
	private static $instance = null;

	// =========================================================================
	// Singleton boilerplate
	// =========================================================================

	/**
	 * Private constructor — obtain the instance via get_instance().
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_action( 'init', array( $this, 'register_themas_post_type' ) );
		add_action( 'init', array( $this, 'register_sectoren_post_type' ) );
		add_action( 'init', array( $this, 'register_projecten_post_type' ) );
		add_action( 'init', array( $this, 'register_whitepapers_post_type' ) );
		add_action( 'init', array( $this, 'register_webinars_post_type' ) );
		add_action( 'init', array( $this, 'register_faq_post_type' ) );
		add_action( 'init', array( $this, 'register_faq_taxonomy' ) );
		add_action( 'wp_head', array( $this, 'output_faq_schema' ) );
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
	// Registration
	// =========================================================================

	/**
	 * Register the 'oplossingen' Custom Post Type.
	 *
	 * @return void
	 */
	public function register_post_type(): void {
		$labels = array(
			'name'                  => esc_html_x( 'Solutions', 'post type general name', 'energieburcht' ),
			'singular_name'         => esc_html_x( 'Solution', 'post type singular name', 'energieburcht' ),
			'menu_name'             => esc_html_x( 'Solutions', 'admin menu', 'energieburcht' ),
			'name_admin_bar'        => esc_html_x( 'Solution', 'add new on admin bar', 'energieburcht' ),
			'add_new'               => esc_html__( 'Add New', 'energieburcht' ),
			'add_new_item'          => esc_html__( 'Add New Solution', 'energieburcht' ),
			'new_item'              => esc_html__( 'New Solution', 'energieburcht' ),
			'edit_item'             => esc_html__( 'Edit Solution', 'energieburcht' ),
			'view_item'             => esc_html__( 'View Solution', 'energieburcht' ),
			'all_items'             => esc_html__( 'All Solutions', 'energieburcht' ),
			'search_items'          => esc_html__( 'Search Solutions', 'energieburcht' ),
			'parent_item_colon'     => esc_html__( 'Parent Solutions:', 'energieburcht' ),
			'not_found'             => esc_html__( 'No solutions found.', 'energieburcht' ),
			'not_found_in_trash'    => esc_html__( 'No solutions found in Trash.', 'energieburcht' ),
			'featured_image'        => esc_html__( 'Solution Image', 'energieburcht' ),
			'set_featured_image'    => esc_html__( 'Set solution image', 'energieburcht' ),
			'remove_featured_image' => esc_html__( 'Remove solution image', 'energieburcht' ),
			'use_featured_image'    => esc_html__( 'Use as solution image', 'energieburcht' ),
			'archives'              => esc_html__( 'Solution Archives', 'energieburcht' ),
			'insert_into_item'      => esc_html__( 'Insert into solution', 'energieburcht' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this solution', 'energieburcht' ),
			'items_list'            => esc_html__( 'Solutions list', 'energieburcht' ),
			'items_list_navigation' => esc_html__( 'Solutions list navigation', 'energieburcht' ),
			'filter_items_list'     => esc_html__( 'Filter solutions list', 'energieburcht' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => esc_html__( 'Solutions offered by Energieburcht.', 'energieburcht' ),
			// Supported editor features.
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			// Taxonomy associations.
			'taxonomies'         => array( 'oplossingen_type' ),
			// Visibility & behaviour.
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_admin_bar'  => true,
			// Flat (non-hierarchical) structure — like posts, not pages.
			'hierarchical'       => false,
			// Enable an archive page at /oplossingen/.
			'has_archive'        => true,
			// Enable Gutenberg block editor & REST API access.
			'show_in_rest'       => true,
			'rest_base'          => 'oplossingen',
			// URL rewrite.
			'rewrite'            => array(
				'slug'       => 'oplossingen',
				'with_front' => false,
			),
			// Capability mapping — uses standard 'post' caps.
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			// Admin menu position (below Comments).
			'menu_position'      => 25,
			'menu_icon'          => 'dashicons-lightbulb',
		);

		register_post_type( 'oplossingen', $args );
	}

	/**
	 * Register the 'oplossingen_type' taxonomy (Solution Type).
	 *
	 * Used for internal organisation of solutions; exposed in the REST API
	 * so the block editor can assign terms without leaving Gutenberg.
	 *
	 * @return void
	 */
	public function register_taxonomy(): void {
		$labels = array(
			'name'                       => esc_html_x( 'Solution Types', 'taxonomy general name', 'energieburcht' ),
			'singular_name'              => esc_html_x( 'Solution Type', 'taxonomy singular name', 'energieburcht' ),
			'search_items'               => esc_html__( 'Search Solution Types', 'energieburcht' ),
			'popular_items'              => esc_html__( 'Popular Solution Types', 'energieburcht' ),
			'all_items'                  => esc_html__( 'All Solution Types', 'energieburcht' ),
			'parent_item'                => esc_html__( 'Parent Solution Type', 'energieburcht' ),
			'parent_item_colon'          => esc_html__( 'Parent Solution Type:', 'energieburcht' ),
			'edit_item'                  => esc_html__( 'Edit Solution Type', 'energieburcht' ),
			'update_item'                => esc_html__( 'Update Solution Type', 'energieburcht' ),
			'add_new_item'               => esc_html__( 'Add New Solution Type', 'energieburcht' ),
			'new_item_name'              => esc_html__( 'New Solution Type Name', 'energieburcht' ),
			'separate_items_with_commas' => esc_html__( 'Separate solution types with commas', 'energieburcht' ),
			'add_or_remove_items'        => esc_html__( 'Add or remove solution types', 'energieburcht' ),
			'choose_from_most_used'      => esc_html__( 'Choose from the most used solution types', 'energieburcht' ),
			'not_found'                  => esc_html__( 'No solution types found.', 'energieburcht' ),
			'menu_name'                  => esc_html__( 'Solution Types', 'energieburcht' ),
			'back_to_items'              => esc_html__( '&larr; Back to Solution Types', 'energieburcht' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => false,
			// Expose to Gutenberg and the REST API.
			'show_in_rest'      => true,
			'rest_base'         => 'oplossingen-type',
			'rewrite'           => array(
				'slug'         => 'oplossingen-type',
				'with_front'   => false,
				'hierarchical' => true,
			),
		);

		register_taxonomy( 'oplossingen_type', array( 'oplossingen' ), $args );
	}

	/**
	 * Register the 'themas' (Themes) Custom Post Type.
	 *
	 * @return void
	 */
	public function register_themas_post_type(): void {
		$labels = array(
			'name'                  => esc_html_x( 'Themes', 'post type general name', 'energieburcht' ),
			'singular_name'         => esc_html_x( 'Theme', 'post type singular name', 'energieburcht' ),
			'menu_name'             => esc_html_x( 'Themes', 'admin menu', 'energieburcht' ),
			'name_admin_bar'        => esc_html_x( 'Theme', 'add new on admin bar', 'energieburcht' ),
			'add_new'               => esc_html__( 'Add New', 'energieburcht' ),
			'add_new_item'          => esc_html__( 'Add New Theme', 'energieburcht' ),
			'new_item'              => esc_html__( 'New Theme', 'energieburcht' ),
			'edit_item'             => esc_html__( 'Edit Theme', 'energieburcht' ),
			'view_item'             => esc_html__( 'View Theme', 'energieburcht' ),
			'all_items'             => esc_html__( 'All Themes', 'energieburcht' ),
			'search_items'          => esc_html__( 'Search Themes', 'energieburcht' ),
			'parent_item_colon'     => esc_html__( 'Parent Themes:', 'energieburcht' ),
			'not_found'             => esc_html__( 'No themes found.', 'energieburcht' ),
			'not_found_in_trash'    => esc_html__( 'No themes found in Trash.', 'energieburcht' ),
			'featured_image'        => esc_html__( 'Theme Image', 'energieburcht' ),
			'set_featured_image'    => esc_html__( 'Set theme image', 'energieburcht' ),
			'remove_featured_image' => esc_html__( 'Remove theme image', 'energieburcht' ),
			'use_featured_image'    => esc_html__( 'Use as theme image', 'energieburcht' ),
			'archives'              => esc_html__( 'Theme Archives', 'energieburcht' ),
			'insert_into_item'      => esc_html__( 'Insert into theme', 'energieburcht' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this theme', 'energieburcht' ),
			'items_list'            => esc_html__( 'Themes list', 'energieburcht' ),
			'items_list_navigation' => esc_html__( 'Themes list navigation', 'energieburcht' ),
			'filter_items_list'     => esc_html__( 'Filter themes list', 'energieburcht' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => esc_html__( 'Themes offered by Energieburcht.', 'energieburcht' ),
			// Supported editor features.
			'supports'           => array( 'title', 'editor', 'thumbnail', 'revisions' ),
			// Visibility & behaviour.
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_admin_bar'  => true,
			'hierarchical'       => false,
			// Enable a dedicated overview page at /themas/.
			'has_archive'        => true,
			// Enable Gutenberg block editor & REST API access.
			'show_in_rest'       => true,
			'rest_base'          => 'themas',
			// URL rewrite.
			'rewrite'            => array(
				'slug'       => 'themas',
				'with_front' => false,
			),
			// Capability mapping — uses standard 'post' caps.
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			// Admin menu position (below Solutions).
			'menu_position'      => 26,
			'menu_icon'          => 'dashicons-art',
		);

		register_post_type( 'themas', $args );
	}

	/**
	 * Register the 'sectoren' (Sectors) Custom Post Type.
	 *
	 * Acts as a primary landing hub for industry-specific content. REST API
	 * support is enabled so block-based layouts can query and render sector
	 * entries via the block editor and headless/hybrid front-ends.
	 *
	 * @return void
	 */
	public function register_sectoren_post_type(): void {
		$labels = array(
			'name'                  => esc_html_x( 'Sectors', 'post type general name', 'energieburcht' ),
			'singular_name'         => esc_html_x( 'Sector', 'post type singular name', 'energieburcht' ),
			'menu_name'             => esc_html_x( 'Sectors', 'admin menu', 'energieburcht' ),
			'name_admin_bar'        => esc_html_x( 'Sector', 'add new on admin bar', 'energieburcht' ),
			'add_new'               => esc_html__( 'Add New', 'energieburcht' ),
			'add_new_item'          => esc_html__( 'Add New Sector', 'energieburcht' ),
			'new_item'              => esc_html__( 'New Sector', 'energieburcht' ),
			'edit_item'             => esc_html__( 'Edit Sector', 'energieburcht' ),
			'view_item'             => esc_html__( 'View Sector', 'energieburcht' ),
			'all_items'             => esc_html__( 'All Sectors', 'energieburcht' ),
			'search_items'          => esc_html__( 'Search Sectors', 'energieburcht' ),
			'parent_item_colon'     => esc_html__( 'Parent Sectors:', 'energieburcht' ),
			'not_found'             => esc_html__( 'No sectors found.', 'energieburcht' ),
			'not_found_in_trash'    => esc_html__( 'No sectors found in Trash.', 'energieburcht' ),
			'featured_image'        => esc_html__( 'Sector Image', 'energieburcht' ),
			'set_featured_image'    => esc_html__( 'Set sector image', 'energieburcht' ),
			'remove_featured_image' => esc_html__( 'Remove sector image', 'energieburcht' ),
			'use_featured_image'    => esc_html__( 'Use as sector image', 'energieburcht' ),
			'archives'              => esc_html__( 'Sector Archives', 'energieburcht' ),
			'insert_into_item'      => esc_html__( 'Insert into sector', 'energieburcht' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this sector', 'energieburcht' ),
			'items_list'            => esc_html__( 'Sectors list', 'energieburcht' ),
			'items_list_navigation' => esc_html__( 'Sectors list navigation', 'energieburcht' ),
			'filter_items_list'     => esc_html__( 'Filter sectors list', 'energieburcht' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => esc_html__( 'Industry-specific sector landing pages for Energieburcht.', 'energieburcht' ),
			// Supported editor features.
			'supports'           => array( 'title', 'editor', 'thumbnail' ),
			// Visibility & behaviour.
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_admin_bar'  => true,
			'hierarchical'       => false,
			// Enable a dedicated overview page at /sectoren/.
			'has_archive'        => true,
			// Full REST API exposure for block-based layouts.
			'show_in_rest'       => true,
			'rest_base'          => 'sectoren',
			// URL rewrite.
			'rewrite'            => array(
				'slug'       => 'sectoren',
				'with_front' => false,
			),
			// Capability mapping — uses standard 'post' caps.
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			// Admin menu position (below Themes).
			'menu_position'      => 27,
			'menu_icon'          => 'dashicons-building',
		);

		register_post_type( 'sectoren', $args );
	}

	/**
	 * Register the 'projecten' (Projects) Custom Post Type.
	 *
	 * @return void
	 */
	public function register_projecten_post_type(): void {
		$labels = array(
			'name'                  => esc_html_x( 'Projects', 'post type general name', 'energieburcht' ),
			'singular_name'         => esc_html_x( 'Project', 'post type singular name', 'energieburcht' ),
			'menu_name'             => esc_html_x( 'Projects', 'admin menu', 'energieburcht' ),
			'name_admin_bar'        => esc_html_x( 'Project', 'add new on admin bar', 'energieburcht' ),
			'add_new'               => esc_html__( 'Add New', 'energieburcht' ),
			'add_new_item'          => esc_html__( 'Add New Project', 'energieburcht' ),
			'new_item'              => esc_html__( 'New Project', 'energieburcht' ),
			'edit_item'             => esc_html__( 'Edit Project', 'energieburcht' ),
			'view_item'             => esc_html__( 'View Project', 'energieburcht' ),
			'all_items'             => esc_html__( 'All Projects', 'energieburcht' ),
			'search_items'          => esc_html__( 'Search Projects', 'energieburcht' ),
			'parent_item_colon'     => esc_html__( 'Parent Projects:', 'energieburcht' ),
			'not_found'             => esc_html__( 'No projects found.', 'energieburcht' ),
			'not_found_in_trash'    => esc_html__( 'No projects found in Trash.', 'energieburcht' ),
			'featured_image'        => esc_html__( 'Project Image', 'energieburcht' ),
			'set_featured_image'    => esc_html__( 'Set project image', 'energieburcht' ),
			'remove_featured_image' => esc_html__( 'Remove project image', 'energieburcht' ),
			'use_featured_image'    => esc_html__( 'Use as project image', 'energieburcht' ),
			'archives'              => esc_html__( 'Project Archives', 'energieburcht' ),
			'insert_into_item'      => esc_html__( 'Insert into project', 'energieburcht' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this project', 'energieburcht' ),
			'items_list'            => esc_html__( 'Projects list', 'energieburcht' ),
			'items_list_navigation' => esc_html__( 'Projects list navigation', 'energieburcht' ),
			'filter_items_list'     => esc_html__( 'Filter projects list', 'energieburcht' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => esc_html__( 'Projects completed by Energieburcht.', 'energieburcht' ),
			// Supported editor features.
			'supports'           => array( 'title', 'editor', 'thumbnail' ),
			// Visibility & behaviour.
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_admin_bar'  => true,
			'hierarchical'       => false,
			// Enable a dedicated overview page at /projecten/.
			'has_archive'        => true,
			// Enable Gutenberg block editor & REST API access.
			'show_in_rest'       => true,
			'rest_base'          => 'projecten',
			// URL rewrite.
			'rewrite'            => array(
				'slug'       => 'projecten',
				'with_front' => false,
			),
			// Capability mapping — uses standard 'post' caps.
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			// Admin menu position (below Sectors).
			'menu_position'      => 28,
			'menu_icon'          => 'dashicons-portfolio',
		);

		register_post_type( 'projecten', $args );
	}

	/**
	 * Register the 'whitepapers' Custom Post Type.
	 *
	 * @return void
	 */
	public function register_whitepapers_post_type(): void {
		$labels = array(
			'name'                  => esc_html_x( 'Whitepapers', 'post type general name', 'energieburcht' ),
			'singular_name'         => esc_html_x( 'Whitepaper', 'post type singular name', 'energieburcht' ),
			'menu_name'             => esc_html_x( 'Whitepapers', 'admin menu', 'energieburcht' ),
			'name_admin_bar'        => esc_html_x( 'Whitepaper', 'add new on admin bar', 'energieburcht' ),
			'add_new'               => esc_html__( 'Add New', 'energieburcht' ),
			'add_new_item'          => esc_html__( 'Add New Whitepaper', 'energieburcht' ),
			'new_item'              => esc_html__( 'New Whitepaper', 'energieburcht' ),
			'edit_item'             => esc_html__( 'Edit Whitepaper', 'energieburcht' ),
			'view_item'             => esc_html__( 'View Whitepaper', 'energieburcht' ),
			'all_items'             => esc_html__( 'All Whitepapers', 'energieburcht' ),
			'search_items'          => esc_html__( 'Search Whitepapers', 'energieburcht' ),
			'parent_item_colon'     => esc_html__( 'Parent Whitepapers:', 'energieburcht' ),
			'not_found'             => esc_html__( 'No whitepapers found.', 'energieburcht' ),
			'not_found_in_trash'    => esc_html__( 'No whitepapers found in Trash.', 'energieburcht' ),
			'featured_image'        => esc_html__( 'Whitepaper Cover', 'energieburcht' ),
			'set_featured_image'    => esc_html__( 'Set whitepaper cover', 'energieburcht' ),
			'remove_featured_image' => esc_html__( 'Remove whitepaper cover', 'energieburcht' ),
			'use_featured_image'    => esc_html__( 'Use as whitepaper cover', 'energieburcht' ),
			'archives'              => esc_html__( 'Whitepaper Archives', 'energieburcht' ),
			'insert_into_item'      => esc_html__( 'Insert into whitepaper', 'energieburcht' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this whitepaper', 'energieburcht' ),
			'items_list'            => esc_html__( 'Whitepapers list', 'energieburcht' ),
			'items_list_navigation' => esc_html__( 'Whitepapers list navigation', 'energieburcht' ),
			'filter_items_list'     => esc_html__( 'Filter whitepapers list', 'energieburcht' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => esc_html__( 'Whitepapers published by Energieburcht.', 'energieburcht' ),
			'supports'           => array( 'title', 'editor', 'thumbnail' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_admin_bar'  => true,
			'hierarchical'       => false,
			'has_archive'        => true,
			'show_in_rest'       => true,
			'rest_base'          => 'whitepapers',
			'rewrite'            => array(
				'slug'       => 'whitepapers',
				'with_front' => false,
			),
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			'menu_position'      => 29,
			'menu_icon'          => 'dashicons-media-document',
		);

		register_post_type( 'whitepapers', $args );
	}

	/**
	 * Register the 'webinars' Custom Post Type.
	 *
	 * @return void
	 */
	public function register_webinars_post_type(): void {
		$labels = array(
			'name'                  => esc_html_x( 'Webinars', 'post type general name', 'energieburcht' ),
			'singular_name'         => esc_html_x( 'Webinar', 'post type singular name', 'energieburcht' ),
			'menu_name'             => esc_html_x( 'Webinars', 'admin menu', 'energieburcht' ),
			'name_admin_bar'        => esc_html_x( 'Webinar', 'add new on admin bar', 'energieburcht' ),
			'add_new'               => esc_html__( 'Add New', 'energieburcht' ),
			'add_new_item'          => esc_html__( 'Add New Webinar', 'energieburcht' ),
			'new_item'              => esc_html__( 'New Webinar', 'energieburcht' ),
			'edit_item'             => esc_html__( 'Edit Webinar', 'energieburcht' ),
			'view_item'             => esc_html__( 'View Webinar', 'energieburcht' ),
			'all_items'             => esc_html__( 'All Webinars', 'energieburcht' ),
			'search_items'          => esc_html__( 'Search Webinars', 'energieburcht' ),
			'parent_item_colon'     => esc_html__( 'Parent Webinars:', 'energieburcht' ),
			'not_found'             => esc_html__( 'No webinars found.', 'energieburcht' ),
			'not_found_in_trash'    => esc_html__( 'No webinars found in Trash.', 'energieburcht' ),
			'featured_image'        => esc_html__( 'Webinar Thumbnail', 'energieburcht' ),
			'set_featured_image'    => esc_html__( 'Set webinar thumbnail', 'energieburcht' ),
			'remove_featured_image' => esc_html__( 'Remove webinar thumbnail', 'energieburcht' ),
			'use_featured_image'    => esc_html__( 'Use as webinar thumbnail', 'energieburcht' ),
			'archives'              => esc_html__( 'Webinar Archives', 'energieburcht' ),
			'insert_into_item'      => esc_html__( 'Insert into webinar', 'energieburcht' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this webinar', 'energieburcht' ),
			'items_list'            => esc_html__( 'Webinars list', 'energieburcht' ),
			'items_list_navigation' => esc_html__( 'Webinars list navigation', 'energieburcht' ),
			'filter_items_list'     => esc_html__( 'Filter webinars list', 'energieburcht' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => esc_html__( 'Webinars hosted by Energieburcht.', 'energieburcht' ),
			'supports'           => array( 'title', 'editor', 'thumbnail' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_admin_bar'  => true,
			'hierarchical'       => false,
			'has_archive'        => true,
			'show_in_rest'       => true,
			'rest_base'          => 'webinars',
			'rewrite'            => array(
				'slug'       => 'webinars',
				'with_front' => false,
			),
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			'menu_position'      => 30,
			'menu_icon'          => 'dashicons-video-alt2',
		);

		register_post_type( 'webinars', $args );
	}

	/**
	 * Register the 'faq' Custom Post Type.
	 *
	 * 'title'  → the Question text.
	 * 'editor' → the Answer text.
	 *
	 * Archive is intentionally placed under /kenniscentrum/faq/ to nest it
	 * inside the knowledge-centre section of the site.
	 *
	 * @return void
	 */
	public function register_faq_post_type(): void {
		$labels = array(
			'name'                  => esc_html_x( 'FAQ', 'post type general name', 'energieburcht' ),
			'singular_name'         => esc_html_x( 'FAQ Item', 'post type singular name', 'energieburcht' ),
			'menu_name'             => esc_html_x( 'FAQ', 'admin menu', 'energieburcht' ),
			'name_admin_bar'        => esc_html_x( 'FAQ Item', 'add new on admin bar', 'energieburcht' ),
			'add_new'               => esc_html__( 'Add New', 'energieburcht' ),
			'add_new_item'          => esc_html__( 'Add New Question', 'energieburcht' ),
			'new_item'              => esc_html__( 'New Question', 'energieburcht' ),
			'edit_item'             => esc_html__( 'Edit Question', 'energieburcht' ),
			'view_item'             => esc_html__( 'View Question', 'energieburcht' ),
			'all_items'             => esc_html__( 'All Questions', 'energieburcht' ),
			'search_items'          => esc_html__( 'Search FAQ', 'energieburcht' ),
			'parent_item_colon'     => esc_html__( 'Parent Questions:', 'energieburcht' ),
			'not_found'             => esc_html__( 'No questions found.', 'energieburcht' ),
			'not_found_in_trash'    => esc_html__( 'No questions found in Trash.', 'energieburcht' ),
			'archives'              => esc_html__( 'FAQ Archive', 'energieburcht' ),
			'insert_into_item'      => esc_html__( 'Insert into question', 'energieburcht' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this question', 'energieburcht' ),
			'items_list'            => esc_html__( 'Questions list', 'energieburcht' ),
			'items_list_navigation' => esc_html__( 'Questions list navigation', 'energieburcht' ),
			'filter_items_list'     => esc_html__( 'Filter questions list', 'energieburcht' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => esc_html__( 'Frequently Asked Questions for Energieburcht.', 'energieburcht' ),
			// Title = Question, Editor = Answer. No thumbnail needed for FAQs.
			'supports'           => array( 'title', 'editor' ),
			'taxonomies'         => array( 'faq_cat' ),
			// Visibility & behaviour.
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_admin_bar'  => true,
			'hierarchical'       => false,
			// Archive at /kenniscentrum/faq/ (string overrides the default slug).
			'has_archive'        => 'kenniscentrum/faq',
			// Gutenberg block editor & REST API.
			'show_in_rest'       => true,
			'rest_base'          => 'faq',
			// Singles live at /kenniscentrum/faq/{slug}/.
			'rewrite'            => array(
				'slug'       => 'kenniscentrum/faq',
				'with_front' => false,
			),
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			'menu_position'      => 31,
			'menu_icon'          => 'dashicons-editor-help',
		);

		register_post_type( 'faq', $args );
	}

	/**
	 * Register the 'faq_cat' taxonomy (FAQ Categories).
	 *
	 * Allows FAQ items to be grouped by topic (e.g. Solutions, Themes) so
	 * Gutenberg Query blocks can filter by category on the front end.
	 *
	 * @return void
	 */
	public function register_faq_taxonomy(): void {
		$labels = array(
			'name'                       => esc_html_x( 'FAQ Categories', 'taxonomy general name', 'energieburcht' ),
			'singular_name'              => esc_html_x( 'FAQ Category', 'taxonomy singular name', 'energieburcht' ),
			'search_items'               => esc_html__( 'Search FAQ Categories', 'energieburcht' ),
			'popular_items'              => esc_html__( 'Popular FAQ Categories', 'energieburcht' ),
			'all_items'                  => esc_html__( 'All FAQ Categories', 'energieburcht' ),
			'parent_item'                => esc_html__( 'Parent FAQ Category', 'energieburcht' ),
			'parent_item_colon'          => esc_html__( 'Parent FAQ Category:', 'energieburcht' ),
			'edit_item'                  => esc_html__( 'Edit FAQ Category', 'energieburcht' ),
			'update_item'                => esc_html__( 'Update FAQ Category', 'energieburcht' ),
			'add_new_item'               => esc_html__( 'Add New FAQ Category', 'energieburcht' ),
			'new_item_name'              => esc_html__( 'New FAQ Category Name', 'energieburcht' ),
			'separate_items_with_commas' => esc_html__( 'Separate categories with commas', 'energieburcht' ),
			'add_or_remove_items'        => esc_html__( 'Add or remove FAQ categories', 'energieburcht' ),
			'choose_from_most_used'      => esc_html__( 'Choose from the most used categories', 'energieburcht' ),
			'not_found'                  => esc_html__( 'No FAQ categories found.', 'energieburcht' ),
			'menu_name'                  => esc_html__( 'FAQ Categories', 'energieburcht' ),
			'back_to_items'              => esc_html__( '&larr; Back to FAQ Categories', 'energieburcht' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => false,
			'show_in_rest'      => true,
			'rest_base'         => 'faq-cat',
			'rewrite'           => array(
				'slug'         => 'kenniscentrum/faq-cat',
				'with_front'   => false,
				'hierarchical' => true,
			),
		);

		register_taxonomy( 'faq_cat', array( 'faq' ), $args );
	}

	/**
	 * Output FAQPage JSON-LD schema on the FAQ archive page.
	 *
	 * Fires on `wp_head`. Produces a schema.org/FAQPage graph covering every
	 * published FAQ item visible on the current archive page, enabling rich
	 * results (accordion snippets) in Google Search.
	 *
	 * @return void
	 */
	public function output_faq_schema(): void {
		// Only run on the FAQ archive (and paginated pages of it).
		if ( ! is_post_type_archive( 'faq' ) ) {
			return;
		}

		// Re-use the current main query — same posts the template will render.
		global $wp_query;

		$entities = array();

		foreach ( $wp_query->posts as $post ) {
			// Strip shortcodes and block markup; keep plain-text answer.
			$answer = wp_strip_all_tags(
				apply_filters( 'the_content', $post->post_content )
			);

			if ( empty( $answer ) ) {
				continue;
			}

			$entities[] = array(
				'@type'          => 'Question',
				'name'           => wp_strip_all_tags( get_the_title( $post ) ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $answer,
				),
			);
		}

		if ( empty( $entities ) ) {
			return;
		}

		$schema = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $entities,
		);

		printf(
			'<script type="application/ld+json">%s</script>' . "\n",
			wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT )
		);
	}
}
