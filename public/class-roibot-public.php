<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://https://www.roiforpros.com/r/
 * @since      1.0.0
 *
 * @package    Roibot
 * @subpackage Roibot/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Roibot
 * @subpackage Roibot/public
 * @author     Frank <frank@mybuildapp.com>
 */
class Roibot_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/roibot-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$opts = get_option( 'roibot_settings', array() );

		// Send news list to JS (used by the News tab you already have)
		$news_items = isset( $opts['news_items'] ) ? $opts['news_items'] : array();
		wp_localize_script( $this->plugin_name, 'roibotNewsItems', $news_items );

		// Optional: expose a few simple strings if your JS ever needs them
		wp_localize_script( $this->plugin_name, 'roibotSettings', array(
			'header_name' => $opts['header_name'] ?? '',
			'popup_text'  => $opts['popup_text']  ?? '',
			'welcome_text'=> $opts['welcome_text']?? '',
			'avatar1'     => $opts['avatar1']     ?? '',
			'avatar2'     => $opts['avatar2']     ?? '',
		) );

		// Brand colors as CSS variables
		$css_vars = ':root{'
			. (! empty($opts['brand_primary']) ? '--roibot-primary:' . $opts['brand_primary'] . ';' : '')
			. (! empty($opts['brand_accent'])  ? '--roibot-accent:'  . $opts['brand_accent']  . ';' : '')
			. (! empty($opts['brand_bg'])      ? '--roibot-header-bg:' . $opts['brand_bg']    . ';' : '')
			. (! empty($opts['brand_text'])    ? '--roibot-text:'    . $opts['brand_text']    . ';' : '')
			. '}';
		wp_add_inline_style( $this->plugin_name, $css_vars );


		// wp_enqueue_script( 'firebase-app', 'https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js', array(), null, false );
        // wp_enqueue_script( 'firebase-database', 'https://www.gstatic.com/firebasejs/10.12.2/firebase-database-compat.js', array(), null, false );
        // wp_enqueue_script( 'firebase-storage', 'https://www.gstatic.com/firebasejs/10.12.2/firebase-storage-compat.js', array(), null, false );
		// wp_enqueue_script( 'firebase-auth', 'https://cdnjs.cloudflare.com/ajax/libs/firebase/10.12.2/firebase-auth-compat.js', array(), null, false );
		//wp_enqueue_script( 'firebase-stor', 'https://cdnjs.cloudflare.com/ajax/libs/firebase/10.12.2/firebase-compat.min.js', array(), null, false );
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/roibot-public.js', array(), $this->version, true );
		$opts = get_option( 'roibot_settings', array() );
		$news_items = ! empty( $opts['news_items'] ) ? $opts['news_items'] : array();
		wp_localize_script( $this->plugin_name, 'roibotNewsItems', $news_items );


	}

	public function render_roibot() {
		$options = get_option( 'roibot_settings', array() );
		ob_start();
		include plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/roibot-public-display.php';
		return ob_get_clean();
	}

}
