<?php
/**
 * Alpha Color Control
 *
 * @package Energieburcht
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Energieburcht_Customize_Alpha_Color_Control
 *
 * Extends the default color control to add alpha channel support.
 */
class Energieburcht_Customize_Alpha_Color_Control extends WP_Customize_Color_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'energieburcht-alpha-color';

	/**
	 * Enqueue control related scripts/styles.
	 */
	public function enqueue() {
		wp_enqueue_script(
			'energieburcht-wp-color-picker-alpha',
			get_template_directory_uri() . '/assets/js/wp-color-picker-alpha.js',
			array( 'jquery', 'wp-color-picker' ),
			'3.0.4',
			true
		);

		wp_enqueue_script(
			'energieburcht-customizer-alpha-color',
			get_template_directory_uri() . '/assets/js/customizer-alpha-color.js',
			array( 'jquery', 'wp-color-picker', 'energieburcht-wp-color-picker-alpha' ),
			'1.0.0',
			true
		);
		wp_enqueue_style( 'wp-color-picker' );
	}

	/**
	 * Render the control's content.
	 *
	 * We simply add a data attribute to the input so our JS knows to enable alpha.
	 */
	public function render_content() {
		// Process the palette
		$palette = null;
		if ( isset( $this->input_attrs['palette'] ) ) {
			$palette = $this->input_attrs['palette'];
		}
		
		// If palette is an array, implode it
		if ( is_array( $palette ) ) {
			$palette = implode( '|', $palette );
		}
		?>
		<?php if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif; ?>
		<?php if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif; ?>
		<div class="customize-control-content">
			<input 
				class="alpha-color-control-input" 
				type="text" 
				data-palette="<?php echo esc_attr( $palette ); ?>" 
				data-alpha-enabled="true" 
				value="<?php echo esc_attr( $this->value() ); ?>" 
				<?php $this->link(); ?> 
			/>
		</div>
		<?php
	}
}
