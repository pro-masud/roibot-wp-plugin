<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://www.roiforpros.com/r/
 * @since      1.0.0
 *
 * @package    Roibot
 * @subpackage Roibot/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Roibot
 * @subpackage Roibot/admin
 * @author     Frank <frank@mybuildapp.com>
 */
class Roibot_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Roibot_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Roibot_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/roibot-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Roibot_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Roibot_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/roibot-admin.js', array( 'jquery' ), $this->version, false );

	}

	// === Add below your existing methods in class Roibot_Admin ===

	const OPTION = 'roibot_settings';

	public function add_plugin_admin_menu() {
		add_menu_page(
			'Roibot Settings',
			'Roibot',
			'manage_options',
			'roibot',
			array( $this, 'render_settings_page' ),
			'dashicons-format-chat',
			56
		);
	}

	public function register_settings() {
		register_setting( 'roibot_settings_group', self::OPTION, array( $this, 'sanitize_settings' ) );
	}

	public function sanitize_settings( $input ) {
		$out = array();

		$out['sitewide_enable'] = ! empty( $input['sitewide_enable'] ) ? 1 : 0;

		$out['brand_primary'] = isset( $input['brand_primary'] ) ? sanitize_hex_color( $input['brand_primary'] ) : '';
		$out['brand_accent']  = isset( $input['brand_accent'] )  ? sanitize_hex_color( $input['brand_accent'] )  : '';
		$out['brand_bg']      = isset( $input['brand_bg'] )      ? sanitize_hex_color( $input['brand_bg'] )      : '';
		$out['brand_text']    = isset( $input['brand_text'] )    ? sanitize_hex_color( $input['brand_text'] )    : '';

		$out['header_name'] = isset( $input['header_name'] ) ? sanitize_text_field( $input['header_name'] ) : '';
		$out['avatar1']     = isset( $input['avatar1'] )     ? esc_url_raw( $input['avatar1'] )             : '';
		$out['avatar2']     = isset( $input['avatar2'] )     ? esc_url_raw( $input['avatar2'] )             : '';

		$out['popup_text']   = isset( $input['popup_text'] )   ? wp_kses_post( $input['popup_text'] )   : '';
		$out['welcome_text'] = isset( $input['welcome_text'] ) ? wp_kses_post( $input['welcome_text'] ) : '';

		$out['news_items'] = array();
    if ( ! empty( $input['news_items'] ) && is_array( $input['news_items'] ) ) {
        foreach ( array_values( $input['news_items'] ) as $item ) { // reindex safely
            $title = isset( $item['title'] ) ? sanitize_text_field( $item['title'] ) : '';
            $url   = isset( $item['url'] )   ? esc_url_raw( $item['url'] )           : '';
            if ( $title && $url ) {
                $out['news_items'][] = array( 'title' => $title, 'url' => $url );
            }
        }
    }

    return $out;
	}

	public function render_settings_page() {
		$opts = get_option( self::OPTION, array() );
		include plugin_dir_path( __FILE__ ) . 'partials/roibot-settings-page.php';
	}

	/** Only for our settings screen */
	public function admin_enqueue( $hook ) {
		if ( $hook !== 'toplevel_page_roibot' ) return;
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'roibot-admin-settings', plugin_dir_url( __FILE__ ) . 'css/roibot-admin.css', array(), $this->version );
		wp_enqueue_script( 'roibot-admin-settings', plugin_dir_url( __FILE__ ) . 'js/roibot-admin.js', array( 'jquery', 'wp-color-picker' ), $this->version, true );
	}


}
