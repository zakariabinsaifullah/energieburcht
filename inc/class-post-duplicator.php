<?php
/**
 * Post Duplication Engine
 *
 * Appends a "Duplicate" row action to the admin list view for Posts, Pages,
 * and every registered Custom Post Type. The duplicate is always created as a
 * draft so it cannot accidentally go live, and the editor is redirected to the
 * new post's edit screen with a contextual success notice.
 *
 * Security:
 *  - check_admin_referer() guards the action against CSRF.
 *  - current_user_can( 'edit_post', $id ) enforces capability per-post.
 *  - All output is escaped; all input is sanitized before use.
 *
 * Meta copying:
 *  - Uses add_post_meta() (not update_post_meta()) so that repeatable meta
 *    keys — common in ACF repeaters — are preserved as multiple rows.
 *
 * Taxonomy copying:
 *  - Iterates every taxonomy registered for the post type so that custom
 *    taxonomies are included alongside categories and tags.
 *
 * @package Energieburcht
 * @since   1.0.0
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Energieburcht_Post_Duplicator
 */
final class Energieburcht_Post_Duplicator {

	/**
	 * Single shared instance of this class.
	 *
	 * @var Energieburcht_Post_Duplicator|null
	 */
	private static $instance = null;

	/**
	 * Post meta keys that must not be carried over to the duplicate.
	 *
	 * _edit_lock  — stores a timestamp + user ID for "someone is editing" UI.
	 * _edit_last  — stores the user ID of the last editor.
	 *
	 * @var string[]
	 */
	private static $skip_meta_keys = array( '_edit_lock', '_edit_last' );

	// =========================================================================
	// Singleton boilerplate
	// =========================================================================

	/**
	 * Private constructor — obtain the instance via get_instance().
	 */
	private function __construct() {
		// Add the "Duplicate" link to post/CPT list rows.
		add_filter( 'post_row_actions', array( $this, 'add_duplicate_link' ), 10, 2 );

		// Add the "Duplicate" link to page list rows (separate filter in WP core).
		add_filter( 'page_row_actions', array( $this, 'add_duplicate_link' ), 10, 2 );

		// Handle the duplication request when the admin action fires.
		add_action( 'admin_action_eb_duplicate_post', array( $this, 'handle_duplication' ) );

		// Render the success notice on the new draft's edit screen.
		add_action( 'admin_notices', array( $this, 'show_duplicate_notice' ) );
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
	// Row action
	// =========================================================================

	/**
	 * Append a nonce-signed "Duplicate" action link to the row actions array.
	 *
	 * Fires on both `post_row_actions` (posts + CPTs) and `page_row_actions`
	 * (pages), so all public post types are covered with a single callback.
	 *
	 * @param  array    $actions Existing row action links.
	 * @param  \WP_Post $post    The post object for the current row.
	 * @return array
	 */
	public function add_duplicate_link( array $actions, \WP_Post $post ): array {
		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}

		$url = wp_nonce_url(
			add_query_arg(
				array(
					'action'  => 'eb_duplicate_post',
					'post_id' => $post->ID,
				),
				admin_url( 'admin.php' )
			),
			'eb_duplicate_post_' . $post->ID
		);

		$actions['eb_duplicate'] = sprintf(
			'<a href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( $url ),
			/* translators: %s: Post title. */
			esc_attr( sprintf( __( 'Duplicate &#8220;%s&#8221;', 'energieburcht' ), get_the_title( $post ) ) ),
			esc_html__( 'Duplicate', 'energieburcht' )
		);

		return $actions;
	}

	// =========================================================================
	// Duplication handler
	// =========================================================================

	/**
	 * Execute the duplication when the `admin_action_eb_duplicate_post` hook fires.
	 *
	 * Flow:
	 *  1. Validate nonce (CSRF guard).
	 *  2. Verify the current user can edit the original post.
	 *  3. Insert a new post (status = draft) cloned from the original.
	 *  4. Copy all post meta (including ACF fields).
	 *  5. Copy all taxonomy terms.
	 *  6. Redirect to the new draft's edit screen.
	 *
	 * @return void
	 */
	public function handle_duplication(): void {
		$post_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0;

		if ( ! $post_id ) {
			wp_die( esc_html__( 'Invalid post ID supplied for duplication.', 'energieburcht' ) );
		}

		// ── CSRF guard ──────────────────────────────────────────────────────
		check_admin_referer( 'eb_duplicate_post_' . $post_id );

		// ── Capability check ─────────────────────────────────────────────────
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( esc_html__( 'You do not have permission to duplicate this post.', 'energieburcht' ) );
		}

		// ── Fetch the original ───────────────────────────────────────────────
		$original = get_post( $post_id );

		if ( ! $original instanceof \WP_Post ) {
			wp_die( esc_html__( 'The original post could not be found.', 'energieburcht' ) );
		}

