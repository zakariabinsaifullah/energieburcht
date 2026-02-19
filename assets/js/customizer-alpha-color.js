/**
 * Customizer Alpha Color Control Linkage
 */
( function( $, api ) {

	api.controlConstructor['energieburcht-alpha-color'] = api.Control.extend( {
		ready: function() {
			var control = this;
			var input = control.container.find( '.alpha-color-control-input' );

			// Initialize wpColorPicker with alpha support
			// Note: wp-color-picker-alpha.js extends the widget so passing alphaEnabled: true triggers it.
			var pickerOptions = {
				change: function( event, ui ) {
					var color = ui.color.toString(); // Uses custom toString if alpha enabled
					control.setting.set( color );
				},
				clear: function() {
					control.setting.set( '' );
				},
				palettes: input.data( 'palette' ) ? input.data( 'palette' ).split( '|' ) : true  // Use standard palette if not custom
			};

			input.wpColorPicker( pickerOptions );
		}
	} );

} )( jQuery, wp.customize );
