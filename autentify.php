<?php
/**
 * @package Autentify
 */
/**
 * Plugin Name:       		Autentify anti fraud for WooCommerce
 * Plugin URI:        		https://autentify.com.br/
 * Description:       		Anti-fraude em tempo real para e-commerces, protegendo transações e auxiliando na tomada de decisões seguras.
 * Version:           		2.2.0
 * Requires at least: 		4.7
 * Tested up to: 					6.6.2
 * Requires PHP:      		5.6
 * WC requires at least: 	7.1
 * WC tested up to: 			8.2.3
 * Requires Plugins: 			woocommerce, woocommerce-extra-checkout-fields-for-brazil
 * Author:            		Autentify
 * Author URI:        		https://autentify.com.br/
 * License:           		GPL v3 or later
 * License URI:       		https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       		Autentify
 * Domain Path:       		/languages
 */
/*
Autentify is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.
 
Autentify is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Autentify. If not, see https://www.gnu.org/licenses/gpl-3.0.html.
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AUTENTIFY_VERSION', '2.2.0' );

define( 'AUTENTIFY__FILE__', __FILE__ );
define( 'AUTENTIFY_PLUGIN_BASE', plugin_basename( AUTENTIFY__FILE__ ) );
define( 'AUTENTIFY_PATH', plugin_dir_path( AUTENTIFY__FILE__ ) );
define( 'AUTENTIFY_URL', plugins_url( '/', AUTENTIFY__FILE__ ) );

add_action( 'plugins_loaded', 'autentify_load_plugin_textdomain' );

define( 'AUTENTIFY_ASSETS_PATH', AUTENTIFY_PATH . 'assets/' );
define( 'AUTENTIFY_ASSETS_URL', AUTENTIFY_URL . 'assets/' );

define( 'AUTENTIFY_API_TOKEN', get_option( 'autentify_api_token' ) );

add_action( 'before_woocommerce_init', 'setup_hpos_compatibility' );

require_once( AUTENTIFY_PATH . 'app/models/autentify_api.php' );
require_once( AUTENTIFY_PATH . 'app/models/autentify_auth.php' );

require_once( AUTENTIFY_PATH . 'includes/admin-menu.php');
require_once( AUTENTIFY_PATH . 'includes/autentify_activator.php' );
require_once( AUTENTIFY_PATH . 'includes/autentify_deactivator.php' );

require_once( AUTENTIFY_PATH . 'includes/autenti_commerce/controllers/autentify_autenti_commerce_controller.php' );

require_once( AUTENTIFY_PATH . 'includes/autenti_commerce/services/autentify_autenti_commerce_client.php' );
require_once( AUTENTIFY_PATH . 'includes/autenti_commerce/services/autentify_autenti_commerce_status_updater_service.php' );

require_once( AUTENTIFY_PATH . 'includes/autenti_commerce/models/autentify_autenti_commerce.php' );
require_once( AUTENTIFY_PATH . 'includes/autenti_commerce/models/autentify_autenti_commerce_order.php' );
require_once( AUTENTIFY_PATH . 'includes/autenti_commerce/models/autentify_autenti_commerce_customer.php' );
require_once( AUTENTIFY_PATH . 'includes/autenti_commerce/models/autentify_autenti_commerce_address.php' );
require_once( AUTENTIFY_PATH . 'includes/autenti_commerce/models/autentify_autenti_commerce_item.php' );
require_once( AUTENTIFY_PATH . 'includes/autenti_commerce/models/autentify_autenti_commerce_payment.php' );
require_once( AUTENTIFY_PATH . 'includes/autenti_commerce/models/autentify_autenti_commerce_phone.php' );
require_once( AUTENTIFY_PATH . 'includes/autenti_commerce/models/autentify_autenti_commerce_document.php' );

require_once( AUTENTIFY_PATH . 'includes/autenti_commerce/builders/autentify_autenti_commerce_data_builder.php' );
require_once( AUTENTIFY_PATH . 'includes/autenti_commerce/builders/autentify_autenti_commerce_order_builder.php' );
require_once( AUTENTIFY_PATH . 'includes/autenti_commerce/builders/autentify_autenti_commerce_customer_builder.php' );
require_once( AUTENTIFY_PATH . 'includes/autenti_commerce/builders/autentify_autenti_commerce_order_items_builder.php' );
require_once( AUTENTIFY_PATH . 'includes/autenti_commerce/builders/autentify_autenti_commerce_shipping_address_builder.php' );

require_once( AUTENTIFY_PATH . 'includes/autenti_commerce/helpers/autentify_autenti_commerce_helper.php' );

if ( ! version_compare( PHP_VERSION, '5.6', '>=' ) ) {
	add_action( 'admin_notices', 'autentify_fail_php_version' );
} elseif ( ! version_compare( get_bloginfo( 'version' ), '4.7', '>=' ) ) {
	add_action( 'admin_notices', 'autentify_fail_wp_version' );
} elseif ( ! defined('AUTENTIFY_API_TOKEN') || AUTENTIFY_API_TOKEN == "" ) {
	add_action( 'admin_notices', 'autentify_fail_api_token_variable' );
} else {
	require_once( AUTENTIFY_PATH . 'app/helpers/autentify_email_helper.php' );
	require_once( AUTENTIFY_PATH . 'app/helpers/autentify_cpf_helper.php' );
	require_once( AUTENTIFY_PATH . 'app/helpers/autentify_score_helper.php' );
	require_once( AUTENTIFY_PATH . 'app/helpers/autentify_autenti_mail_helper.php' );

	require_once( AUTENTIFY_PATH . 'app/models/autentify_autenti_mail.php' );

	require_once( AUTENTIFY_PATH . 'app/daos/autentify_autenti_mail_dao.php' );
	
	require_once( AUTENTIFY_PATH . 'includes/user_roles.php' );

	require_once( AUTENTIFY_PATH . 'app/actions/autentify_autenti_mail_actions.php' );
	require_once( AUTENTIFY_PATH . 'app/actions/autentify_autenti_commerce_actions.php' );

	require( AUTENTIFY_PATH . 'app/models/autentify_plugin.php' );

	define( 'AUTENTIFY_CHECK_TIMEOUT', 180 );

	Autentify_Plugin::get_instance();
}

/**
 * Load Autentify textdomain.
 *
 * Load gettext translate for Autentify text domain.
 *
 * @since 1.0.0
 *
 * @return void
 */
