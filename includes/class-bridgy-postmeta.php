<?php
// Adds Post Meta Box for Bridgy Publish

// The Bridgy_Postmeta class sets up a post meta box to publish using Bridgy
class Bridgy_Postmeta {
	public static function init() {
		// Add meta box to new post/post pages only
		add_action( 'load-post.php', array( 'Bridgy_Postmeta', 'bridgybox_setup' ) );
		add_action( 'load-post-new.php', array( 'Bridgy_Postmeta', 'bridgybox_setup' ) );

		add_action( 'save_post', array( 'Bridgy_Postmeta', 'save_post' ), 8, 2 );
		add_action( 'publish_post', array( 'Bridgy_Postmeta', 'publish_post' ) );
		add_action( 'do_bridgy', array( 'Bridgy_Postmeta', 'send_bridgy' ) );

		add_filter( 'the_content', array( 'Bridgy_Postmeta', 'the_content' ) );
		// Syndication Link Backcompat
		add_filter( 'syn_add_links', array( 'Bridgy_Postmeta', 'syn_add_links' ) );
		// Micropub Syndication Targets
		add_filter( 'micropub_syndicate-to', array( 'Bridgy_Postmeta', 'syndicate_to' ), 10, 2 );

		$args = array(
			'sanitize_callback' => 'sanitize_text_field',
			'type' => 'string',
			'description' => 'Brid.gy Disable Backlink',
			'single' => true,
			'show_in_rest' => false,
		);
		register_meta( 'post', '_bridgy_backlink', $args );

		$args = array(
			'sanitize_callback' => 'sanitize_text_field',
			'type' => 'string',
			'description' => 'Brid.gy Post to Twitter',
			'single' => true,
			'show_in_rest' => false,
		);
		register_meta( 'post', '_bridgy_twitter', $args );

		$args = array(
			'sanitize_callback' => 'sanitize_text_field',
			'type' => 'string',
			'description' => 'Brid.gy Post to Facebook',
			'single' => true,
			'show_in_rest' => false,
		);
		register_meta( 'post', '_bridgy_facebook', $args );

		$args = array(
			'sanitize_callback' => 'sanitize_text_field',
			'type' => 'string',
			'description' => 'Brid.gy Post to Flickr',
			'single' => true,
			'show_in_rest' => false,
		);
		register_meta( 'post', '_bridgy_flickr', $args );

	}

	/* Meta box setup function. */
	public static function bridgybox_setup() {
		/* Add meta boxes on the 'add_meta_boxes' hook. */
		add_action( 'add_meta_boxes', array( 'Bridgy_Postmeta', 'add_postmeta_boxes' ) );
	}

	/* Create one or more meta boxes to be displayed on the post editor screen. */
	public static function add_postmeta_boxes() {
		add_meta_box(
			'bridgybox-meta',      // Unique ID
			esc_html__( 'Bridgy Publish To', 'bridgy-publish' ),    // Title
			array( 'Bridgy_Postmeta', 'metabox' ),   // Callback function
			'post',         // Admin page (or post type)
			'side',         // Context
			'default'         // Priority
		);
	}

	public static function bridgy_checkboxes( $post_ID ) {
		$services = Bridgy_Config::service_options();
		$string = '<ul>';
		foreach ( $services as $key => $value ) {
			$service = get_option( 'bridgy_' . $key );
			if ( '' === $service ) {
				continue;
			}
			$meta = get_post_meta( $post_ID, '_bridgy_' . $key, true );
			$string .= '<li>';
			$string .= '<input type="hidden" name="bridgy_' . $key . '" value="no" />';
			$string .= '<input type="checkbox" name="bridgy_' . $key . '"';
			$string .= ' value="yes"';

			if ( ! $meta ) {
				$string .= checked( $service, 'checked', false );
			} else {
				$string .= checked( $meta, 'yes', false );
			}

			$string .= ' />';
			$string .= '<label for="bridgy_' . $key . '">' . $value . '</label>';
			$string .= '</li>';

		}
		$string .= '</ul>';
		return $string;
	}

