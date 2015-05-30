<?php

add_action( 'admin_init' , array('bridgy_config', 'admin_init') );
add_action('admin_menu', array('bridgy_config', 'admin_menu') );


// The bridgy_config class sets up the Settings Page for the plugin
class bridgy_config {

	public static function admin_init() {
		$options = get_option('bridgy_options');
		register_setting( 'bridgy_options', 'bridgy_options' );
		add_settings_section( 'bridgy-content', __('Options', 'Bridgy Publish')  , array('bridgy_config', 'options_callback'), 'bridgy_options' );
		add_settings_field( 'twitter', __('Enable Twitter Option', 'Bridgy Publish'), array('bridgy_config', 'checkbox_callback'), 'bridgy_options', 'bridgy-content' ,  array( 'name' => 'twitter') );
		add_settings_field( 'facebook', __('Enable Facebook Option', 'Bridgy Publish'), array('bridgy_config', 'checkbox_callback'), 'bridgy_options', 'bridgy-content' ,  array( 'name' => 'facebook') );
    add_settings_field( 'instagram', __('Enable Instagram Option', 'Bridgy Publish'), array('bridgy_config', 'checkbox_callback'), 'bridgy_options', 'bridgy-content' ,  array( 'name' => 'instagram') );
    add_settings_field( 'omitlink', __('Disable Link Back to Post', 'Bridgy Publish'), array('bridgy_config', 'checkbox_callback'), 'bridgy_options', 'bridgy-content' ,  array( 'name' => 'omitlink') );
    add_settings_field( 'ignoreformatting', __('Ignore Formatting', 'Bridgy Publish'), array('bridgy_config', 'checkbox_callback'), 'bridgy_options', 'bridgy-content' ,  array( 'name' => 'ignoreformatting') );

    add_settings_field( 'shortlinks', __('Send Shortlinks insted of Full URL', 'Bridgy Publish'), array('bridgy_config', 'checkbox_callback'), 'bridgy_options', 'bridgy-content' ,  array( 'name' => 'shortlinks') );

	}

	public static function admin_menu() {
		add_options_page( '', __('Bridgy Publish', 'Bridgy Publish'), 'manage_options', 'bridgy_options', array('bridgy_config', 'options_form') );
	}

	public static function settings_link($links) {
		$settings_link = '<a href="options-general.php?page=bridgy_options">Settings</a>';
		array_unshift($links, $settings_link);
 		return $links;
	}

	public static function options_callback() {
		_e ('', 'Bridgy Publish');
	}

	public static function checkbox_callback(array $args) {
		$options = get_option('bridgy_options');
		$name = $args['name'];
		$checked = $options[$name];
		echo "<input name='bridgy_options[$name]' type='hidden' value='0' />";
		echo "<input name='bridgy_options[$name]' type='checkbox' value='1' " . checked( 1, $checked, false ) . " /> ";
}

	public static function options_form() {
		kind_taxonomy::kind_defaultterms ();
		echo '<div class="wrap">';
		echo '<h2>' . __('Bridgy Publish', 'Bridgy Publish') . '</h2>';
		echo '<p>'; 
		esc_html_e( 'Adds support for publishing through Bridgy', 'Bridgy Publish');
		echo '</p><hr />';
		echo '<form method="post" action="options.php">';
			settings_fields( 'bridgy_options' );
    	do_settings_sections( 'bridgy_options' );
    	submit_button();
    echo '</form></div>';
	}

} // End Class

?>
