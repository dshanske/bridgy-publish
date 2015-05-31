<?php
/**
 * Plugin Name: Bridgy Publish
 * Plugin URI: https://github.com/dshanske/bridgy-publish
 * Description: Bridgy Publish for WordPress
 * Version: 1.0.0
 * Author: David Shanske
 * Author URI: http://david.shanske.com
 * Text Domain: Microformats2, POSSE
 */

function bridgy_publish_activation() {
  if (version_compare(phpversion(), 5.3, '<')) {
    @trigger_error(__("The minimum PHP version required for this plugin is 5.3", "Bridgy Publish"), E_USER_ERROR );
  }
}

register_activation_hook(__FILE__, 'bridgy_publish_activation');

// Add a notice to the Admin Pages if the WordPress Webmentions Plugin isn't Activated
add_action( 'admin_notices', 'bridgy_plugin_notice' );
function bridgy_plugin_notice() {
	if (!class_exists("WebMentionPlugin"))
		{
			echo '<div class="error"><p>';
			echo '<a href="https://wordpress.org/plugins/webmention/">';
			esc_html_e( 'Bridgy Publish Requires the WordPress Webmention Plugin', 'Bridgy Publish' );
			echo '</a></p></div>';
		}
}

// Config Class
 require_once( plugin_dir_path( __FILE__ ) . 'includes/class-bridgy-config.php');

// Post Meta Class
 require_once( plugin_dir_path( __FILE__ ) . 'includes/class-bridgy-postmeta.php');
