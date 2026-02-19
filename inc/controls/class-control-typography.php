<?php
/**
 * Custom Customizer Typography Control
 *
 * Renders a set of options for typography:
 * 1. A set of radio buttons for Presets (Small, Medium, Large, etc.)
 * 2. A "Custom" option that reveals a text input for manual values.
 * 3. A "Reset" button to restore the default.
 *
 * @package Energieburcht
 * @since   1.0.0
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Energieburcht_Customize_Typography_Control
 */
class Energieburcht_Customize_Typography_Control extends WP_Customize_Control {

	/**
	 * Control type identifier.
	 *
	 * @var string
	 */
	public $type = 'energieburcht-typography';

	/**
	 * Render the control's HTML content.
	 *
	 * @return void
	 */
	public function render_content(): void {

		// Retrieve all presets from theme.json logic or passed in via input_attrs.
		// We expect input_attrs['presets'] to be an array of [ 'name' => 'Small', 'value' => '0.875rem' ] etc.
		$presets = isset( $this->input_attrs['presets'] ) ? $this->input_attrs['presets'] : array();
		
		// Current value
		$current_value = $this->value();
		
		// Default value
		$default_value = isset( $this->setting->default ) ? $this->setting->default : '';

		// Unique ID for scoping
		$control_id = 'energieburcht-typography-' . $this->id;
		$reset_id   = 'energieburcht-typography-reset-' . $this->id;
		$custom_id  = 'energieburcht-typography-custom-' . $this->id;
		
		// Determine if current value matches a preset
		$is_custom = true;
		foreach ( $presets as $preset ) {
			if ( $current_value === $preset['value'] ) {
				$is_custom = false;
				break;
			}
		}

		?>
		<div class="energieburcht-typography-control" id="<?php echo esc_attr( $control_id ); ?>">
			
			<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif; ?>

				<button type="button" id="<?php echo esc_attr( $reset_id ); ?>" class="energieburcht-reset-btn" title="<?php esc_attr_e( 'Reset to default', 'energieburcht' ); ?>">
					<span class="dashicons dashicons-image-rotate"></span>
				</button>
			</div>

			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description" style="margin-bottom: 10px; display:block;">
					<?php echo esc_html( $this->description ); ?>
				</span>
			<?php endif; ?>

			<div class="typography-presets" style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 15px;">
				<?php foreach ( $presets as $preset ) : ?>
					<?php 
					$checked = ( $current_value === $preset['value'] ) ? 'checked' : ''; 
					$preset_id = $control_id . '-' . sanitize_key( $preset['name'] );
					?>
					<div class="typography-option">
						<input 
							type="radio" 
							name="<?php echo esc_attr( $this->id ); ?>_mode" 
							id="<?php echo esc_attr( $preset_id ); ?>" 
							value="<?php echo esc_attr( $preset['value'] ); ?>"
							class="typography-radio-input"
							<?php echo $checked; ?>
							data-is-custom="false"
						>
						<label for="<?php echo esc_attr( $preset_id ); ?>" class="typography-radio-label">
							<?php echo esc_html( $preset['name'] ); ?>
						</label>
					</div>
				<?php endforeach; ?>

				<!-- Custom Option -->
				<div class="typography-option">
					<input 
						type="radio" 
						name="<?php echo esc_attr( $this->id ); ?>_mode" 
						id="<?php echo esc_attr( $custom_id . '_radio' ); ?>" 
						value="custom" 
						class="typography-radio-input"
						<?php echo $is_custom ? 'checked' : ''; ?>
						data-is-custom="true"
					>
					<label for="<?php echo esc_attr( $custom_id . '_radio' ); ?>" class="typography-radio-label">
						<?php esc_html_e( 'Custom', 'energieburcht' ); ?>
					</label>
				</div>
			</div>

			<!-- Custom Input Area -->
			<div class="typography-custom-input-wrapper" style="display: <?php echo $is_custom ? 'block' : 'none'; ?>; margin-top: 10px;">
				<label for="<?php echo esc_attr( $custom_id ); ?>" style="display:block; margin-bottom:5px; font-size:12px;">
					<?php esc_html_e( 'Enter value (e.g. clamp(...) or 1.5rem)', 'energieburcht' ); ?>
				</label>
				<input 
					type="text" 
					id="<?php echo esc_attr( $custom_id ); ?>" 
					class="typography-text-input" 
					value="<?php echo esc_attr( $current_value ); ?>" 
					placeholder="clamp(1rem, 2vw, 1.5rem)"
				>
				<p class="description" style="margin-top:5px; font-size:11px;">
					<?php esc_html_e( 'Example: clamp(1.125rem, 2vw, 1.25rem)', 'energieburcht' ); ?>
				</p>
			</div>
			
			<!-- Hidden input to store the actual setting value that Customizer saves -->
			<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( $current_value ); ?>" class="typography-value-storage">

		</div>

		<?php
		// Inline styles for this control structure
		?>
		<style>
			.typography-presets {
				display: flex;
				flex-wrap: wrap;
				gap: 8px;
			}
			.typography-option {
				display: flex;
                position: relative;
			}
			.typography-radio-input {
				position: absolute;
				left: 0;
				top: 0;
				width: 100%;
				height: 100%;
				opacity: 0;
				cursor: pointer;
                z-index: -1;
			}
			.typography-radio-label {
				display: flex;
				align-items: center;
				justify-content: center;
				padding: 6px 12px;
				border: 1px solid #dcdcde;
				border-radius: 4px;
				background: #f6f7f7;
				font-size: 13px;
				font-weight: 500;
				color: #50575e;
				cursor: pointer;
				transition: all 0.2s ease-in-out;
				min-height: auto !important;
				text-align: center;
			}
			.typography-radio-label:hover {
				background: #fff;
				border-color: #2271b1;
				color: #2271b1;
			}
			.typography-radio-input:checked + .typography-radio-label {
				background: #2271b1;
				color: #fff;
				border-color: #2271b1;
				box-shadow: 0 2px 4px rgba(0,0,0,0.1);
			}
			.typography-text-input {
				width: 100%;
				padding: 8px;
				border: 1px solid #8c8f94;
				border-radius: 4px;
			}
			.energieburcht-reset-btn {
				background: none;
				border: none;
				cursor: pointer;
				color: #a7aaad;
				transition: color 0.2s;
			}
			.energieburcht-reset-btn:hover {
				color: #2271b1;
			}
		</style>

		<script>
		( function() {
			var container = document.getElementById( '<?php echo esc_js( $control_id ); ?>' );
			if ( ! container ) return;

			var radios      = container.querySelectorAll( '.typography-radio-input' );
			var customWrap  = container.querySelector( '.typography-custom-input-wrapper' );
			var customInput = container.querySelector( '.typography-text-input' );
			var storage     = container.querySelector( '.typography-value-storage' );
			var resetBtn    = container.querySelector( '.energieburcht-reset-btn' );
			var defaultVal  = '<?php echo esc_js( $default_value ); ?>';

			function updateStorage( val ) {
				storage.value = val;
				storage.dispatchEvent( new Event( 'input' ) );
				storage.dispatchEvent( new Event( 'change' ) ); // Trigger Customizer save
			}

			// Radio change handler
			radios.forEach( function( radio ) {
				radio.addEventListener( 'change', function() {
					if ( this.dataset.isCustom === 'true' ) {
						customWrap.style.display = 'block';
						// If switching to custom, update storage with whatever is in the text input
						updateStorage( customInput.value );
					} else {
						customWrap.style.display = 'none';
						updateStorage( this.value );
						// Also sync text input just in case user switches back to custom later
						customInput.value = this.value;
					}
				} );
			} );

			// Custom input handler
			customInput.addEventListener( 'input', function() {
				// Only update if custom radio is checked
				var active = container.querySelector( '.typography-radio-input:checked' );
				if ( active && active.dataset.isCustom === 'true' ) {
					updateStorage( this.value );
				}
			} );

			// Reset handler
			if ( resetBtn ) {
				resetBtn.addEventListener( 'click', function() {
					// 1. Find if default value matches a preset
					var matchingRadio = null;
					var customRadio   = null;

					radios.forEach( function( r ) {
						if ( r.value === defaultVal && r.dataset.isCustom !== 'true' ) {
							matchingRadio = r;
						}
						if ( r.dataset.isCustom === 'true' ) {
							customRadio = r;
						}
					} );

					if ( matchingRadio ) {
						matchingRadio.checked = true;
						customWrap.style.display = 'none';
					} else {
						// Default is likely a custom value? Or just default to custom mode
						if ( customRadio ) customRadio.checked = true;
						customWrap.style.display = 'block';
					}

					customInput.value = defaultVal;
					updateStorage( defaultVal );
				} );
			}

		} )();
		</script>
		<?php
	}
}
