<?php
/**
 * Custom Customizer Editor Control
 *
 * A plain <textarea> control for the WordPress Customizer. Unlike the default
 * textarea control, this one renders the description below the input using
 * wp_kses_post() so that HTML (e.g. <code> tags) is preserved safely.
 *
 * @package Energieburcht
 * @since   1.0.0
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Energieburcht_Customize_Editor_Control
 *
 * Extends WP_Customize_Control to provide a textarea input with an
 * HTML-aware description field.
 */
class Energieburcht_Customize_Editor_Control extends WP_Customize_Control {

	/**
	 * Control type identifier.
	 *
	 * Must be unique across all registered Customizer controls.
	 *
	 * @var string
	 */
	public $type = 'energieburcht-editor';

	/**
	 * Render the control's HTML content.
	 *
	 * Outputs a labelled <textarea> followed by an optional description.
	 * The description is passed through wp_kses_post() so that authors can
	 * use safe HTML (e.g. <code>, <a>) in the Customizer UI.
	 *
	 * @return void
	 */
	public function render_content(): void {
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title">
					<?php echo esc_html( $this->label ); ?>
				</span>
			<?php endif; ?>

			<div class="customize-control-notifications-container"></div>

			<textarea
				class="large-text code"
				rows="5"
				<?php $this->link(); ?>
			><?php echo esc_textarea( $this->value() ); ?></textarea>

			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description">
					<?php
					// wp_kses_post() allows safe HTML (e.g. <code>, <strong>) in the
					// description while stripping any dangerous tags or attributes.
					echo wp_kses_post( $this->description );
					?>
				</span>
			<?php endif; ?>
		</label>
		<?php
	}
}
