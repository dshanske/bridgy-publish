<?php

// The Bridgy_Config class sets up the Settings Page for the plugin
class Bridgy_Config {

	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 13 );
	}

	public function init() {
		if ( ! get_option( 'bridgy_shortlinks' ) ) {
			remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
		}
		register_setting( 'bridgy_options', 'bridgy_options' );
		register_setting(
			'bridgy-options', // settings page
			'bridgy_backlink', // option name
			array(
				'type'         => 'string',
				'description'  => 'Brid.gy Back Link',
				'show_in_rest' => true,
				'default'      => '',
			)
		);
		register_setting(
			'bridgy-options', // settings page
			'bridgy_twitter', // option name
			array(
				'type'         => 'string',
				'description'  => 'Brid.gy Twitter Option',
				'show_in_rest' => true,
				'default'      => 'true',
			)
		);

		register_setting(
			'bridgy-options', // settings page
			'bridgy_facebook', // option name
			array(
				'type'         => 'string',
				'description'  => 'Brid.gy Facebook Option',
				'show_in_rest' => true,
				'default'      => '',
			)
		);

		register_setting(
			'bridgy-options', // settings page
			'bridgy_flickr', // option name
			array(
				'type'         => 'string',
				'description'  => 'Brid.gy Flicker Option',
				'show_in_rest' => true,
				'default'      => '',
			)
		);

		register_setting(
			'bridgy-options', // settings page
			'bridgy_github', // option name
			array(
				'type'         => 'string',
				'description'  => 'Brid.gy Github Option',
				'show_in_rest' => true,
				'default'      => '',
			)
		);

		register_setting(
			'bridgy-options', // settings page
			'bridgy_ignoreformatting', // option name
			array(
				'type'         => 'boolean',
				'description'  => 'Brid.gy Ignore Formatting in Plaintext',
				'show_in_rest' => true,
				'default'      => '0',
			)
		);

		register_setting(
			'bridgy-options', // settings page
			'bridgy_backlink', // option name
			array(
				'type'         => 'string',
				'description'  => 'Brid.gy Disable Backlinks',
				'show_in_rest' => true,
				'default'      => '',
			)
		);

		register_setting(
			'bridgy-options', // settings page
			'bridgy_shortlinks', // option name
			array(
				'type'         => 'boolean',
				'description'  => 'Brid.gy Enable Shortlinks',
				'show_in_rest' => true,
				'default'      => '0',
			)
		);

		register_setting(
			'bridgy-options', // settings page
			'bridgy_twitterexcerpt', // option name
			array(
				'type'         => 'boolean',
				'description'  => 'Use Post Excerpt for Tweets',
				'show_in_rest' => true,
				'default'      => '0',
			)
		);
	}

	public function admin_init() {

		add_settings_section(
			'bridgy-content',
			__( 'Options', 'bridgy-publish' ),
			array( $this, 'options_callback' ),
			'bridgy-options'
		);

		add_settings_field(
			'bridgy_twitter',
			__(
				'Publish to Twitter',
				'bridgy-publish'
			),
			array( $this, 'radio_callback' ),
			'bridgy-options',
			'bridgy-content',
			array(
				'name' => 'bridgy_twitter',
				'list' => self::syndication_options(),
			)
		);

		add_settings_field(
			'bridgy_facebook',
			__( 'Publish to Facebook', 'bridgy-publish' ),
			array( $this, 'radio_callback' ),
			'bridgy-options',
			'bridgy-content',
			array(
				'name' => 'bridgy_facebook',
				'list' => self::syndication_options(),
			)
		);

		add_settings_field(
			'bridgy_flickr',
			__( 'Publish to Flickr', 'bridgy-publish' ),
			array( $this, 'radio_callback' ),
			'bridgy-options',
			'bridgy-content',
			array(
				'name' => 'bridgy_flickr',
				'list' => self::syndication_options(),
			)
		);

		add_settings_field(
			'bridgy_github',
			__( 'Publish to Github', 'bridgy-publish' ),
			array( $this, 'radio_callback' ),
			'bridgy-options',
			'bridgy-content',
			array(
				'name' => 'bridgy_github',
				'list' => self::syndication_options(),
			)
		);

		add_settings_field(
			'omitlink',
			__( 'Link Back to Post', 'bridgy-publish' ),
			array( $this, 'radio_callback' ),
			'bridgy-options',
			'bridgy-content',
			array(
				'name' => 'bridgy_backlink',
				'list' => self::backlink_options(),
			)
		);

		add_settings_field(
			'ignoreformatting',
			__( 'Disable plain text whitespace and formatting', 'bridgy-publish' ),
			array( $this, 'checkbox_callback' ),
			'bridgy-options',
			'bridgy-content',
			array(
				'name' => 'bridgy_ignoreformatting',
			)
		);

		add_settings_field(
			'shortlinks',
			__( 'Send Shortlinks instead of Full URL', 'bridgy-publish' ),
			array( $this, 'checkbox_callback' ),
			'bridgy-options',
			'bridgy-content',
			array(
				'name' => 'bridgy_shortlinks',
			)
		);

		add_settings_field(
			'twitterexcerpt',
			__( 'Set Twitter from Post Excerpt', 'bridgy-publish' ),
			array( $this, 'checkbox_callback' ),
			'bridgy-options',
			'bridgy-content',
			array(
				'name' => 'bridgy_twitterexcerpt',
			)
		);

	}

	public function admin_menu() {
		// If the IndieWeb Plugin is installed use its menu.
		if ( class_exists( 'IndieWeb_Plugin' ) ) {
			add_submenu_page(
				'indieweb',
				__( 'Bridgy', 'bridgy-publish' ),
				__( 'Bridgy', 'bridgy-publish' ),
				'manage_options',
				'bridgy_options',
				array( $this, 'options_form' )
			);
		} else {
			add_options_page(
				'',
				__( 'Bridgy', 'bridgy-publish' ),
				'manage_options',
				'bridgy_options',
				array( $this, 'options_form' )
			);
		}
	}

	public function settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=bridgy_options">Settings</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	public function options_callback() {
		_e( 'Bridgy', 'bridgy-publish' );
	}

	public function checkbox_callback( array $args ) {
		$name    = $args['name'];
		$checked = get_option( $name );
		echo "<input name='" . $name . "' type='hidden' value=0 />";
		echo "<input name='" . $name . "' type='checkbox' value=1 " . checked( 1, $checked, false ) . ' /> ';
	}

	public function syndication_options() {
		return array(
			''        => __( 'Disable', 'bridgy-publish' ),
			'true'    => __( 'Enabled', 'bridgy-publish' ),
			'checked' => __( 'Checked by Default in Add Post', 'bridgy-publish' ),
		);
	}

	public static function backlink_options() {
		return array(
			''      => __( 'Show', 'bridgy-publish' ),
			'true'  => __( 'Hide', 'bridgy-publish' ),
			'maybe' => __( 'If Shortened', 'bridgy-publish' ),
		);
	}

	public static function service_options() {
		return array(
			'twitter'  => __( 'Twitter', 'bridgy-publish' ),
			'facebook' => __( 'Facebook', 'bridgy-publish' ),
			'flickr'   => __( 'Flickr', 'bridgy-publish' ),
			'github'   => __( 'Github', 'bridgy-publish' ),
		);
	}

	public function select_callback( array $args ) {
		$name    = $args['name'];
		$select  = get_option( $name );
		$options = $args['list'];
		echo "<select name='" . $name . "id='" . $name . "'>";
		foreach ( $options as $key => $value ) {
			echo '<option value="' . $key . '" ' . ( $select === $key ? 'selected>' : '>' ) . $value . '</option>';
		}
		echo '</select>';
	}

	public function radio_callback( array $args ) {
		$name    = $args['name'];
		$select  = get_option( $name );
		$options = $args['list'];
		echo '<fieldset>';
		foreach ( $options as $key => $value ) {
			echo '<input type="radio" name="' . $name . '" id="' . $name . '" value="' . $key . '" ' . checked( $key, $select, false ) . ' />';
			echo '<label for="' . $args['name'] . '">' . $value . '</label>';

			echo '<br />';
		}
		echo '</fieldset>';
	}

	public function register_form() {
		echo '</form></div>';
		echo '<h2>' . __( 'Bridgy Registration', 'bridgy-publish' ) . '</h2>';
		echo '<p>' . __( 'Register for silos through the Bridgy site', 'bridgy-publish' ) . '</p>';
		if ( ! get_user_meta( get_current_user_id(), 'bridgy-twitter' ) ) {
			$this->bridgy_form( 'twitter', __( 'Register for Twitter', 'bridgy-publish' ) );
		} else {
			echo '<a href="' . get_user_meta( get_current_user_id(), 'bridgy-twitter', true ) . '">' . __( 'Twitter User Page', 'bridgy-publish' ) . '</a>';
		}
		echo '<br />';
		if ( ! get_user_meta( get_current_user_id(), 'bridgy-facebook' ) ) {
			$this->bridgy_form( 'facebook', __( 'Register for Facebook', 'bridgy-publish' ) );
		} else {
			echo '<a href="' . get_user_meta( get_current_user_id(), 'bridgy-facebook', true ) . '">' . __( 'Facebook User Page', 'bridgy-publish' ) . '</a>';
		}
		echo '<br />';
		if ( ! get_user_meta( get_current_user_id(), 'bridgy-googleplus' ) ) {
			$this->bridgy_form( 'googleplus', __( 'Register for Google Plus', 'bridgy-publish' ) );
			echo '<br />';
		} else {
			echo '<a href="' . get_user_meta( get_current_user_id(), 'bridgy-googleplus', true ) . '">' . __( 'Google Plus User Page', 'bridgy-publish' ) . '</a>';
		}
		echo '<br />';
		if ( ! get_user_meta( get_current_user_id(), 'bridgy-instagram' ) ) {
			$this->bridgy_form( 'instagram', __( 'Register for Instagram', 'bridgy-publish' ) );
		} else {
			echo '<a href="' . get_user_meta( get_current_user_id(), 'bridgy-instagram', true ) . '">' . __( 'Instagram User Page', 'bridgy-publish' ) . '</a>';
		}
		echo '<br />';
		if ( ! get_user_meta( get_current_user_id(), 'bridgy-flickr' ) ) {
			$this->bridgy_form( 'flickr', __( 'Register for Flickr', 'bridgy-publish' ) );
		} else {
			echo '<p><a href="' . get_user_meta( get_current_user_id(), 'bridgy-flickr', true ) . '">' . __( 'Flickr User Page', 'bridgy-publish' ) . '</a> </p>';
		}
		echo '<br />';

	}

	public function bridgy_form( $service, $text, $features = array( 'listen', 'publish' ) ) {
		echo '<p>';
		echo '<form method="post" action="https://brid.gy/' . $service . '/start">';
		echo '<input class="button-secondary" type="submit" value="' . $text . '" /><br />';
		echo '<input type="hidden" name="feature" value="' . implode( ',', $features ) . '" /><br />';
		echo '<input type="hidden" name="callback" value="' . admin_url( 'admin.php?page=bridgy_options&service=' ) . $service . '" />';
		echo '<input type="hidden" name="user_url" value="' . wp_get_current_user()->user_url . '" />';
		echo '</form></p>';
	}


	public function options_form() {
		load_template( plugin_dir_path( __DIR__ ) . 'templates/settings.php' );
	}

} // End Class


