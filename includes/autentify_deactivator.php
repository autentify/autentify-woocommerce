<?php

register_deactivation_hook( AUTENTIFY__FILE__, 'autentify_delete_options' );

/**
 * Deletes the autentify_api_token and autentify_auto_order_check options.
 *
 * @since 2.0.0
 *
 * @return void
 */
function autentify_delete_options() {
	delete_option( "autentify_api_token" );
	delete_option( "autentify_auto_order_check" );
}