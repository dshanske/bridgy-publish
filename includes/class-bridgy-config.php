<?php

// The Bridgy_Config class sets up the Settings Page for the plugin
class Bridgy_Config {


	public static function init() {
		register_setting( 'bridgy_options', 'bridgy_options' );
		register_setting(
			'bridgy-options', // settings page
			'bridgy_backlink', // option name
			array(
				'type' => 'string',
				'description' => 'Brid.gy Back Link',
				'show_in_rest' => true,
				'default' => '',
			)
		);
		register_setting(
			'bridgy-options', // settings page
			'bridgy_twitter', // option name
			array(
				'type' => 'string',
				'description' => 'Brid.gy Twitter Option',
				'show_in_rest' => true,
				'default' => 'true',
			)
		);

		register_setting(
			'bridgy-options', // settings page
			'bridgy_facebook', // option name
			array(
				'type' => 'string',
				'description' => 'Brid.gy Facebook Option',
				'show_in_rest' => true,
				'default' => '',
			)
		);

		register_setting(
			'bridgy-options', // settings page
			'bridgy_flickr', // option name
			array(
				'type' => 'string',
				'description' => 'Brid.gy Flicker Option',
				'show_in_rest' => true,
				'default' => '',
			)
		);

		register_setting(
			'bridgy-options', // settings page
			'bridgy_ignoreformatting', // option name
			array(
				'type' => 'boolean',
				'description' => 'Brid.gy Ignore Formatting in Plaintext',
				'show_in_rest' => true,
				'default' => '0',
			)
		);

		register_setting(
			'bridgy-options', // settings page
			'bridgy_backlink', // option name
			array(
				'type' => 'string',
				'description' => 'Brid.gy Disable Backlinks',
				'show_in_rest' => true,
				'default' => '',
			)
		);

		register_setting(
			'bridgy-options', // settings page
			'bridgy_shortlinks', // option name
			array(
				'type' => 'boolean',
				'description' => 'Brid.gy Enable Shortlinks',
				'show_in_rest' => true,
				'default' => '0',
			)
		);

		register_setting(
			'bridgy-options', // settings page
			'bridgy_twitterexcerpt', // option name
			array(
				'type' => 'boolean',
				'description' => 'Set Twitter Post from Excerpt',
				'show_in_rest' => true,
				'default' => '0',
			)
		);

		add_settings_section(
			'bridgy-content',
			__( 'Options', 'bridgy-publish' ),
			array( 'Bridgy_Config', 'options_callback' ),
			'bridgy-options'
		);

		add_settings_field(
			'bridgy_twitter',
			__( 'Enable Twitter Option',
			'bridgy-publish' ),
			array( 'Bridgy_Config', 'radio_callback' ),
			'bridgy-options',
			'bridgy-content' ,
			array(
				'name' => 'bridgy_twitter',
				'list' => self::syndication_options(),
			)
		);

		add_settings_field(
			'bridgy_facebook',
			__( 'Enable Facebook Option', 'bridgy-publish' ),
			array( 'Bridgy_Config', 'radio_callback' ),
			'bridgy-options',
			'bridgy-content' ,
			array(
				'name' => 'bridgy_facebook',
				'list' => self::syndication_options(),
			)
		);
		add_settings_field(
			'bridgy_flickr',
			__( 'Enable Flickr Option', 'bridgy-publish' ),
			array( 'Bridgy_Config', 'radio_callback' ),
			'bridgy-options',
			'bridgy-content' ,
			array(
				'name' => 'bridgy_flickr',
				'list' => self::syndication_options(),
			)
		);

		add_settings_field(
			'omitlink',
			__( 'Link Back to Post', 'bridgy-publish' ),
			array( 'Bridgy_Config', 'radio_callback' ),
			'bridgy-options',
			'bridgy-content' ,
			array(
				'name' => 'bridgy_backlink',
				'list' => self::backlink_options(),
			)
		);

		add_settings_field(
			'ignoreformatting',
			__( 'Disable plain text whitespace and formatting', 'bridgy-publish' ),
			array( 'Bridgy_Config', 'checkbox_callback' ),
			'bridgy-options',
			'bridgy-content' ,
			array(
				'name' => 'bridgy_ignoreformatting',
			)
		);

		add_settings_field(
			'shortlinks',
			__( 'Send Shortlinks instead of Full URL', 'bridgy-publish' ),
			array( 'Bridgy_Config', 'checkbox_callback' ),
			'bridgy-options',
			'bridgy-content' ,
			array(
				'name' => 'bridgy_shortlinks',
			)
		);

		add_settings_field(
			'twitterexcerpt',
			__( 'Set Twitter from Post Excerpt', 'bridgy-publish' ),
			array( 'Bridgy_Config', 'checkbox_callback' ),
			'bridgy-options',
			'bridgy-content',
			array(
				'name' => 'bridgy_twitterexcerpt',
			)
		);

	}

