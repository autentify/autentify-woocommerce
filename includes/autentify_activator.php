<?php

register_activation_hook( AUTENTIFY__FILE__, 'autentify_insert_options' );

/**
 * Inserts the autentify_api_token and autentify_auto_order_check options.
 *
 * @since 2.0.0
 *
 * @return void
 */
function autentify_insert_options() {
	add_option( "autentify_api_token" );
	// add_option( "autentify_auto_order_check", "true" ); // Disabled AutentiMail
}