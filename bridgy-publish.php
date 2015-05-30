<?php
/**
 * Plugin Name: Bridgy Publish
 * Plugin URI: https://github.com/dshanske/bridgy-publish
 * Description: Bridgy Publish for WordPress
 * Version: 0.1.0
 * Author: David Shanske
 * Author URI: http://david.shanske.com
 * Text Domain: Microformats2, POSSE
 */

function bridgy_publish_activation() {
  if (version_compare(phpversion(), 5.3, '<')) {
    die("The minimum PHP version required for this plugin is 5.3");
  }
}

register_activation_hook(__FILE__, 'bridgy_publish_activation');

// Config Class
 require_once( plugin_dir_path( __FILE__ ) . 'includes/class-bridgy-config.php');

// Post Meta Class
 require_once( plugin_dir_path( __FILE__ ) . 'includes/class-bridgy-postmeta.php');