	public static function admin_menu() {
		// If the IndieWeb Plugin is installed use its menu.
		if ( class_exists( 'IndieWeb_Plugin' ) ) {
			add_submenu_page(
				'indieweb',
				__( 'Bridgy Publish', 'bridgy-publish' ),
				__( 'Bridgy Publish', 'bridgy-publish' ),
				'manage_options',
				'bridgy_options',
				array( 'Bridgy_Config', 'options_form' )
			);
		} else {
			add_options_page(
				'',
				__( 'Bridgy Publish', 'bridgy-publish' ),
				'manage_options',
				'bridgy_options',
				array( 'Bridgy_Config', 'options_form' )
			);
		}
	}

	public static function settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=bridgy_options">Settings</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	public static function options_callback() {
		_e( '', 'bridgy-publish' );
	}

	public static function checkbox_callback( array $args ) {
		$name = $args['name'];
		$checked = get_option( $name );
		echo "<input name='" . $name . "' type='hidden' value=0 />";
		echo "<input name='" . $name . "' type='checkbox' value=1 " . checked( 1, $checked, false ) . ' /> ';
	}

	public static function syndication_options() {
		return array(
			'' => _x( 'Disable', 'bridgy-publish' ),
			'true' => _x( 'Enabled', 'bridgy-publish' ),
			'checked'  => _x( 'Checked by Default in Add Post', 'bridgy-publish' ),
		);
	}

	public static function backlink_options() {
		return array(
			'' => _x( 'Show', 'bridgy-publish' ),
			'true' => _x( 'Hide', 'bridgy-publish' ),
			'maybe'  => _x( 'Maybe', 'bridgy-publish' ),
		);
	}

	public static function service_options() {
		return array(
			'twitter' => _x( 'Twitter', 'bridgy-publish' ),
			'facebook' => _x( 'Facebook', 'bridgy-publish' ),
			'flickr' => _x( 'Flickr', 'bridgy-publish' ),
		);
	}

	public static function select_callback( array $args ) {
		$name = $args['name'];
		$select = get_option( $name );
		$options = $args['list'];
		echo "<select name='" . $name . "id='" . $name . "'>";
		foreach ( $options as $key => $value ) {
			echo '<option value="' . $key . '" ' . ( $select === $key ? 'selected>' : '>' ) . $value . '</option>';
		}
		echo '</select>';
	}

	public static function radio_callback( array $args ) {
		$name = $args['name'];
		$select = get_option( $name );
		$options = $args['list'];
		echo '<fieldset>';
		foreach ( $options as $key => $value ) {
			echo '<input type="radio" name="' . $name . '" id="' . $name . '" value="' . $key . '" ' . checked( $key, $select, false ) . ' />';
			echo '<label for="' . $args['name'] . '">' . $value . '</label>';

			echo '<br />';
		}
		echo '</fieldset>';
	}

	public static function options_form() {
		echo '<div class="wrap">';

		echo '<h2>' . __( 'Bridgy Publish', 'bridgy-publish' ) . '</h2>';
		echo '<p>';
		esc_html_e( 'Adds support for publishing through Bridgy', 'bridgy-publish' );
		echo '</p><hr />';
		echo '<form method="post" action="options.php">';
		settings_fields( 'bridgy-options' );
		do_settings_sections( 'bridgy-options' );
		submit_button();
		echo '</form></div>';
	}

} // End Class

?>