	public static function bridgy_backlink( $post_ID ) {
		$bridgy_backlink_meta = get_post_meta( $post_ID, '_bridgy_backlink', true );
		$default = get_option( 'bridgy_backlink' );
		if ( ! $bridgy_backlink_meta ) {
			$bridgy_backlink_meta = $default;
		}
		$string = '<label for="bridgy_backlink_option">' . _x( 'Omit Back Link','bridgy-publish' ) . '</label>';
		$bridgy_backlink_options = Bridgy_Config::backlink_options();

		$string .= '<select name="bridgy_backlink" id="bridgy_backlink">';
		foreach ( $bridgy_backlink_options as $key => $value ) {
			$string .= '<option value="' . $key . '" ' . ( $bridgy_backlink_meta === $key ? 'selected>' : '>' )  . $value . '</option>';
		}
		$string .= '</select>';
		return $string;
	}

	public static function metabox( $object, $box ) {
		wp_nonce_field( 'bridgy_metabox', 'bridgy_metabox_nonce' );

		echo self::bridgy_checkboxes( $object->ID );

		echo self::bridgy_backlink( $object->ID );
	}

	/* Save the meta box's post metadata. */
	public static function save_post( $post_id, $post ) {
		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */
		// Check if our nonce is set.
		if ( ! isset( $_POST['bridgy_metabox_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['bridgy_metabox_nonce'], 'bridgy_metabox' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}
		$services = Bridgy_Config::service_options();
		/* OK, its safe for us to save the data now. */
		foreach ( $services as $key => $value ) {
			if ( isset( $_POST[ 'bridgy_'.$key ] ) ) {
				update_post_meta( $post_id, '_bridgy_' . $key, $_POST[ 'bridgy_'.$key ] );

			}
		}

		if ( isset( $_POST['bridgy_backlink'] ) ) {
			if ( ! empty( $_POST['bridgy_backlink'] ) ) {
				update_post_meta( $post_id,'_bridgy_backlink', $_POST['bridgy_backlink'] );
			} else {
				delete_post_meta( $post_id, '_bridgy_backlink' );
			}
		}
	}

	public static function send_webmention( $url, $key ) {
		$response = send_webmention( $url, 'https://www.brid.gy/publish/' . $key );
		$response_code = wp_remote_retrieve_response_code( $response );
		$json = json_decode( $response['body'] );
		if ( 201 === $response_code ) {
			return $json->url;
		}
		if ( (400 === $response_code)||(500 === $response_code) ) {
			return new WP_Error( 'bridgy_publish_error', $json->error, array( 'status' => 400, 'data' => $json ) );
		}
		return new WP_Error( 'bridgy_publish_error' , __( 'Unknown Bridgy Publish Error' ), array( 'status' => $response_code, 'data' => $json ) );
		;
	}

	public static function str_prefix( $source, $prefix ) {
		return strncmp( $source, $prefix, strlen( $prefix ) ) === 0;
	}

	public static function services( $post_id ) {
		$services = array(
			get_post_meta( $post_id, '_bridgy_twitter', true ) === 'yes' ? 'twitter' : null,
			get_post_meta( $post_id, '_bridgy_facebook', true ) === 'yes' ? 'facebook' : null,
			get_post_meta( $post_id, '_bridgy_flickr', true ) === 'yes' ? 'flickr' : null,
		);
		$syndicates = get_post_meta( $post_id, 'mf2_syndicate-to' );
		if ( is_array( $syndicates ) ) {
			foreach ( $syndicates as $syndicate ) {
				if ( self::str_prefix( $syndicate, 'bridgy_' ) ) {
					$services[] = str_replace( 'bridgy_', '', $syndicate );
				}
			}
		}
		return array_filter( $services );
	}

	public static function publish_post( $post_id ) {
		wp_schedule_single_event( time() + wp_rand( 5, 10 ), 'do_bridgy', array( $post_id ) );
	}

	public static function send_bridgy( $post_id ) {
		$services = self::services( $post_id );

		 /* OK, its safe for us to save the data now. */

		if ( '1' === get_option( 'bridgy_shortlinks' ) ) {
			$url = wp_get_shortlink( $post_id );
		} else {
			$url = get_permalink( $post_id );
		}
		$returns = array();
		if ( ! empty( $services ) ) {
			foreach ( $services as $service ) {
				$response = self::send_webmention( $url, $service );
				if ( ! is_wp_error( $response ) ) {
					$returns[] = $response;
				} else {
					$data = $response->get_error_data();
					error_log( 'Bridgy Publish Error(' . $data['status'] . '): ' . $response->get_error_message() );
				}
			}
			if ( empty( $returns ) ) {
				return;
			}
			if ( WP_DEBUG ) {
				error_log( 'Bridgy Publish Debug: ' . serialize( $returns ) );
			}
			$syn = get_post_meta( $post_id, 'mf2_syndication' );
			if ( ! $syn ) {
				add_post_meta( $post_id, 'mf2_syndication', $returns );
				return;
			}

			$syn = array_merge( $returns, $syn );
			$syn = array_unique( array_filter( $syn ) );
			if ( ! empty( $syn ) ) {
				update_post_meta( $post_id, 'mf2_syndication', $syn );
			}
		}
	}

	public static function the_content($content) {
		$publish = '';
		$classes = array();
		$ID = get_the_ID();

		if ( 1 === get_option( 'bridgy_ignoreformatting' ) ) {
			$classes[] = 'u-bridgy-ignore-formatting';
		}
		$class = implode( ' ', $classes );
		$link = '<a class="%1$s" href="https://www.brid.gy/publish/%2$s"></a>';

		$services = array(
			get_post_meta( $ID, '_bridgy_twitter', true ) ? 'twitter' : null,
			get_post_meta( $ID, '_bridgy_facebook', true ) ? 'facebook' : null,
			get_post_meta( $ID, '_bridgy_flickr', true ) ? 'flickr' : null,
		);
		$services = array_unique( $services );

		foreach ( $services as $service ) {
			$publish .= sprintf( $link, $class, $service );
		}

		$backlink_option = get_post_meta( get_the_ID(), '_bridgy_backlink_options', true );
		if ( '' !== $backlink_option ) {
			$publish .= '<data class="p-bridgy-omit-link" value="' . $backlink_option . '"></data>';
		}
		return $content . $publish;
	}

	public static function syndicate_to( $targets, $user_id ) {
		$services = Bridgy_Config::service_options();
		foreach ( $services as $key => $value ) {
			if ( get_option( 'bridgy_' . $key ) ) {
				$targets[] = array(
					'uid' => 'bridgy_'. $key,
					'name' => sprintf( __( '%1$s via Bridgy', 'bridgy-publish' ), $value ),
				);
			}
		}
		return $targets;
	}

	public static function syn_add_links($urls) {
		$bridgy = get_post_meta( get_the_ID(), 'bridgy_syndication' );
		if ( ! $bridgy ) {
			return $urls;
		}
		if ( is_string( $bridgy ) ) {
			$bridgy = explode( "\n", $bridgy );
		}
		$syn = get_post_meta( get_the_ID(), 'mf2_syndication' );
		if ( ! $syn ) {
			$syn = $bridgy;
		} else {
			$syn = array_merge( $syn, $bridgy );
			$syn = array_unique( $syn );
		}
		update_post_meta( get_the_ID(), 'mf2_syndication', $syn );
		delete_post_meta( get_the_ID(), 'bridgy_syndication' );
		return $urls;
	}

} // End Class
?>
