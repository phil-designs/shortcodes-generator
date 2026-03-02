/**
 * Gutenberg Integration for Alchemy + Aim Shortcodes
 * Adds shortcode insertion button to the Gutenberg editor
 */

(function() {
	const { registerPlugin } = wp.plugins;
	const { PluginSidebar } = wp.editPost;
	const { createElement: el } = wp.element;
	const { Button } = wp.components;
	const { Fragment } = wp.element;

	// Only register if not already registered
	if ( typeof wp !== 'undefined' && typeof wp.plugins !== 'undefined' ) {
		registerPlugin( 'pd-shortcodes-button', {
			render: function() {
				return el(
					PluginSidebar,
					{
						name: 'pd-shortcodes-sidebar',
						title: 'Shortcodes'
					},
					el(
						'div',
						{ style: { padding: '16px' } },
						el(
							Button,
							{
								isPrimary: true,
								onClick: function() {
									// Open the thickbox with the shortcode selector
									tb_show( 'Insert Shortcode', '#TB_inline?width=4000&inlineId=choose-shortcode' );
								}
							},
							'Insert Shortcode'
						)
					)
				);
			}
		} );

		// Add button to the toolbar using block editor hooks
		wp.hooks.addFilter(
			'editor.BlockEdit',
			'pd-shortcodes/add-toolbar-button',
			function( BlockEdit ) {
				return function( props ) {
					return el(
						Fragment,
						null,
						el( BlockEdit, props )
					);
				};
			}
		);

		// Add keyboard shortcut listener
		document.addEventListener( 'keydown', function( event ) {
			// Ctrl+Shift+S (or Cmd+Shift+S on Mac) to open shortcode dialog
			if ( ( event.ctrlKey || event.metaKey ) && event.shiftKey && event.key === 'S' ) {
				event.preventDefault();
				tb_show( 'Insert Shortcode', '#TB_inline?width=4000&inlineId=choose-shortcode' );
			}
		} );
	}

	// Override the window.send_to_editor to work with Gutenberg
	if ( typeof window.send_to_editor === 'function' ) {
		const originalSendToEditor = window.send_to_editor;
		window.send_to_editor = function( html ) {
			// Check if we're in the Gutenberg editor
			if ( typeof wp !== 'undefined' && typeof wp.data !== 'undefined' ) {
				const { dispatch } = wp.data;
				// Insert the shortcode as a block
				dispatch( 'core/editor' ).insertBlocks(
					wp.blocks.createBlock( 'core/paragraph', {
						content: html
					} )
				);
			} else {
				// Fall back to classic editor behavior
				originalSendToEditor.call( this, html );
			}
		};
	}

})();
