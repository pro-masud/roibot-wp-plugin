<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://www.roiforpros.com/r/
 * @since             1.0.0
 * @package           Roibot
 *
 * @wordpress-plugin
 * Plugin Name:       Roibot
 * Plugin URI:        https://https://www.roiforpros.com/r/
 * Description:       This is ROI chatbot
 * Version:           1.0.0
 * Author:            Frank
 * Author URI:        https://https://www.roiforpros.com/r//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       roibot
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ROIBOT_VERSION', '1.0.0' );

// After the plugin header:
if ( ! defined('ROIBOT_PLUGIN_FILE') ) define('ROIBOT_PLUGIN_FILE', __FILE__);
if ( ! defined('ROIBOT_PLUGIN_URL') )  define('ROIBOT_PLUGIN_URL', plugin_dir_url(__FILE__));
if ( ! defined('ROIBOT_PLUGIN_PATH') ) define('ROIBOT_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-roibot-activator.php
 */
function activate_roibot() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-roibot-activator.php';
	Roibot_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-roibot-deactivator.php
 */
function deactivate_roibot() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-roibot-deactivator.php';
	Roibot_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_roibot' );
register_deactivation_hook( __FILE__, 'deactivate_roibot' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-roibot.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_roibot() {

	$plugin = new Roibot();
	$plugin->run();

}
run_roibot();


/**
 * Render Roibot site-wide in footer if enabled in settings.
 */
add_action( 'wp_footer', function () {
    $opts = get_option( 'roibot_settings', array() );
    if ( ! empty( $opts['sitewide_enable'] ) ) {
        echo do_shortcode( '[roibot]' );
    }
}, 100 );

// Load BNI Chatbot (Gemini) integration
require_once plugin_dir_path( __FILE__ ) . 'includes/bni-chatbot/loader-bni-chatbot-gemini.php';
