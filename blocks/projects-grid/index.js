( function ( blocks, element, serverSideRender, blockEditor ) {
	var el = element.createElement;

	blocks.registerBlockType( 'tajwar/projects-grid', {
		edit: function () {
			var blockProps = blockEditor.useBlockProps();
			return el(
				'div',
				blockProps,
				el( serverSideRender, { block: 'tajwar/projects-grid' } )
			);
		},
		save: function () {
			// Dynamic block -- rendered entirely by render.php from the
			// Project custom post type, nothing to save client-side.
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender, window.wp.blockEditor );
