( function ( blocks, element, serverSideRender, blockEditor, components ) {
	var el = element.createElement;
	var Fragment = element.Fragment;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = components.PanelBody;
	var RangeControl = components.RangeControl;

	blocks.registerBlockType( 'tajwar/testimonial-slider', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = blockEditor.useBlockProps();

			return el(
				Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: 'Slides per view', initialOpen: true },
						el( RangeControl, {
							label: 'Desktop',
							value: attributes.perViewDesktop,
							onChange: function ( value ) { setAttributes( { perViewDesktop: value } ); },
							min: 1,
							max: 6,
						} ),
						el( RangeControl, {
							label: 'Tablet',
							value: attributes.perViewTablet,
							onChange: function ( value ) { setAttributes( { perViewTablet: value } ); },
							min: 1,
							max: 6,
						} ),
						el( RangeControl, {
							label: 'Mobile',
							value: attributes.perViewMobile,
							onChange: function ( value ) { setAttributes( { perViewMobile: value } ); },
							min: 1,
							max: 6,
						} )
					)
				),
				el(
					'div',
					blockProps,
					el( serverSideRender, { block: 'tajwar/testimonial-slider', attributes: attributes } )
				)
			);
		},
		save: function () {
			// Dynamic block -- rendered entirely by render.php from the
			// Testimonial custom post type, nothing to save client-side.
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender, window.wp.blockEditor, window.wp.components );
