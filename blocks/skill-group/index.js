( function ( blocks, blockEditor, element ) {
	var el = element.createElement;
	var RichText = blockEditor.RichText;
	var InnerBlocks = blockEditor.InnerBlocks;
	var useBlockProps = blockEditor.useBlockProps;
	var useInnerBlocksProps = blockEditor.useInnerBlocksProps;

	var ALLOWED = [ 'tajwar/skill-pill' ];
	var TEMPLATE = [
		[ 'tajwar/skill-pill', { text: 'Skill' } ],
	];

	blocks.registerBlockType( 'tajwar/skill-group', {
		edit: function ( props ) {
			var blockProps = useBlockProps( { className: 'skill-group' } );
			var innerBlocksProps = useInnerBlocksProps(
				{ className: 'pill-list' },
				{
					allowedBlocks: ALLOWED,
					template: TEMPLATE,
					templateInsertUpdatesSelection: false,
					renderAppender: InnerBlocks.ButtonBlockAppender,
				}
			);

			return el(
				'div',
				blockProps,
				el( RichText, {
					tagName: 'h3',
					className: 'mono',
					value: props.attributes.title,
					onChange: function ( value ) {
						props.setAttributes( { title: value } );
					},
					placeholder: 'Category name',
					allowedFormats: [],
				} ),
				el( 'ul', innerBlocksProps )
			);
		},
		save: function ( props ) {
			var blockProps = useBlockProps.save( { className: 'skill-group' } );
			return el(
				'div',
				blockProps,
				el( RichText.Content, {
					tagName: 'h3',
					className: 'mono',
					value: props.attributes.title,
				} ),
				el( 'ul', { className: 'pill-list' }, el( InnerBlocks.Content ) )
			);
		},
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.element );
