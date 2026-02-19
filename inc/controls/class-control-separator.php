<?php
/**
 * Separator Control
 *
 * @package Energieburcht
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Energieburcht_Customize_Separator_Control
 *
 * Renders a simple separator/heading in the Customizer.
 */
class Energieburcht_Customize_Separator_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'energieburcht-separator';

	/**
	 * Render the control's content.
	 */
	public function render_content() {
		if ( ! empty( $this->label ) ) : ?>
			<div class="customize-control-separator-label" style="
				margin-top: 20px;
				margin-bottom: 10px;
				padding-bottom: 5px;
				border-bottom: 1px solid #ddd;
				font-weight: 600;
				color: #555;
				text-transform: uppercase;
				font-size: 11px;
				letter-spacing: 0.5px;
			">
				<?php echo esc_html( $this->label ); ?>
			</div>
		<?php endif;
		if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif;
	}
}
