( function ( blocks, element, serverSideRender, blockEditor ) {
	var el = element.createElement;

	blocks.registerBlockType( 'tajwar/stats-counter', {
		edit: function () {
			var blockProps = blockEditor.useBlockProps();
			return el(
				'div',
				blockProps,
				el( serverSideRender, { block: 'tajwar/stats-counter' } )
			);
		},
		save: function () {
			// Dynamic block -- render.php outputs the markup, view.js
			// animates it on the front end. Nothing to save client-side.
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender, window.wp.blockEditor );
