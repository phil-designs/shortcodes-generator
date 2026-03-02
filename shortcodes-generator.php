<?php
/*
Plugin Name: Phil.Designs Shortcodes Generator
Plugin URI: http://www.phildesigns.com
Description: A shortcode generator to add buttons, columns, tabs, toggles and more to your theme.
Version: 4.0.0
Author: phil.designs | Phillip De Vita
Author URI: http://www.phildesigns.com
*/

class Shortcodes {

    function __construct()
    {
    	define( 'PDSC_VERSION', '3.0' );
    	// option name for user entered CSS
    	if ( ! defined( 'PDSC_CUSTOM_CSS_OPTION' ) ) {
    		define( 'PDSC_CUSTOM_CSS_OPTION', 'pdsc_custom_css' );
    	}
    	
    	/** Create a filter for "Theme Mode" **/
    	define( 'PDSC_THEME_MODE', apply_filters( 'pdsc_theme_mode', false ) );
    		
    	if ( false == PDSC_THEME_MODE) {
    		define( 'PDSC_PLUGIN_URL', plugin_dir_url( __FILE__ ));
    		define( 'PDSC_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
      } else {
        if ( true == PDSC_THEME_MODE ) {
    		 $path = ltrim( end( @explode( get_stylesheet(), str_replace( '\\', '/', dirname( __FILE__ ) ) ) ), '/' );
    		define( 'PDSC_PLUGIN_URL',  trailingslashit(trailingslashit(get_bloginfo('template_directory') ). $path));
    		define( 'PDSC_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
       	}
    	  }

    	require_once( PDSC_PLUGIN_DIR .'includes/shortcodes.php' );

        // enqueue scripts/styles on the front end at the proper time
        add_action( 'wp_enqueue_scripts', array( $this, 'init' ) );
        add_action( 'admin_init', array(&$this, 'admin_init') );
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        // always load tinymce helper script on admin pages so any editor can register the plugin
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        // add settings link under plugin name on Plugins screen
        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_settings_link' ) );
	}

	/**
	 * Enqueue front end scripts and styles
	 *
	 * @return	void
	 */
	// originally hooked to init, but now fired on wp_enqueue_scripts to allow reliable checking
	function init()
	{
		if ( ! is_admin() ) {
			// check if FontAwesome has already been registered or enqueued by the theme or another plugin
			// common handles used by themes/plugins: "fontawesome", "font-awesome", "fa"
			$already_loaded = false;
			$handles = array( 'fontawesome', 'font-awesome', 'fa' );
			foreach ( $handles as $handle ) {
				if ( wp_style_is( $handle, 'registered' ) || wp_style_is( $handle, 'enqueued' ) ) {
					$already_loaded = true;
					break;
				}
			}

			/**
			 * Filter whether the plugin should load FontAwesome.
			 * Return false if the theme already provides it or you wish to disable it.
			 *
			 * @param bool $load Whether to enqueue FontAwesome (default: true if not detected).
			 */
			$load_fa = apply_filters( 'pdsc_load_fontawesome', ! $already_loaded );

			if ( $load_fa ) {
				// Enqueue FontAwesome from CDN (latest version)
				$fontawesome_url = apply_filters( 'pdsc_fontawesome_cdn_url', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css' );
				wp_enqueue_style( 'fontawesome', $fontawesome_url );
			}

			wp_enqueue_style( 'shortcodes', PDSC_PLUGIN_URL . 'assets/css/shortcodes.css' );
			// append user custom css if provided
			$custom_css = get_option( defined( 'PDSC_CUSTOM_CSS_OPTION' ) ? PDSC_CUSTOM_CSS_OPTION : 'pdsc_custom_css', '' );
			if ( ! empty( $custom_css ) ) {
				wp_add_inline_style( 'shortcodes', $custom_css );
			}
			wp_enqueue_script( 'shortcodes-lib', PDSC_PLUGIN_URL . 'assets/js/shortcodes-lib.js', array('jquery', 'jquery-ui-accordion', 'jquery-ui-tabs') );

			//Animate.css
			wp_enqueue_style( 'animate', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css', array(), '4.1.1' );
	
			//WOW.js
			wp_enqueue_style( 'wow-animate', PDSC_PLUGIN_URL . 'assets/css/animate.css', array(), '1.1.2' );
			wp_enqueue_script( 'wow', 'https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js', array(), '1.1.2', true );
		}
	}

	/**
	 * Enqueue Scripts and Styles
	 *
	 * @return	void
	 */
	function admin_init()
	{
		include_once( PDSC_PLUGIN_DIR . 'includes/class-admin-insert.php' );

		// register option for custom css
		register_setting(
			'pdsc_settings',
			PDSC_CUSTOM_CSS_OPTION,
			array( 'sanitize_callback' => array( $this, 'sanitize_css' ) )
		);

		// ensure thickbox scripts/styles are available for the popup
		add_thickbox();

		// css
		wp_enqueue_style( 'popup', PDSC_PLUGIN_URL . 'assets/css/admin.css', false, '1.0', 'all' );

		// js
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_localize_script( 'jquery', 'Shortcodes', array('plugin_folder' => WP_PLUGIN_URL .'/shortcodes') );

		// register TinyMCE plugin/button for classic editor blocks and ACF
		add_filter( 'mce_external_plugins', array( $this, 'register_tinymce_plugin' ) );
		add_filter( 'mce_buttons', array( $this, 'register_tinymce_button' ) );
	}

	/**
	 * Sanitize the CSS entered by the user.
	 *
	 * @param string $css Raw user input.
	 * @return string Sanitized string safe to output inside a <style> tag.
	 */
	public function sanitize_css( $css ) {
		// strip any HTML tags; allow all normal CSS characters.
		$css = wp_strip_all_tags( $css );
		// trim whitespace
		return trim( $css );
	}

	/**
	 * Add the settings page under the "Settings" menu.
	 */
	public function add_admin_menu() {
		add_options_page(
			__( 'Shortcodes Generator', 'shortcodes-generator' ),
			__( 'Shortcodes Generator Styles', 'shortcodes-generator' ),
			'manage_options',
			'pdsc_settings',
			array( $this, 'settings_page_html' )
		);
	}

	/**
	 * Output the HTML for the plugin settings page.
	 */
	public function settings_page_html() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		// show messages
		if ( isset( $_GET['settings-updated'] ) ) {
			add_settings_error( 'pdsc_messages', 'pdsc_message', __( 'Settings Saved', 'shortcodes-generator' ), 'updated' );
		}
		settings_errors( 'pdsc_messages' );
		$custom_css = get_option( PDSC_CUSTOM_CSS_OPTION, '' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Shortcodes Generator Settings', 'shortcodes-generator' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'pdsc_settings' );
				do_settings_sections( 'pdsc_settings' );
				?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( PDSC_CUSTOM_CSS_OPTION ); ?>"><?php esc_html_e( 'Custom CSS', 'shortcodes-generator' ); ?></label></th>
						<td>
							<textarea id="<?php echo esc_attr( PDSC_CUSTOM_CSS_OPTION ); ?>" name="<?php echo esc_attr( PDSC_CUSTOM_CSS_OPTION ); ?>" rows="10" cols="50" class="large-text code"><?php echo esc_textarea( $custom_css ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Enter CSS that will be output on the front end in addition to the plugin styles. This is useful for overrides.', 'shortcodes-generator' ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}


	/**
	 * Add external TinyMCE plugin file path
	 */
	public function register_tinymce_plugin( $plugins ) {
		$plugins['pdsc_shortcodes'] = PDSC_PLUGIN_URL . 'assets/js/tinymce-shortcodes.js';
		return $plugins;
	}

	/**
	 * Add button to TinyMCE toolbar
	 */
	public function register_tinymce_button( $buttons ) {
		array_push( $buttons, 'pdsc_shortcodes' );
		return $buttons;
	}

	/**
	 * Load the tinymce plugin script on every admin page. This ensures
	 * the plugin is available whether editors are created via Gutenberg
	 * (Classic block, ACF) or traditional pages.
	 */
	public function enqueue_admin_assets() {
		wp_enqueue_script(
			'pdsc-tinymce-plugin',
			PDSC_PLUGIN_URL . 'assets/js/tinymce-shortcodes.js',
			array(),
			'1.0',
			false
		);
	}

	/**
	 * Add a "Settings" link on the Plugins page listing.
	 *
	 * @param array $links Existing action links.
	 * @return array Modified links including settings.
	 */
	public function add_settings_link( $links ) {
		$settings_url = admin_url( 'options-general.php?page=pdsc_settings' );
		$settings_link = '<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings', 'shortcodes-generator' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

}
new Shortcodes();

?>