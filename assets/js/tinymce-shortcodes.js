(function() {
	// Registration helper; called when TiniMCE is ready
	function register() {
		if ( typeof tinymce === 'undefined' ) {
			return;
		}

		if ( tinymce.PluginManager.get('pdsc_shortcodes') ) {
			return; // already registered
		}

		tinymce.create('tinymce.plugins.pdscShortcodes', {
			init : function(ed, url) {
				ed.addButton('pdsc_shortcodes', {
					title : 'Insert Shortcode',
					icon : 'dashicons-editor-code',
					onclick : function() {
						tb_show('Insert Shortcode', '#TB_inline?width=4000&inlineId=choose-shortcode');
					}
				});
			},
		});

		tinymce.PluginManager.add('pdsc_shortcodes', tinymce.plugins.pdscShortcodes);
	}

	// try immediate registration
	register();

	// also listen for editors initialising later and explicitly add the button
	document.addEventListener('tinymce-editor-init', function(event) {
		register();
		var ed = event.editor;
		if ( ed && typeof ed.addButton === 'function' ) {
			ed.addButton('pdsc_shortcodes', {
				title : 'Insert Shortcode',
				icon : 'dashicons-editor-code',
				onclick : function() {
					tb_show('Insert Shortcode', '#TB_inline?width=4000&inlineId=choose-shortcode');
				}
			});
		}
	});
})();
