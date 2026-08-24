( function ( blocks, element, serverSideRender, blockEditor ) {
	var el = element.createElement;

	blocks.registerBlockType( 'tajwar/experience-timeline', {
		edit: function () {
			var blockProps = blockEditor.useBlockProps();
			return el(
				'div',
				blockProps,
				el( serverSideRender, { block: 'tajwar/experience-timeline' } )
			);
		},
		save: function () {
			// Dynamic block -- rendered entirely by render.php from the
			// Experience custom post type, nothing to save client-side.
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender, window.wp.blockEditor );
