( function( $, api ) {
    'use strict';

    api.controlConstructor['energieburcht-responsive-range'] = api.Control.extend( {
        ready: function() {
            var control = this;
            var container = control.container;
            
            // Elements
            var responsiveToggles = container.find( '.responsive-toggle' );
            var responsiveFields = container.find( '.responsive-field' );
            var sliders = container.find( '.energieburcht-range-slider' );
            var numbers = container.find( '.energieburcht-range-number' );
            var unitToggles = container.find( '.unit-toggle' );
            var unitDisplays = container.find( '.unit-display' );
            var hiddenInput = container.find( '.energieburcht-responsive-value' );
            var resetButton = container.find( '.reset-button' );

            // Initialization state
            var currentValue = JSON.parse( hiddenInput.val() );

            // Helpers
            function updateState() {
                hiddenInput.val( JSON.stringify( currentValue ) ).trigger( 'change' );
                control.setting.set( hiddenInput.val() ); // Force update setting
            }

            function syncInputs( device, val ) {
                container.find( '.energieburcht-range-slider[data-device="' + device + '"]' ).val( val );
                container.find( '.energieburcht-range-number[data-device="' + device + '"]' ).val( val );
            }

            // 1. Device Toggling
            responsiveToggles.on( 'click', function() {
                var device = $( this ).data( 'device' );
                
                // Update toggles
                responsiveToggles.removeClass( 'active' );
                $( this ).addClass( 'active' );
                
                // Update fields
                responsiveFields.removeClass( 'active' );
                container.find( '.responsive-field[data-device="' + device + '"]' ).addClass( 'active' );
            } );

            // 2. Input Handling (Slider & Number)
            sliders.on( 'input', function() {
                var device = $( this ).data( 'device' );
                var val = $( this ).val();
                
                currentValue[ device ] = val;
                syncInputs( device, val );
                updateState();
            } );

            numbers.on( 'input', function() {
                var device = $( this ).data( 'device' );
                var val = $( this ).val();
                
                currentValue[ device ] = val;
                syncInputs( device, val );
                updateState();
            } );

            // 3. Unit Switching
            unitToggles.on( 'click', function() {
                var unit = $( this ).data( 'unit' );
                
                // Update UI state
                unitToggles.removeClass( 'active' );
                $( this ).addClass( 'active' );
                unitDisplays.text( unit );

                // Update value
                currentValue.unit = unit;
                updateState();
            } );

            // 4. Reset Button
            resetButton.on( 'click', function() {
                // Determine defaults (could be passed via data attributes, defaulting to empty/px here for simplicity if not set)
                // In a perfect world, we'd pass the default values from PHP to JS.
                // for now, we reset to empty strings and 'px'.
                
                var defaultVal = {
                    desktop: '',
                    tablet: '',
                    mobile: '',
                    unit: 'px'
                };

                // Ideally read from control.params.default if available and matched structure
                
                currentValue = defaultVal;
                
                // Reset UI
                ['desktop', 'tablet', 'mobile'].forEach( function( device ) {
                    syncInputs( device, '' );
                });

                // Reset Unit
                unitToggles.removeClass( 'active' );
                unitToggles.filter( '[data-unit="px"]' ).addClass( 'active' );
                unitDisplays.text( 'px' );

                updateState();
            } );
            
            // Listen for external preview changes (if needed, though this is usually one-way)
        }
    } );

} )( jQuery, wp.customize );

// =============================================================================
// Palette Colour Control
// =============================================================================

( function( $, api ) {
    'use strict';

    api.controlConstructor['energieburcht-palette-color'] = api.Control.extend( {
        ready: function() {
            var control    = this;
            var container  = control.container;
            var swatches   = container.find( '.eb-palette-swatch' );
            var hidden     = container.find( '.eb-palette-hidden-value' );
            var pickerRow  = container.find( '.eb-custom-picker-row' );
            var pickerInput = container.find( '.eb-color-picker-input' );
            var resetBtn   = container.find( '.eb-reset-btn' );

            // ── Initialise wp-color-picker on the custom hex input ────────────
            pickerInput.wpColorPicker( {
                change: function( e, ui ) {
                    var hex = ui.color.toString();
                    hidden.val( hex ).trigger( 'change' );
                    control.setting.set( hex );
                    // Deselect all palette swatches, select "Custom" button.
                    swatches.removeClass( 'is-selected' ).attr( 'aria-pressed', 'false' );
                    container.find( '.eb-palette-custom' ).addClass( 'is-selected' ).attr( 'aria-pressed', 'true' );
                },
                clear: function() {
                    hidden.val( '' ).trigger( 'change' );
                    control.setting.set( '' );
                }
            } );

            // ── Palette swatch + Custom button click ──────────────────────────
            swatches.on( 'click', function() {
                var $btn = $( this );
                var val  = $btn.data( 'var' );

                // Update selected state on all swatches.
                swatches.removeClass( 'is-selected' ).attr( 'aria-pressed', 'false' );
                $btn.addClass( 'is-selected' ).attr( 'aria-pressed', 'true' );

                if ( val === 'custom' ) {
                    // Show the colour picker; do NOT overwrite the setting until
                    // the user actually picks a colour (handled by wpColorPicker above).
                    pickerRow.addClass( 'is-visible' );
                } else {
                    // Store the CSS variable reference and hide the picker.
                    pickerRow.removeClass( 'is-visible' );
                    hidden.val( val ).trigger( 'change' );
                    control.setting.set( val );
                }
            } );

            // ── Reset to default ──────────────────────────────────────────────
            resetBtn.on( 'click', function() {
                var defaultVal = hidden.data( 'default' );
                if ( ! defaultVal ) {
                    return;
                }

                var isPaletteVar = /^var\(--eb-[a-z-]+\)$/.test( defaultVal );

                hidden.val( defaultVal ).trigger( 'change' );
                control.setting.set( defaultVal );

                if ( isPaletteVar ) {
                    // Select the matching palette swatch.
                    pickerRow.removeClass( 'is-visible' );
                    swatches.removeClass( 'is-selected' ).attr( 'aria-pressed', 'false' );
                    swatches.filter( '[data-var="' + defaultVal + '"]' )
                        .addClass( 'is-selected' ).attr( 'aria-pressed', 'true' );
                } else {
                    // Default is a hex — activate Custom picker and set the value.
                    swatches.removeClass( 'is-selected' ).attr( 'aria-pressed', 'false' );
                    container.find( '.eb-palette-custom' )
                        .addClass( 'is-selected' ).attr( 'aria-pressed', 'true' );
                    pickerRow.addClass( 'is-visible' );
                    pickerInput.wpColorPicker( 'color', defaultVal );
                }
            } );
        }
    } );

} )( jQuery, wp.customize );
