<?php
/**
 * Plugin Name: Bridgy Publish
 * Plugin URI: https://github.com/dshanske/bridgy-publish
 * Description: Bridgy Publish for WordPress
 * Version: 1.2.0
 * Author: David Shanske
 * Author URI: http://david.shanske.com
 * Text Domain: bridgy-publish
 */

// Add a notice to the Admin Pages if the WordPress Webmentions Plugin isn't Activated
add_action( 'admin_notices', 'bridgy_plugin_notice' );
function bridgy_plugin_notice() {
	if ( ! function_exists( 'send_webmention' ) ) {
		echo '<div class="error"><p>';
		echo '<a href="https://wordpress.org/plugins/webmention/">';
		esc_html_e( 'Bridgy Publish Requires a Webmention Plugin', 'bridgy-publish' );
		echo '</a></p></div>';
	}
}

add_action( 'plugins_loaded', 'bridgy_plugin_init' );

function bridgy_plugin_init() {
	load_plugin_textdomain( 'bridgy-publish', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	// Config Class
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-bridgy-config.php' );
	add_action( 'admin_init', array( 'Bridgy_Config', 'init' ) );
	add_action( 'admin_menu', array( 'Bridgy_Config', 'admin_menu' ), 13 );

	// Post Meta Class
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-bridgy-postmeta.php' );
	add_action( 'init' , array( 'Bridgy_Postmeta', 'init' ) );

}
