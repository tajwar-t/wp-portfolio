( function ( blocks, blockEditor, element ) {
	var el = element.createElement;
	var RichText = blockEditor.RichText;
	var useBlockProps = blockEditor.useBlockProps;

	blocks.registerBlockType( 'tajwar/skill-pill', {
		edit: function ( props ) {
			var blockProps = useBlockProps();
			return el( RichText, Object.assign( {}, blockProps, {
				tagName: 'li',
				value: props.attributes.text,
				onChange: function ( value ) {
					props.setAttributes( { text: value } );
				},
				placeholder: 'Skill',
				allowedFormats: [],
			} ) );
		},
		save: function ( props ) {
			var blockProps = useBlockProps.save();
			return el( RichText.Content, Object.assign( {}, blockProps, {
				tagName: 'li',
				value: props.attributes.text,
			} ) );
		},
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.element );