function autentify_load_plugin_textdomain() {
	load_plugin_textdomain( 'autentify' );
}

/**
 * Autentify admin notice for AUTENTIFY_API_TOKEN variable.
 *
 * Warning when the site doesn't have the AUTENTIFY_API_TOKEN variable.
 *
 * @since 1.0.0
 *
 * @return void
 */
function autentify_fail_api_token_variable() {
	$message = 'Autentify requer o API Token. Para configurar o API Token, acesse o menu lateral esquerdo do WordPress,'
		. ' localize a opção "Autentify", em seguida adicione o API Token no campo correspondente, e clique em salvar mudanças.'
		. ' E para obter o API Token entre em nosso painel: <a href="https://painel.autentify.com.br/developers/api_token" target="_blank">www.painel.autentify.com.br/developers/api_token</a>';
	$html_message = sprintf( '<div class="notice notice-error is-dismissible">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}

/**
 * Autentify admin notice for API connection.
 *
 * Warning when the API is not available.
 *
 * @since 1.0.0
 *
 * @return void
 */
function autentify_fail_api_connection() {
	$message = 'Autentify API não está disponível no momento. Por favor, aguade um pouco, e tente novamente mais tarde. Para mais informações entre em nosso site: <a href="https://autentify.com.br" target="_blank">www.autentify.com.br</a>';
	$html_message = sprintf( '<div class="notice notice-error is-dismissible">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}

/**
 * Autentify admin notice for Authentication.
 *
 * Warning when the current API Token doesn't authenticate to generate the Bearer Token.
 *
 * @since 1.0.0
 *
 * @return void
 */
function autentify_fail_authentication() {
	$message = 'Autentify não conseguiu validar o API Token definido nas configurações para gerar o Bearer Token. Entre em nosso painel, e verifique se o API Token foi copiado corretamente: <a href="https://painel.autentify.com.br/developers/api_token" target="_blank">www.painel.autentify.com.br/developers/api_token</a>';
	$html_message = sprintf( '<div class="notice notice-error is-dismissible">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}

/**
 * Autentify admin notice for minimum PHP version.
 *
 * Warning when the site doesn't have the minimum required PHP version.
 *
 * @since 1.0.0
 *
 * @return void
 */
function autentify_fail_php_version() {
	/* translators: %s: PHP version */
	$message = sprintf( esc_html__( 'Autentify requer a versão mínima do PHP %s+. O plugin atualmente NÃO ESTÁ FUNCIONANDO.', 'autentify' ), '5.6' );
	$html_message = sprintf( '<div class="notice notice-error is-dismissible">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}

/**
 * Autentify admin notice for minimum WordPress version.
 *
 * Warning when the site doesn't have the minimum required WordPress version.
 *
 * @since 1.5.0
 *
 * @return void
 */
function autentify_fail_wp_version() {
	/* translators: %s: WordPress version */
	$message = sprintf( esc_html__( 'Autentify requer a versão mínima do WordPress %s+. O plugin atualmente NÃO ESTÁ FUNCIONANDO.', 'autentify' ), '4.7' );
	$html_message = sprintf( '<div class="notice notice-error is-dismissible">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}

/**
 * Setups WooCommerce HPOS compatibility.
 *
 * @since 2.2.0
 *
 * @return void
 */
function setup_hpos_compatibility() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'custom_order_tables',
			'autentify-wocommerce/autentify.php',
			true
		);
	}
}