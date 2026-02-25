/**
 * Gutenberg Extension: Button Hover Colors
 *
 * Adds Hover Background and Hover Text color controls to the core/button block.
 */

(function (wp) {
  const { addFilter } = wp.hooks;
  const { createHigherOrderComponent } = wp.compose;
  const { InspectorControls } = wp.blockEditor;
  const { PanelBody, ColorPalette } = wp.components;
  const { select } = wp.data;

  const isValidBlockType = (name) => name === 'core/button';

  /**
   * 1. Add custom attributes to the core/button block.
   */
  function addHoverColorAttributes(settings, name) {
    if (!isValidBlockType(name)) {
      return settings;
    }

    // Ensure attributes object exists
    if (typeof settings.attributes !== 'undefined') {
      settings.attributes = Object.assign(settings.attributes, {
        hoverBackgroundColor: {
          type: 'string',
        },
        hoverTextColor: {
          type: 'string',
        },
      });
    }

    return settings;
  }
  addFilter(
    'blocks.registerBlockType',
    'energieburcht/button-hover-attributes',
    addHoverColorAttributes,
  );

  /**
   * 2. Add Inspector Controls (Sidebar Panel).
   */
  const withHoverColorControls = createHigherOrderComponent((BlockEdit) => {
    return (props) => {
      if (!isValidBlockType(props.name)) {
        return wp.element.createElement(BlockEdit, props);
      }

      const { attributes, setAttributes } = props;
      const { hoverBackgroundColor, hoverTextColor } = attributes;

      // Fetch theme color palette
      const colors = select('core/block-editor').getSettings().colors || [];

      return wp.element.createElement(
        wp.element.Fragment,
        {},
        wp.element.createElement(BlockEdit, props),
        wp.element.createElement(
          InspectorControls,
          {},
          wp.element.createElement(
            PanelBody,
            {
              title: 'Hover Colors',
              initialOpen: false,
            },
            wp.element.createElement(
              'div',
              {
                className: 'eb-color-control-wrapper',
                style: { marginBottom: '1rem' },
              },
              wp.element.createElement(
                'p',
                { style: { marginBottom: '8px' } },
                'Hover Text',
              ),
              wp.element.createElement(ColorPalette, {
                colors: colors,
                value: hoverTextColor,
                onChange: (val) => setAttributes({ hoverTextColor: val }),
                clearable: true,
              }),
            ),
            wp.element.createElement(
              'div',
              { className: 'eb-color-control-wrapper' },
              wp.element.createElement(
                'p',
                { style: { marginBottom: '8px' } },
                'Hover Background',
              ),
              wp.element.createElement(ColorPalette, {
                colors: colors,
                value: hoverBackgroundColor,
                onChange: (val) => setAttributes({ hoverBackgroundColor: val }),
                clearable: true,
              }),
            ),
          ),
        ),
      );
    };
  }, 'withHoverColorControls');

  addFilter(
    'editor.BlockEdit',
    'energieburcht/button-hover-controls',
    withHoverColorControls,
  );
})(window.wp);
