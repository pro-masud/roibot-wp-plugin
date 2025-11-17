<?php
/**
 * Loader for BNI Chatbot (Gemini) inside ROIBOT plugin.
 * - Includes the provided single-file class (unchanged)
 * - Instantiates it
 * - Moves settings page under Roibot menu only (removes from Settings)
 */

// Include class
require_once plugin_dir_path( __FILE__ ) . 'class-bni-chatbot-gemini.php';

// Instantiate (the class hooks everything itself)
global $bni_chatbot_gemini_instance;
if ( ! isset( $bni_chatbot_gemini_instance ) ) {
	$bni_chatbot_gemini_instance = new BNI_Chatbot_Gemini_SingleFile_V1();
}

// Remove the original Settings->BNI Chatbot (Gemini) menu hook that the class added
remove_action( 'admin_menu', array( $bni_chatbot_gemini_instance, 'settings_menu' ), 10 );

// Re-add under the "Roibot" top-level menu only
add_action( 'admin_menu', function () use ( $bni_chatbot_gemini_instance ) {
	// Safety: also try to remove in case it exists already
	remove_submenu_page( 'options-general.php', 'bni-chatbot-gemini' );

	add_submenu_page(
		'roibot',                          // parent slug from Roibot
		'BNI Chatbot (Gemini)',            // page title
		'BNI Chatbot (Gemini)',            // menu title
		'manage_options',                  // capability
		'bni-chatbot-gemini',              // menu slug (reuse same slug)
		function() use ( $bni_chatbot_gemini_instance ) {
			if ( $bni_chatbot_gemini_instance && method_exists( $bni_chatbot_gemini_instance, 'settings_page_html' ) ) {
				$bni_chatbot_gemini_instance->settings_page_html();
			}
		},
		10
	);
}, 999 );
