<?php
/**
 * Palette Color Control
 *
 * A custom Customizer control that presents the theme colour palette as
 * clickable swatches. Selecting a swatch stores the corresponding CSS
 * variable reference (e.g. `var(--eb-navy)`) so the element colour stays
 * automatically linked to the palette. A "Custom" button exposes a standard
 * WordPress colour picker for arbitrary hex values.
 *
 * @package Energieburcht
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Energieburcht_Customize_Palette_Color_Control
 */
class Energieburcht_Customize_Palette_Color_Control extends WP_Customize_Control {

	/**
	 * Control type identifier — matched in customizer-control.js.
	 *
	 * @var string
	 */
	public $type = 'energieburcht-palette-color';

	// =========================================================================
	// Asset enqueueing
	// =========================================================================

	/**
	 * Enqueue the scripts and styles required by this control.
	 *
	 * @return void
	 */
	public function enqueue(): void {
		// WordPress built-in colour picker (Iris).
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		// Shared control script and stylesheet.
		wp_enqueue_script(
			'energieburcht-customizer-control-js',
			get_template_directory_uri() . '/assets/js/customizer-control.js',
			array( 'jquery', 'wp-color-picker', 'customize-controls' ),
			'1.0.0',
			true
		);

		wp_enqueue_style(
			'energieburcht-customizer-control-css',
			get_template_directory_uri() . '/assets/css/customizer-control.css',
			array( 'wp-color-picker' ),
			'1.0.0'
		);
	}

	// =========================================================================
	// Rendering
	// =========================================================================

	/**
	 * Render the control HTML.
	 *
	 * Outputs:
	 *  - A row of palette colour swatches (each stores a var(--eb-*) reference).
	 *  - A "Custom" button that expands a wp-color-picker for arbitrary hex input.
	 *  - A hidden <input> linked to the WP Customizer setting.
	 *
	 * @return void
	 */
	public function render_content(): void {
		$current = $this->value();
		$palette  = Energieburcht_Customizer::get_color_palette();

		// Determine whether the saved value is a known palette var reference.
		$is_palette_var = (bool) preg_match( '/^var\(--eb-[a-z-]+\)$/', $current );
		$custom_value   = $is_palette_var ? '' : $current;
		?>

		<div class="eb-palette-color-control">

			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>

			<!-- Palette swatches -->
			<div class="eb-palette-swatches" role="radiogroup" aria-label="<?php esc_attr_e( 'Select palette colour', 'energieburcht' ); ?>">

				<?php foreach ( $palette as $color ) :
					$var_val  = 'var(' . $color['css_var'] . ')';
					$selected = $current === $var_val;
					?>
					<button
						type="button"
						class="eb-palette-swatch<?php echo $selected ? ' is-selected' : ''; ?>"
						data-var="<?php echo esc_attr( $var_val ); ?>"
						title="<?php echo esc_attr( $color['label'] ); ?>"
						aria-pressed="<?php echo $selected ? 'true' : 'false'; ?>"
						style="background-color: <?php echo esc_attr( $color['default'] ); ?>;"
					></button>
				<?php endforeach; ?>

				<!-- Custom colour button -->
				<button
					type="button"
					class="eb-palette-swatch eb-palette-custom<?php echo ! $is_palette_var ? ' is-selected' : ''; ?>"
					data-var="custom"
					title="<?php esc_attr_e( 'Custom colour', 'energieburcht' ); ?>"
					aria-pressed="<?php echo ! $is_palette_var ? 'true' : 'false'; ?>"
				>
					<span class="dashicons dashicons-art" aria-hidden="true"></span>
				</button>

				<!-- Reset to default — inline at the end of the swatches row -->
				<button
					type="button"
					class="eb-reset-btn"
					title="<?php esc_attr_e( 'Reset to default', 'energieburcht' ); ?>"
				>
					<span class="dashicons dashicons-image-rotate" aria-hidden="true"></span>
				</button>

			</div><!-- .eb-palette-swatches -->

			<!-- Custom wp-color-picker (shown only when "Custom" is active) -->
			<div class="eb-custom-picker-row<?php echo ! $is_palette_var ? ' is-visible' : ''; ?>">
				<input
					type="text"
					class="eb-color-picker-input"
					value="<?php echo esc_attr( $custom_value ); ?>"
					data-default-color="<?php echo esc_attr( $custom_value ); ?>"
				>
			</div>

			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description">
					<?php echo esc_html( $this->description ); ?>
				</span>
			<?php endif; ?>

			<!-- Hidden input linked to the WP Customizer setting -->
			<input
				type="hidden"
				class="eb-palette-hidden-value"
				<?php $this->link(); ?>
				value="<?php echo esc_attr( $current ); ?>"
				data-default="<?php echo esc_attr( $this->setting->default ); ?>"
			>

		</div><!-- .eb-palette-color-control -->
		<?php
	}
}
