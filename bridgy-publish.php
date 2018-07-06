<?php
/**
 * Plugin Name: Bridgy
 * Plugin URI: https://github.com/dshanske/bridgy-publish
 * Description: Bridgy pulls comments, likes, and reshares on social networks back to your web site. You can also use it to post to social networks - or comment, like, reshare, or even RSVP - from your own web site.
 * Version: 1.4.2
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
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bridgy-config.php';
	new Bridgy_Config();

	// Post Meta Class
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bridgy-postmeta.php';
	new Bridgy_Postmeta();

}
