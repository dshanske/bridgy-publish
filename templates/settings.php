?>
<div class="wrap">
<h2><?php _e( 'Bridgy', 'bridgy-publish' ); ?></h2>
<p><?php  _e( 'Adds support for Bridgy. Register for Bridgy below', 'bridgy-publish' ); ?></p>
<hr />
<?php
                if ( isset( $_GET['service'] ) ) {
                        switch ( $_GET['result'] ) {
                                case 'success':
                                        update_user_meta( get_current_user_id(), 'bridgy-' . esc_attr( $_GET['service'] ), esc_url_raw( $_GET['user'] ) );
                                        echo '<h2 class="notice notice-success">' . __( 'You have successfully registered', 'bridgy-publish' ) . '</h2>';
                                        break;
                                case 'failure':
                                        delete_user_meta( get_current_user_id(), 'bridgy-' . esc_attr( $_GET['service'] ) );
                                        echo '<h2 class="notice notice-error">' . __( 'Your registration has failed', 'bridgy-publish' ) . '</h2>';
                                        break;
                                case 'declined':
                                        delete_user_meta( get_current_user_id(), 'bridgy-' . esc_attr( $_GET['service'] ) );
                                        echo '<h2 class="notice notice-warning">' . __( 'Your registration have been declined', 'bridgy-publish' ) . '</h2>';
                                        break;
                        }
                }
?>
<form method="post" action="options.php">';
<?php settings_fields( 'bridgy-options' );
do_settings_sections( 'bridgy-options' );
submit_button();
?>
