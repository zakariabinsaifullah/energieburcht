<?php
/**
 * Custom Customizer Range Control
 *
 * Renders a range slider paired with a number input and a reset-to-default
 * button inside the WordPress Customizer.
 *
 * Note on styles: the CSS for this control is printed once by
 * Energieburcht_Customizer::print_control_styles() (hooked to
 * `customize_controls_print_styles`), so it is NOT duplicated here.
 *
 * @package Energieburcht
 * @since   1.0.0
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Energieburcht_Customize_Range_Control
 *
 * Paired range-slider + number-input with a reset-to-default button.
 * The slider and number input are kept in sync via vanilla JavaScript.
 */
class Energieburcht_Customize_Range_Control extends WP_Customize_Control {

	/**
	 * Control type identifier.
	 *
	 * Must be unique across all registered Customizer controls.
	 *
	 * @var string
	 */
	public $type = 'energieburcht-range';

	/**
	 * Render the control's HTML content.
	 *
	 * Outputs a range input, a linked number input, and a reset button.
	 * A small self-contained vanilla-JS snippet wires the three together;
	 * it is scoped to this specific control instance via a unique ID so
	 * multiple range controls on the same page do not interfere.
	 *
	 * @return void
	 */
	public function render_content(): void {

		// Resolve input attributes with safe fallbacks.
		$min     = isset( $this->input_attrs['min'] )  ? (int) $this->input_attrs['min']  : 0;
		$max     = isset( $this->input_attrs['max'] )  ? (int) $this->input_attrs['max']  : 100;
		$step    = isset( $this->input_attrs['step'] ) ? (int) $this->input_attrs['step'] : 1;
		$default = (int) $this->setting->default;
		$value   = (int) $this->value();

		// Build a unique DOM ID for this instance so the JS below is scoped.
		$range_id  = 'energieburcht-range-' . esc_attr( $this->id );
		$number_id = 'energieburcht-number-' . esc_attr( $this->id );
		$reset_id  = 'energieburcht-reset-' . esc_attr( $this->id );
		?>

		<div class="energieburcht-range-control">

			<?php if ( ! empty( $this->label ) ) : ?>
				<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">
					<label for="<?php echo esc_attr( $range_id ); ?>" class="customize-control-title">
						<?php echo esc_html( $this->label ); ?>
					</label>

					<button
						type="button"
						id="<?php echo esc_attr( $reset_id ); ?>"
						class="energieburcht-range-reset"
						title="<?php esc_attr_e( 'Reset to default', 'energieburcht' ); ?>"
					>
						<span class="dashicons dashicons-image-rotate"></span>
					</button>
				</div>
			<?php endif; ?>

			<div class="range-control-wrapper">
				<input
					type="range"
					id="<?php echo esc_attr( $range_id ); ?>"
					class="energieburcht-range-input"
					min="<?php echo esc_attr( $min ); ?>"
					max="<?php echo esc_attr( $max ); ?>"
					step="<?php echo esc_attr( $step ); ?>"
					value="<?php echo esc_attr( $value ); ?>"
					<?php $this->link(); ?>
				/>

				<input
					type="number"
					id="<?php echo esc_attr( $number_id ); ?>"
					class="energieburcht-range-number"
					min="<?php echo esc_attr( $min ); ?>"
					max="<?php echo esc_attr( $max ); ?>"
					step="<?php echo esc_attr( $step ); ?>"
					value="<?php echo esc_attr( $value ); ?>"
					aria-label="<?php echo esc_attr( $this->label ); ?>"
				/>
			</div>

			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description">
					<?php echo esc_html( $this->description ); ?>
				</span>
			<?php endif; ?>

		</div>

		<script>
		( function() {
			var rangeEl  = document.getElementById( '<?php echo esc_js( $range_id ); ?>' );
			var numberEl = document.getElementById( '<?php echo esc_js( $number_id ); ?>' );
			var resetEl  = document.getElementById( '<?php echo esc_js( $reset_id ); ?>' );

			if ( ! rangeEl || ! numberEl || ! resetEl ) {
				return;
			}

			// Keep the number input in sync when the range slider moves.
			rangeEl.addEventListener( 'input', function() {
				numberEl.value = rangeEl.value;
			} );

			// Keep the range slider in sync when the number input changes,
			// and fire a 'change' event so the Customizer registers the update.
			numberEl.addEventListener( 'input', function() {
				rangeEl.value = numberEl.value;
				rangeEl.dispatchEvent( new Event( 'change' ) );
			} );

			// Reset both inputs to the setting's default value.
			resetEl.addEventListener( 'click', function() {
				var defaultVal = '<?php echo esc_js( (string) $default ); ?>';
				rangeEl.value  = defaultVal;
				numberEl.value = defaultVal;
				rangeEl.dispatchEvent( new Event( 'input' ) );
				rangeEl.dispatchEvent( new Event( 'change' ) );
			} );
		}() );
		</script>
		<?php
	}
}
