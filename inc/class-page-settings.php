<?php
/**
 * Page Settings Meta Box
 *
 * Registers a meta box for pages to control page-specific settings,
 * such as hiding the page title.
 *
 * @package Energieburcht
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Energieburcht_Page_Settings
 */
class Energieburcht_Page_Settings {

	/**
	 * Meta key for hiding the page title.
	 */
	const META_KEY_HIDE_TITLE = '_energieburcht_title_visibility';
	const META_KEY_HERO_VISIBILITY = '_energieburcht_hero_visibility';
	const META_KEY_HERO_CTA_TEXT = '_energieburcht_hero_cta_text';
	const META_KEY_HERO_CTA_LINK = '_energieburcht_hero_cta_link';

	/**
	 * Single shared instance of this class.
	 *
	 * @var Energieburcht_Page_Settings|null
	 */
	private static $instance = null;

	/**
	 * Private constructor — obtain the instance via get_instance().
	 */
	private function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post',      array( $this, 'save_meta_box' ) );
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

	/**
	 * Add the meta box.
	 */
	public function add_meta_box() {
		add_meta_box(
			'energieburcht_page_settings',
			__( 'Page Settings', 'energieburcht' ),
			array( $this, 'render_meta_box' ),
			'page',
			'normal',
			'default'
		);
	}

	/**
	 * Render the meta box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box( $post ) {
		// Add nonce for verification.
		wp_nonce_field( 'energieburcht_page_settings_nonce', 'energieburcht_page_settings_nonce' );

		// Retrieve existing value from the database.
		$visibility = get_post_meta( $post->ID, self::META_KEY_HIDE_TITLE, true );
		if ( empty( $visibility ) ) {
			$visibility = 'default';
		}
		?>
			<div style="margin-bottom: 10px;"><strong><?php esc_html_e( 'Title Visibility', 'energieburcht' ); ?></strong></div>
			<p>
				<label>
					<input type="radio" name="energieburcht_title_visibility" value="default" <?php checked( $visibility, 'default' ); ?> />
					<?php esc_html_e( 'Default', 'energieburcht' ); ?>
				</label><br>
				<label>
					<input type="radio" name="energieburcht_title_visibility" value="show" <?php checked( $visibility, 'show' ); ?> />
					<?php esc_html_e( 'Show', 'energieburcht' ); ?>
				</label><br>
				<label>
					<input type="radio" name="energieburcht_title_visibility" value="hide" <?php checked( $visibility, 'hide' ); ?> />
					<?php esc_html_e( 'Hide', 'energieburcht' ); ?>
				</label>
			</p>

			<hr>

			<div style="margin-bottom: 10px;"><strong><?php esc_html_e( 'Hero Area', 'energieburcht' ); ?></strong></div>
			<?php
			$hero_vis = get_post_meta( $post->ID, self::META_KEY_HERO_VISIBILITY, true );
			if ( empty( $hero_vis ) ) {
				$hero_vis = 'default';
			}
			$cta_text = get_post_meta( $post->ID, self::META_KEY_HERO_CTA_TEXT, true );
			$cta_link = get_post_meta( $post->ID, self::META_KEY_HERO_CTA_LINK, true );
			?>
			<p>
				<label><?php esc_html_e( 'Hero Visibility', 'energieburcht' ); ?></label><br>
				<label>
					<input type="radio" name="energieburcht_hero_visibility" value="default" <?php checked( $hero_vis, 'default' ); ?> />
					<?php esc_html_e( 'Default', 'energieburcht' ); ?>
				</label><br>
				<label>
					<input type="radio" name="energieburcht_hero_visibility" value="show" <?php checked( $hero_vis, 'show' ); ?> />
					<?php esc_html_e( 'Show', 'energieburcht' ); ?>
				</label><br>
				<label>
					<input type="radio" name="energieburcht_hero_visibility" value="hide" <?php checked( $hero_vis, 'hide' ); ?> />
					<?php esc_html_e( 'Hide', 'energieburcht' ); ?>
				</label>
			</p>
			<p>
				<label for="energieburcht_hero_cta_text"><?php esc_html_e( 'CTA Text', 'energieburcht' ); ?></label><br>
				<input type="text" name="energieburcht_hero_cta_text" id="energieburcht_hero_cta_text" value="<?php echo esc_attr( $cta_text ); ?>" style="width:100%;" />
			</p>
			<p>
				<label for="energieburcht_hero_cta_link"><?php esc_html_e( 'CTA Link', 'energieburcht' ); ?></label><br>
				<input type="text" name="energieburcht_hero_cta_link" id="energieburcht_hero_cta_link" value="<?php echo esc_attr( $cta_link ); ?>" style="width:100%;" />
			</p>
		<?php
	}

	/**
	 * Save the meta box selection.
	 *
	 * @param int $post_id The post ID.
	 */
	public function save_meta_box( $post_id ) {
		// Check if our nonce is set.
		if ( ! isset( $_POST['energieburcht_page_settings_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['energieburcht_page_settings_nonce'], 'energieburcht_page_settings_nonce' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		/* OK, it's safe for us to save the data now. */

		// Sanitize user input.
		$new_val = isset( $_POST['energieburcht_title_visibility'] ) ? sanitize_key( $_POST['energieburcht_title_visibility'] ) : 'default';
		$hero_vis = isset( $_POST['energieburcht_hero_visibility'] ) ? sanitize_key( $_POST['energieburcht_hero_visibility'] ) : 'default';
		$cta_text = isset( $_POST['energieburcht_hero_cta_text'] ) ? sanitize_text_field( $_POST['energieburcht_hero_cta_text'] ) : '';
		$cta_link = isset( $_POST['energieburcht_hero_cta_link'] ) ? esc_url_raw( $_POST['energieburcht_hero_cta_link'] ) : '';

		// Update the meta field in the database.
		update_post_meta( $post_id, self::META_KEY_HIDE_TITLE, $new_val );
		update_post_meta( $post_id, self::META_KEY_HERO_VISIBILITY, $hero_vis );
		update_post_meta( $post_id, self::META_KEY_HERO_CTA_TEXT, $cta_text );
		update_post_meta( $post_id, self::META_KEY_HERO_CTA_LINK, $cta_link );
	}
}