		// ── Insert the duplicate ─────────────────────────────────────────────
		// wp_insert_post() expects slashed data (mirrors the behaviour of the
		// post editor form submission).
		$new_post_args = wp_slash(
			array(
				'post_title'     => $original->post_title,
				'post_content'   => $original->post_content,
				'post_excerpt'   => $original->post_excerpt,
				'post_status'    => 'draft',
				'post_type'      => $original->post_type,
				'post_author'    => get_current_user_id(),
				'post_parent'    => $original->post_parent,
				'menu_order'     => $original->menu_order,
				'post_password'  => $original->post_password,
				'comment_status' => $original->comment_status,
				'ping_status'    => $original->ping_status,
				'to_ping'        => $original->to_ping,
			)
		);

		$new_id = wp_insert_post( $new_post_args, true );

		if ( is_wp_error( $new_id ) ) {
			wp_die(
				sprintf(
					/* translators: %s: WP_Error message. */
					esc_html__( 'Duplication failed: %s', 'energieburcht' ),
					esc_html( $new_id->get_error_message() )
				)
			);
		}

		// ── Copy meta ────────────────────────────────────────────────────────
		$this->copy_post_meta( $post_id, $new_id );

		// ── Copy taxonomy terms ──────────────────────────────────────────────
		$this->copy_taxonomies( $original, $new_id );

		// ── Redirect to the new draft's edit screen ──────────────────────────
		wp_safe_redirect(
			add_query_arg(
				array(
					'action'        => 'edit',
					'post'          => $new_id,
					'eb_duplicated' => '1',
				),
				admin_url( 'post.php' )
			)
		);
		exit;
	}

	// =========================================================================
	// Private helpers
	// =========================================================================

	/**
	 * Copy every post meta entry from the original post to the new one.
	 *
	 * Key design decisions:
	 *  - add_post_meta() is used (not update_post_meta()) so that multiple rows
	 *    sharing the same key — as created by ACF repeater fields — are all
	 *    preserved rather than being collapsed into a single value.
	 *  - maybe_unserialize() is applied to each raw DB value before passing it
	 *    to add_post_meta(), which then re-serializes arrays/objects correctly.
	 *  - Internal WordPress meta keys that would cause conflicts are skipped.
	 *
	 * @param  int $original_id Source post ID.
	 * @param  int $new_id      Destination post ID.
	 * @return void
	 */
	private function copy_post_meta( int $original_id, int $new_id ): void {
		// get_post_meta() with no key and no single flag returns a map of
		// meta_key => [ raw_db_value, ... ] (always an array of arrays).
		$all_meta = get_post_meta( $original_id );

		if ( empty( $all_meta ) ) {
			return;
		}

		foreach ( $all_meta as $meta_key => $meta_values ) {
			if ( in_array( $meta_key, self::$skip_meta_keys, true ) ) {
				continue;
			}

			foreach ( $meta_values as $raw_value ) {
				// The raw DB string may be a serialized PHP value. Unserializing
				// lets add_post_meta() re-serialize it deterministically.
				add_post_meta( $new_id, $meta_key, maybe_unserialize( $raw_value ) );
			}
		}
	}

	/**
	 * Assign every taxonomy term from the original post to the new post.
	 *
	 * Iterates all taxonomies registered for the post type — not just the
	 * built-in category/tag pair — so custom taxonomies are included.
	 *
	 * @param  \WP_Post $original Source post object.
	 * @param  int      $new_id   Destination post ID.
	 * @return void
	 */
	private function copy_taxonomies( \WP_Post $original, int $new_id ): void {
		$taxonomies = get_object_taxonomies( $original->post_type );

		foreach ( $taxonomies as $taxonomy ) {
			$term_ids = wp_get_object_terms( $original->ID, $taxonomy, array( 'fields' => 'ids' ) );

			if ( is_wp_error( $term_ids ) || empty( $term_ids ) ) {
				continue;
			}

			wp_set_object_terms( $new_id, $term_ids, $taxonomy );
		}
	}

	// =========================================================================
	// Admin notice
	// =========================================================================

	/**
	 * Display a dismissible success notice on the edit screen of the new draft.
	 *
	 * Only shown when the `eb_duplicated=1` query parameter is present, which
	 * is appended to the redirect URL in handle_duplication().
	 *
	 * @return void
	 */
	public function show_duplicate_notice(): void {
		$screen = get_current_screen();

		// Only show on the post edit screen.
		if ( ! $screen || 'post' !== $screen->base ) {
			return;
		}

		if ( empty( $_GET['eb_duplicated'] ) || '1' !== sanitize_key( $_GET['eb_duplicated'] ) ) {
			return;
		}

		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html__( 'Post duplicated successfully. You are now editing the new draft.', 'energieburcht' )
		);
	}
}
