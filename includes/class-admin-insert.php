<?php
/**
 * Creates the admin interface to add shortcodes to the editor
**/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * PDSC_Admin_Insert class
 */
class PDSC_Admin_Insert {

	/**
	 * __construct function
	 *
	 * @access public
	 * @return  void
	 */
	public function __construct() {
		// Safety check to ensure plugin is properly initialized
		if ( ! defined( 'PDSC_PLUGIN_DIR' ) ) {
			return;
		}

		add_action( 'media_buttons', array( $this, 'media_buttons' ), 20 );
		add_action( 'admin_footer', array( $this, 'popup_html' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_gutenberg_assets' ) );
	}

	/**
	 * media_buttons function
	 *
	 * @access public
	 * @return void
	 */
	public function media_buttons( $editor_id = 'content' ) {
		global $pagenow;

		// Only run on add/edit screens
		if ( in_array( $pagenow, array('post.php', 'page.php', 'post-new.php', 'post-edit.php') ) ) {
			$output = '<a href="#TB_inline?width=4000&amp;inlineId=choose-shortcode" class="thickbox button button-primary aa-shortcode-btn thicbox" title="' . __( 'phil.designs Shortcodes' ) . '">' . __( 'Insert Shortcode' ) . '</a>';
		}
		echo $output;
	}

	/**
	 * Enqueue assets for Gutenberg block editor
	 *
	 * @access public
	 * @return void
	 */
	public function enqueue_gutenberg_assets() {
		// Gutenberg-specific integration UI
		wp_enqueue_script(
			'pd-shortcodes-gutenberg',
			PDSC_PLUGIN_URL . 'assets/js/gutenberg-integration.js',
			array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components' ),
			'1.0',
			true
		);

		wp_localize_script(
			'pd-shortcodes-gutenberg',
			'PDShortcodesGutenberg',
			array(
				'popupId' => 'choose-shortcode',
			)
		);

		// ensure the TinyMCE plugin file is available to any editors created inside Gutenberg (Classic block, ACF WYSIWYG)
		wp_enqueue_script(
			'pdsc-tinymce-plugin',
			PDSC_PLUGIN_URL . 'assets/js/tinymce-shortcodes.js',
			array(),
			'1.0',
			false
		);
	}

	/**
	 * Build out the input fields for shortcode content
	 * @param  string $key
	 * @param  array $param the parameters of the input
	 * @return void
	 */
	public function build_fields($key, $param) {
		$html = '<tr>';
		$html .= '<td class="label">' . $param['label'] . ':</td>';
		switch( $param['type'] )
		{
			case 'text' :

				// prepare
				$output = '<td><label class="screen-reader-text" for="' . $key .'">' . $param['label'] . '</label>';
				$output .= '<input type="text" class="form-text" name="' . $key . '" id="' . $key . '" value="' . $param['std'] . '" />' . "\n";
				$output .= '<span class="form-desc">' . $param['desc'] . '</span></td>' . "\n";

				// append
				$html .= $output;

				break;

			case 'textarea' :

				// prepare
				$output = '<td><label class="screen-reader-text" for="' . $key .'">' . $param['label'] . '</label>';
				$output .= '<textarea rows="10" cols="30" name="' . $key . '" id="' . $key . '" class="form-textarea">' . $param['std'] . '</textarea>' . "\n";
				$output .= '<span class="form-desc">' . $param['desc'] . '</span></td>' . "\n";

				// append
				$html .= $output;

				break;

			case 'select' :

				// prepare
				$output = '<td><label class="screen-reader-text" for="' . $key .'">' . $param['label'] . '</label>';
				$output .= '<select name="' . $key . '" id="' . $key . '" class="form-select">' . "\n";

				foreach( $param['options'] as $value => $option )
				{
					$output .= '<option value="' . $value . '">' . $option . '</option>' . "\n";
				}

				$output .= '</select>' . "\n";
				$output .= '<span class="form-desc">' . $param['desc'] . '</span></td>' . "\n";

				// append
				$html .= $output;

				break;

			case 'checkbox' :

				// prepare
				$output = '<td><label class="screen-reader-text" for="' . $key .'">' . $param['label'] . '</label>';
				$output .= '<input type="checkbox" name="' . $key . '" id="' . $key . '" class="form-checkbox"' . ( $param['default'] ? 'checked' : '' ) . '>' . "\n";
				$output .= '<span class="form-desc">' . $param['desc'] . '</span></td>';

				$html .= $output;

				break;

			default :
				break;
		}
		$html .= '</tr>';

		return $html;
	}

	/**
	 * Popup window
	 *
	 * Print the footer code needed for the Insert Shortcode Popup
	 *
	 * @since 2.0
	 * @global $pagenow
	 * @return void Prints HTML
	 */
	function popup_html() {
		global $pagenow;
		include(PDSC_PLUGIN_DIR . 'includes/config.php');

		// Only run in add/edit screens
		if ( in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) ) { ?>

			<script type="text/javascript">
				function InsertShortcode() {
					// Grab input content, build the shortcodes, and insert them
					// into the content editor
					var select = jQuery('#select-shortcode').val(),
						type = select.replace('-shortcode', ''),
						template = jQuery('#' + select).data('shortcode-template'),
						childTemplate = jQuery('#' + select).data('shortcode-child-template'),
						tables = jQuery('#' + select).find('table').not('.clone-template'),
						attributes = '',
						content = '',
						contentToEditor = '';

					// go over each table, build the shortcode content
					for (var i = 0; i < tables.length; i++) {
						var elems = jQuery(tables[i]).find('input, select, textarea');

						// Build an attributes string by mapping over the input
						// fields in a given table.
						attributes = jQuery.map(elems, function(el, index) {
							var $el = jQuery(el);

							console.log(el);

							if( $el.attr('id') === 'content' ) {
								content = $el.val();
								return '';
							} else if( $el.attr('id') === 'last' ) {
								if( $el.is(':checked') ) {
									return $el.attr('id') + '="true"';
								} else {
									return '';
								}
							} else {
								return $el.attr('id') + '="' + $el.val() + '"';
							}
						});
						attributes = attributes.join(' ').trim();

						// Place the attributes and content within the provided
						// shortcode template
						if( childTemplate ) {
							// Run the replace on attributes for columns because the
							// attributes are really the shortcodes
							contentToEditor += childTemplate.replace('{{attributes}}', attributes).replace('{{attributes}}', attributes).replace('{{content}}', content);
						} else {
							// Run the replace on attributes for columns because the
							// attributes are really the shortcodes
							contentToEditor += template.replace('{{attributes}}', attributes).replace('{{attributes}}', attributes).replace('{{content}}', content);
						}
					};

					// Insert built content into the parent template
					if( childTemplate ) {
						contentToEditor = template.replace('{{child_shortcode}}', contentToEditor);
					}

					// Send the shortcode to the content editor and reset the fields
					window.send_to_editor( contentToEditor );
					ResetFields();
				}

				// Set the inputs to empty state
				function ResetFields() {
					jQuery('#shortcode-title').text('');
					jQuery('#shortcode-wrap').find('input[type=text], select').val('');
					jQuery('#shortcode-wrap').find('textarea').text('');
					jQuery('.was-cloned').remove();
					jQuery('.shortcode-type').hide();
				}

				// Function to redraw the thickbox for new content
				function ResizeTB() {
					var	ajaxCont = jQuery('#TB_ajaxContent'),
						tbWindow = jQuery('#TB_window'),
						Popup = jQuery('#shortcode-wrap');

					ajaxCont.css({
						height: (tbWindow.outerHeight()-47),
						overflow: 'auto', // IMPORTANT
						width: (tbWindow.outerWidth() - 30)
					});
				}

				// Simple function to clone an included template
				function CloneContent(el) {
					var clone = jQuery(el).find('.clone-template').clone().removeClass('hidden clone-template').removeAttr('id').addClass('was-cloned');

					jQuery(el).append(clone);
				}

				jQuery(document).ready(function($) {
					var $shortcodes = $('.shortcode-type').hide(),
						$title = $('#shortcode-title');

					// Show the selected shortcode input fields
	                $('#select-shortcode').change(function () {
	                	var text = $(this).find('option:selected').text();

	                	$shortcodes.hide();
	                	$title.text(text);
	                    $('#' + $(this).val()).show();
	                    ResizeTB();
	                });

	                // Clone a set of input fields
	                $('.clone-content').on('click', function() {
						var el = $(this).siblings('.sortable');

						CloneContent(el);
						ResizeTB();
						$('.sortable').sortable('refresh');
					});

	                // Remove a set of input fields
					$('.shortcode-type').on('click', '.remove' ,function() {
						$(this).closest('table').remove();
					});

					// Make content sortable using the jQuery UI Sortable method
					$('.sortable').sortable({
						items: 'table:not(".hidden")',
						placeholder: 'sortable-placeholder'
					});
	            });
			</script>

			<div id="choose-shortcode" style="display: none;">
				<div id="shortcode-header"><h3 id="shortcode-title"></h3></div>
				<div id="shortcode-wrap" class="wrap shortcode-wrap">
					<div class="shortcode-select">
						<label for="shortcode"><?php _e('Select the shortcode type' ); ?></label>
						<select name="shortcode" id="select-shortcode">
							<option><?php _e('Select Shortcode' ); ?></option>
						<?php foreach( $pd_shortcodes as $shortcode ) {
								echo '<option data-title="' . $shortcode['title'] . '" value="' . $shortcode['id'] . '">' . $shortcode['title'] . '</option>';
							} ?>
						</select>
					</div>

					<h3 id="shortcode-title"></h3>

				<?php

				$html = '';
				$clone_button = array( 'show' => false );

				// Loop through each shortcode building content
				foreach( $pd_shortcodes as $key => $shortcode ) {

					// Add shortcode templates to be used when building with JS
					$shortcode_template = ' data-shortcode-template="' . $shortcode['template'] . '"';
					if( array_key_exists('child_shortcode', $shortcode ) ) {
						$shortcode_template .= ' data-shortcode-child-template="' . $shortcode['child_shortcode']['template'] . '"';
					}

					// Individual shortcode 'block'
					$html .= '<div id="' . $shortcode['id'] . '" class="shortcode-type" ' . $shortcode_template . '>';

					// If shortcode has children, it can be cloned and is sortable.
					// Add a hidden clone template, and set clone button to be displayed.
					if( array_key_exists('child_shortcode', $shortcode ) ) {
						$html .= (isset($shortcode['child_shortcode']['shortcode']) ? $shortcode['child_shortcode']['shortcode'] : null);
						$shortcode['params'] = $shortcode['child_shortcode']['params'];
						$clone_button['show'] = true;
						$clone_button['text'] = $shortcode['child_shortcode']['clone_button'];
						$html .= '<div class="sortable">';
						$html .= '<table id="clone-' . $shortcode['id'] . '" class="hidden clone-template"><tbody>';
						foreach( $shortcode['params'] as $key => $param ) {
							$html .= $this->build_fields($key, $param);
						}
						if( $clone_button['show'] ) {
							$html .= '<tr><td colspan="2"><a href="#" class="remove">' . __('Remove' ) . '</a></td></tr>';
						}
						$html .= '</tbody></table>';
					}

					// Build the actual shortcode input fields
					$html .= '<table><tbody>';
					foreach( $shortcode['params'] as $key => $param ) {
						$html .= $this->build_fields($key, $param);
					}

					// Add a link to remove a content block
					if( $clone_button['show'] ) {
						$html .= '<tr><td colspan="2"><a href="#" class="remove">' . __('Remove' ) . '</a></td></tr>';
					}
					$html .= '</tbody></table>';

					// Close out the sortable div and display the clone button as needed
					if( $clone_button['show'] ) {
						$html .= '</div>';
						$html .= '<a id="add-' . $shortcode['id'] . '" href="#" class="button-secondary clone-content">' . $clone_button['text'] . '</a>';
						$clone_button['show'] = false;
					}

					// Display notes if provided
					if( array_key_exists('notes', $shortcode) ) {
						$html .= '<p class="notes">' . $shortcode['notes'] . '</p>';
					}
					$html .= '</div>';
				}

				echo $html;
				?>

				<p class="submit">
					<input type="button" id="insert-shortcode" class="button-primary" value="<?php _e('Insert Shortcode' ); ?>" onclick="InsertShortcode();" />
					<a href="#" id="cancel-shortcode-insert" class="button-secondary cancel-shortcode-insert" onclick="tb_remove();"><?php _e('Cancel' ); ?></a>
				</p>
				</div>
			</div>

		<?php
		}
	}
}

	new PDSC_Admin_Insert();