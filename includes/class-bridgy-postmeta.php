<?php
// Adds Post Meta Box for Bridgy Publish

// The Bridgy_Postmeta class sets up a post meta box to publish using Bridgy
class Bridgy_Postmeta {
	public static function init() {
		// Add meta box to new post/post pages only
		add_action( 'load-post.php', array( 'Bridgy_Postmeta', 'bridgybox_setup' ) );
		add_action( 'load-post-new.php', array( 'Bridgy_Postmeta', 'bridgybox_setup' ) );

		add_action( 'save_post', array( 'Bridgy_Postmeta', 'save_post' ), 8, 3 );
		add_action( 'save_post', array( 'Bridgy_Postmeta', 'publish_post' ), 99, 3 );

		add_action( 'wp_footer', array( 'Bridgy_Postmeta', 'wp_footer' ) );
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
		//	'sanitize_callback' => '',
			'type' => 'array',
			'description' => 'Syndicate To',
			'single' => false,
			'show_in_rest' => false,
		);
		register_meta( 'post', 'mf2_syndicate-to', $args );

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
		$meta = get_post_meta( $post_ID, 'mf2_syndicate-to', true );
		foreach ( $services as $key => $value ) {
			$service = get_option( 'bridgy_' . $key );
			if ( '' === $service ) {
				continue;
			}
			$string .= '<li>';
			$string .= '<input type="checkbox" name="mf2_syndicate-to[]"';
			$string .= ' value="bridgy-publish_' . $key . '"';
			if ( empty( $meta ) ) {
				$string .= checked( $service, 'checked', false );
			} else {
				$string .= in_array( 'bridgy-publish_' . $key , $meta ) ? ' checked' : '';
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
	public static function save_post( $post_id, $post, $update ) {
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

		/* OK, its safe for us to save the data now. */
		if ( isset( $_POST['mf2_syndicate-to'] ) ) {
			update_post_meta( $post_id, 'mf2_syndicate-to', array_map( 'sanitize_text_field', $_POST['mf2_syndicate-to'] ) );
		} else {
			update_post_meta( $post_id, 'mf2_syndicate-to', array( 'none' ) );
		}

		if ( isset( $_POST['bridgy_backlink'] ) ) {
			if ( ! empty( $_POST['bridgy_backlink'] ) ) {
				update_post_meta( $post_id,'_bridgy_backlink', sanitize_text_field( $_POST['bridgy_backlink'] ) );
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
	}

	public static function str_prefix( $source, $prefix ) {
		if ( ! is_string( $source ) || ! is_string( $prefix ) ) {
			return false;
		}
		return strncmp( $source, $prefix, strlen( $prefix ) ) === 0;
	}

	public static function services( $post_id ) {
		$metas = get_post_meta( $post_id, 'mf2_syndicate-to', true );
		if ( ! $metas ) {
			return array();
		}
		if ( is_array( $metas[0] ) ) {
			$metas = $metas[0];
		}
		$services = array();
		foreach ( $metas as $meta ) {
			if ( self::str_prefix( $meta, 'bridgy-publish_' ) ) {
				$services[] = str_replace( 'bridgy-publish_', '', $meta );
			}
		}
		return array_filter( $services );
	}

	public static function publish_post( $post_id, $post, $update ) {
		if ( 'publish' === $post->post_status ) {
			self::send_bridgy( $post_id );
		}
	}

	public static function send_bridgy( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}
		$services = self::services( $post_id );
		if ( ! $services ) {
			return;
		}

		 /* OK, its safe for us to save the data now. */
		$url = '';
		if ( '1' == get_option( 'bridgy_shortlinks' ) ) {
			$url = wp_get_shortlink( $post_id );
		}
		if ( empty( $url ) ) {
			$url = get_permalink( $post_id );
		}
		$returns = array();
		$errors = array();
		foreach ( $services as $service ) {
			$response = self::send_webmention( $url, $service );
			if ( ! is_wp_error( $response ) ) {
				$returns[] = $response;
			} else {
				$errors[] = $response;
				error_log( 'Error: ' . $response->get_error_message() );
			}
		}
		$syn = get_post_meta( $post_id, 'mf2_syndication' );

		if ( ! empty( $returns ) ) {
			if ( ! $syn ) {
				add_post_meta( $post_id, 'mf2_syndication', $returns );
			} else {
				$returns = array_merge( $returns, $syn );
				$returns = array_unique( array_filter( $returns ) );
			}
			update_post_meta( $post_id, 'mf2_syndication', $returns );
		}
		if ( ! empty( $errors ) ) {
		}
	}

	public static function wp_footer() {
		$classes = array();

		if ( '1' === get_option( 'bridgy_ignoreformatting' ) ) {
			$classes[] = 'u-bridgy-ignore-formatting';
		}
		$class = implode( ' ', $classes );
		$link = '<a class="%1$s" href="https://www.brid.gy/publish/%2$s"></a>';

		$services = self::services( get_the_ID() );

		foreach ( $services as $service ) {
			printf( $link, $class, $service );
		}

		if ( ! $backlink = get_post_meta( get_the_ID(), '_bridgy_backlink', true ) ) {
			$backlink = get_option( 'bridgy_backlink' );
		}
		if ( '' !== $backlink ) {
			echo '<data class="p-bridgy-omit-link" value="' . $backlink . '"></data>';
		}
		if ( ( '1' === get_option( 'bridgy_twitterexcerpt' ) ) && has_excerpt() ) {
			echo '<p="p-bridgy-twitter-content" style="display:none"' . get_the_excerpt() . '</p>';
		}
	}

	public static function syndicate_to( $targets, $user_id ) {
		$services = Bridgy_Config::service_options();
		foreach ( $services as $key => $value ) {
			if ( get_option( 'bridgy_' . $key ) ) {
				$targets[] = array(
					'uid' => 'bridgy-publish_'. $key,
					'name' => sprintf( __( '%1$s via Bridgy Publish', 'bridgy-publish' ), $value ),
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
