<?php
/**
 * Custom Post Types & Taxonomies
 *
 * Registers all Custom Post Types and taxonomies for this theme:
 *   - 'projecten' (Projects) CPT + 'projecten-categorie' taxonomy
 *   - 'kennisitems' (Knowledge Items) CPT + 'kennisitems-categorie' taxonomy
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
		add_action( 'init',            array( $this, 'register_projecten_post_type' ) );
		add_action( 'init',            array( $this, 'register_projecten_taxonomy' ) );
		add_action( 'init',            array( $this, 'register_kennisitems_post_type' ) );
		add_action( 'init',            array( $this, 'register_kennisitems_taxonomy' ) );
		add_action( 'pre_get_posts',               array( $this, 'set_projecten_posts_per_page' ) );
		add_action( 'wp_ajax_projecten_filter',        array( $this, 'ajax_projecten_filter' ) );
		add_action( 'wp_ajax_nopriv_projecten_filter', array( $this, 'ajax_projecten_filter' ) );
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
	 * Register the 'projecten' (Projects) Custom Post Type.
	 *
	 * @return void
	 */
	public function register_projecten_post_type(): void {
		$labels = array(
			'name'                  => esc_html_x( 'Projecten', 'post type general name', 'energieburcht' ),
			'singular_name'         => esc_html_x( 'Project', 'post type singular name', 'energieburcht' ),
			'menu_name'             => esc_html_x( 'Projecten', 'admin menu', 'energieburcht' ),
			'name_admin_bar'        => esc_html_x( 'Project', 'add new on admin bar', 'energieburcht' ),
			'add_new'               => esc_html__( 'Add New', 'energieburcht' ),
			'add_new_item'          => esc_html__( 'Add Project', 'energieburcht' ),
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
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'taxonomies'         => array( 'projecten-categorie' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_admin_bar'  => true,
			'hierarchical'       => false,
			'has_archive'        => true,
			'show_in_rest'       => true,
			'rest_base'          => 'projecten',
			'rewrite'            => array( 'slug' => 'projecten' ),
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			'menu_position'      => 25,
			'menu_icon'          => 'dashicons-portfolio',
		);

		register_post_type( 'projecten', $args );
	}

	/**
	 * Register the 'projecten-categorie' taxonomy for Projecten.
	 *
	 * @return void
	 */
	public function register_projecten_taxonomy(): void {
		$labels = array(
			'name'                       => esc_html_x( 'Categorieën', 'taxonomy general name', 'energieburcht' ),
			'singular_name'              => esc_html_x( 'Categorie', 'taxonomy singular name', 'energieburcht' ),
			'search_items'               => esc_html__( 'Zoek categorieën', 'energieburcht' ),
			'popular_items'              => esc_html__( 'Populaire categorieën', 'energieburcht' ),
			'all_items'                  => esc_html__( 'Alle categorieën', 'energieburcht' ),
			'parent_item'                => esc_html__( 'Bovenliggende categorie', 'energieburcht' ),
			'parent_item_colon'          => esc_html__( 'Bovenliggende categorie:', 'energieburcht' ),
			'edit_item'                  => esc_html__( 'Categorie bewerken', 'energieburcht' ),
			'update_item'                => esc_html__( 'Categorie bijwerken', 'energieburcht' ),
			'add_new_item'               => esc_html__( 'Nieuwe categorie toevoegen', 'energieburcht' ),
			'new_item_name'              => esc_html__( 'Naam nieuwe categorie', 'energieburcht' ),
			'separate_items_with_commas' => esc_html__( 'Categorieën scheiden met komma\'s', 'energieburcht' ),
			'add_or_remove_items'        => esc_html__( 'Categorieën toevoegen of verwijderen', 'energieburcht' ),
			'choose_from_most_used'      => esc_html__( 'Kies uit de meest gebruikte categorieën', 'energieburcht' ),
			'not_found'                  => esc_html__( 'Geen categorieën gevonden.', 'energieburcht' ),
			'menu_name'                  => esc_html__( 'Categorieën', 'energieburcht' ),
			'back_to_items'              => esc_html__( '&larr; Terug naar categorieën', 'energieburcht' ),
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
			'rest_base'         => 'projecten-categorie',
			'rewrite'           => array( 'slug' => 'projecten-categorie' ),
		);

		register_taxonomy( 'projecten-categorie', array( 'projecten' ), $args );
	}

	/**
	 * Register the 'kennisitems' (Knowledge Items) Custom Post Type.
	 *
	 * @return void
	 */
	public function register_kennisitems_post_type(): void {
		$labels = array(
			'name'                  => esc_html_x( 'Kennisitems', 'post type general name', 'energieburcht' ),
			'singular_name'         => esc_html_x( 'Kennisitem', 'post type singular name', 'energieburcht' ),
			'menu_name'             => esc_html_x( 'Kennisitems', 'admin menu', 'energieburcht' ),
			'name_admin_bar'        => esc_html_x( 'Kennisitem', 'add new on admin bar', 'energieburcht' ),
			'add_new'               => esc_html__( 'Add New', 'energieburcht' ),
			'add_new_item'          => esc_html__( 'Add Kennisitem', 'energieburcht' ),
			'new_item'              => esc_html__( 'New Kennisitem', 'energieburcht' ),
			'edit_item'             => esc_html__( 'Edit Kennisitem', 'energieburcht' ),
			'view_item'             => esc_html__( 'View Kennisitem', 'energieburcht' ),
			'all_items'             => esc_html__( 'All Kennisitems', 'energieburcht' ),
			'search_items'          => esc_html__( 'Search Kennisitems', 'energieburcht' ),
			'parent_item_colon'     => esc_html__( 'Parent Kennisitems:', 'energieburcht' ),
			'not_found'             => esc_html__( 'No kennisitems found.', 'energieburcht' ),
			'not_found_in_trash'    => esc_html__( 'No kennisitems found in Trash.', 'energieburcht' ),
			'featured_image'        => esc_html__( 'Kennisitem Image', 'energieburcht' ),
			'set_featured_image'    => esc_html__( 'Set kennisitem image', 'energieburcht' ),
			'remove_featured_image' => esc_html__( 'Remove kennisitem image', 'energieburcht' ),
			'use_featured_image'    => esc_html__( 'Use as kennisitem image', 'energieburcht' ),
			'archives'              => esc_html__( 'Kennisitem Archives', 'energieburcht' ),
			'insert_into_item'      => esc_html__( 'Insert into kennisitem', 'energieburcht' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this kennisitem', 'energieburcht' ),
			'items_list'            => esc_html__( 'Kennisitems list', 'energieburcht' ),
			'items_list_navigation' => esc_html__( 'Kennisitems list navigation', 'energieburcht' ),
			'filter_items_list'     => esc_html__( 'Filter kennisitems list', 'energieburcht' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => esc_html__( 'Knowledge items published by Energieburcht.', 'energieburcht' ),
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'taxonomies'         => array( 'kennisitems-categorie' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_admin_bar'  => true,
			'hierarchical'       => false,
			'has_archive'        => true,
			'show_in_rest'       => true,
			'rest_base'          => 'kennisitems',
			'rewrite'            => array( 'slug' => 'kennisitems' ),
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			'menu_position'      => 26,
			'menu_icon'          => 'dashicons-welcome-learn-more',
		);

		register_post_type( 'kennisitems', $args );
	}

	/**
	 * Register the 'kennisitems-categorie' taxonomy for Kennisitems.
	 *
	 * @return void
	 */
	public function register_kennisitems_taxonomy(): void {
		$labels = array(
			'name'                       => esc_html_x( 'Categorieën', 'taxonomy general name', 'energieburcht' ),
			'singular_name'              => esc_html_x( 'Categorie', 'taxonomy singular name', 'energieburcht' ),
			'search_items'               => esc_html__( 'Zoek categorieën', 'energieburcht' ),
			'popular_items'              => esc_html__( 'Populaire categorieën', 'energieburcht' ),
			'all_items'                  => esc_html__( 'Alle categorieën', 'energieburcht' ),
			'parent_item'                => esc_html__( 'Bovenliggende categorie', 'energieburcht' ),
			'parent_item_colon'          => esc_html__( 'Bovenliggende categorie:', 'energieburcht' ),
			'edit_item'                  => esc_html__( 'Categorie bewerken', 'energieburcht' ),
			'update_item'                => esc_html__( 'Categorie bijwerken', 'energieburcht' ),
			'add_new_item'               => esc_html__( 'Nieuwe categorie toevoegen', 'energieburcht' ),
			'new_item_name'              => esc_html__( 'Naam nieuwe categorie', 'energieburcht' ),
			'separate_items_with_commas' => esc_html__( 'Categorieën scheiden met komma\'s', 'energieburcht' ),
			'add_or_remove_items'        => esc_html__( 'Categorieën toevoegen of verwijderen', 'energieburcht' ),
			'choose_from_most_used'      => esc_html__( 'Kies uit de meest gebruikte categorieën', 'energieburcht' ),
			'not_found'                  => esc_html__( 'Geen categorieën gevonden.', 'energieburcht' ),
			'menu_name'                  => esc_html__( 'Categorieën', 'energieburcht' ),
			'back_to_items'              => esc_html__( '&larr; Terug naar categorieën', 'energieburcht' ),
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
			'rest_base'         => 'kennisitems-categorie',
			'rewrite'           => array( 'slug' => 'kennisitems-categorie' ),
		);

		register_taxonomy( 'kennisitems-categorie', array( 'kennisitems' ), $args );
	}

	// =========================================================================
	// Query modifications
	// =========================================================================

	/**
	 * Override posts_per_page for the Projecten archive based on the
	 * Customizer setting "Items Per Page".
	 *
	 * Only fires on the main front-end query for the projecten archive so
	 * admin list tables and secondary queries are never affected.
	 *
	 * @param WP_Query $query The current query object.
	 * @return void
	 */
	public function set_projecten_posts_per_page( WP_Query $query ): void {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( $query->is_post_type_archive( 'projecten' ) ) {
			$per_page = absint( get_theme_mod( 'energieburcht_cpt_projecten_posts_per_page', 9 ) );
			$query->set( 'posts_per_page', max( 1, $per_page ) );
		}
	}

	/**
	 * Handle AJAX requests from the Projecten category filter bar.
	 *
	 * Accepts a term_id (0 = all) and paged number, runs a WP_Query, and
	 * returns JSON with the rendered cards HTML, total pages, and found count.
	 * The response is consumed by assets/js/projecten-filter.js.
	 *
	 * @return void  Sends JSON and exits.
	 */
	public function ajax_projecten_filter(): void {
		check_ajax_referer( 'projecten_filter_nonce', 'nonce' );

		$term_id  = intval( wp_unslash( $_POST['term_id'] ?? 0 ) );
		$paged    = max( 1, intval( wp_unslash( $_POST['paged'] ?? 1 ) ) );
		$per_page = absint( get_theme_mod( 'energieburcht_cpt_projecten_posts_per_page', 9 ) );

		$args = array(
			'post_type'      => 'projecten',
			'posts_per_page' => $per_page,
			'paged'          => $paged,
			'post_status'    => 'publish',
		);

		if ( $term_id > 0 ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'projecten-categorie',
					'field'    => 'term_id',
					'terms'    => $term_id,
				),
			);
		}

		$query = new WP_Query( $args );

		ob_start();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				get_template_part( 'parts/projecten-item' );
			}
		}
		$items_html = ob_get_clean();

		wp_reset_postdata();

		wp_send_json_success( array(
			'items'     => $items_html,
			'max_pages' => (int) $query->max_num_pages,
			'found'     => (int) $query->found_posts,
		) );
	}
}
