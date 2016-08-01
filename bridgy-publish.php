<?php
/**
 * Plugin Name: Bridgy Publish
 * Plugin URI: https://github.com/dshanske/bridgy-publish
 * Description: Bridgy Publish for WordPress
 * Version: 1.1.0
 * Author: David Shanske
 * Author URI: http://david.shanske.com
 * Text Domain: Microformats2, POSSE
 */

// Add a notice to the Admin Pages if the WordPress Webmentions Plugin isn't Activated
add_action( 'admin_notices', 'bridgy_plugin_notice' );
function bridgy_plugin_notice() {
	if (!function_exists("send_webmention"))
		{
			echo '<div class="error"><p>';
			echo '<a href="https://wordpress.org/plugins/webmention/">';
			esc_html_e( 'Bridgy Publish Requires a Webmention Plugin', 'Bridgy Publish' );
			echo '</a></p></div>';
		}
}

// Config Class
 require_once( plugin_dir_path( __FILE__ ) . 'includes/class-bridgy-config.php');

// Post Meta Class
 require_once( plugin_dir_path( __FILE__ ) . 'includes/class-bridgy-postmeta.php');
