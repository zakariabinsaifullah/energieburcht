<?php
/**
 * Responsive Range Control
 *
 * @package Energieburcht
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Energieburcht_Customize_Responsive_Range_Control
 */
class Energieburcht_Customize_Responsive_Range_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'energieburcht-responsive-range';

	/**
	 * Enqueue control related scripts/styles.
	 */
	public function enqueue() {
		wp_enqueue_script(
			'energieburcht-customizer-control-js',
			get_template_directory_uri() . '/assets/js/customizer-control.js',
			array( 'jquery', 'customize-controls' ),
			'1.0.0',
			true
		);

		wp_enqueue_style(
			'energieburcht-customizer-control-css',
			get_template_directory_uri() . '/assets/css/customizer-control.css',
			array(),
			'1.0.0'
		);
	}

	/**
	 * Render the control's content.
	 */
	public function render_content() {
		$defaultValue = array(
			'desktop' => '',
			'tablet'  => '',
			'mobile'  => '',
			'unit'    => 'px',
		);

		$value = $this->value();

		if ( is_string( $value ) ) {
			$decoded = json_decode( $value, true );
			if ( is_array( $decoded ) ) {
				$value = array_merge( $defaultValue, $decoded );
			} else {
				// If it's a simple string (old value), assign it to desktop and keep defaults
				$value = array_merge( $defaultValue, array( 'desktop' => $value ) );
			}
		} else {
			$value = $defaultValue;
		}

		// Defaults options
		$defaults = array(
			'min'   => 0,
			'max'   => 100,
			'step'  => 1,
			'units' => array( 'px', 'em', '%', 'rem', 'vw', 'vh' ),
		);

		$attrs = wp_parse_args( $this->input_attrs, $defaults );

		// Unique ID for JS scoping
		$control_id = 'energieburcht-responsive-range-' . $this->id;
		?>

		<div class="energieburcht-responsive-range-control" id="<?php echo esc_attr( $control_id ); ?>">
			
			<div class="energieburcht-control-header">
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif; ?>

				<div class="energieburcht-responsive-toggles">
					<button type="button" class="responsive-toggle active" data-device="desktop" title="<?php esc_attr_e( 'Desktop', 'energieburcht' ); ?>">
						<span class="dashicons dashicons-desktop"></span>
					</button>
					<button type="button" class="responsive-toggle" data-device="tablet" title="<?php esc_attr_e( 'Tablet', 'energieburcht' ); ?>">
						<span class="dashicons dashicons-tablet"></span>
					</button>
					<button type="button" class="responsive-toggle" data-device="mobile" title="<?php esc_attr_e( 'Mobile', 'energieburcht' ); ?>">
						<span class="dashicons dashicons-smartphone"></span>
					</button>
				</div>
			</div>

			<div class="energieburcht-control-body">
				<?php foreach ( array( 'desktop', 'tablet', 'mobile' ) as $device ) : ?>
					<div class="responsive-field <?php echo $device === 'desktop' ? 'active' : ''; ?>" data-device="<?php echo esc_attr( $device ); ?>">
						<div class="range-input-wrapper">
							<input 
								type="range" 
								class="energieburcht-range-slider" 
								min="<?php echo esc_attr( $attrs['min'] ); ?>" 
								max="<?php echo esc_attr( $attrs['max'] ); ?>" 
								step="<?php echo esc_attr( $attrs['step'] ); ?>" 
								value="<?php echo esc_attr( $value[ $device ] ); ?>"
								data-device="<?php echo esc_attr( $device ); ?>"
							>
							<div class="input-with-unit">
								<input 
									type="number" 
									class="energieburcht-range-number" 
									min="<?php echo esc_attr( $attrs['min'] ); ?>" 
									max="<?php echo esc_attr( $attrs['max'] ); ?>" 
									step="<?php echo esc_attr( $attrs['step'] ); ?>" 
									value="<?php echo esc_attr( $value[ $device ] ); ?>"
									data-device="<?php echo esc_attr( $device ); ?>"
								>
								<?php if ( ! empty( $attrs['units'] ) ) : ?>
									<span class="unit-display"><?php echo esc_html( $value['unit'] ); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
				
				<div class="energieburcht-control-actions">
					<?php if ( count( $attrs['units'] ) > 1 ) : ?>
						<div class="unit-selector">
							<?php foreach ( $attrs['units'] as $unit ) : ?>
								<button 
									type="button" 
									class="unit-toggle <?php echo $value['unit'] === $unit ? 'active' : ''; ?>" 
									data-unit="<?php echo esc_attr( $unit ); ?>"
								>
									<?php echo esc_html( $unit ); ?>
								</button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<button type="button" class="reset-button" title="<?php esc_attr_e( 'Reset', 'energieburcht' ); ?>">
						<span class="dashicons dashicons-image-rotate"></span>
					</button>
				</div>
			</div>

			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>

			<!-- Hidden input to store JSON value -->
			<input type="hidden" class="energieburcht-responsive-value" <?php $this->link(); ?> value="<?php echo esc_attr( json_encode( $value ) ); ?>">
			
		</div>
		<?php
	}
}
