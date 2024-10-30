<?php

class Autentify_Autenti_Commerce_Client {
  private $api;
  private $auth;
  private static $instance;

  private function __construct() {
    $this->api = Autentify_Api::get_instance();
    $this->auth = Autentify_Auth::get_instance();
  }

  public static function get_instance() {
    if ( ! isset( self::$instance ) ) {
      self::$instance = new Autentify_Autenti_Commerce_Client();
    }
    return self::$instance;
  }

  public function initiate_analysis( $autenti_commerce_data) {
		$url = $this->api->get_base_url() . '/v2/autenti_commerces';

		$args = array(
			'body'            => json_encode($autenti_commerce_data),
			'timeout'         => AUTENTIFY_CHECK_TIMEOUT,
      'headers'         => array(
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $this->auth->get_bearer_token()
      )
		);

    $body = null;
    $response = null;
    try {
      $response = wp_remote_post( $url, $args );
      $body = json_decode( wp_remote_retrieve_body( $response ) );
    } catch (Exception $e) {
      error_log("\n\n\initiate_analysis 2\n\n\n");
      // delete_post_meta( $order_id, 'autenti_commerce' );
    }

    if ( is_wp_error( $response ) ) {
      // delete_post_meta( $order_id, 'autenti_commerce' );
    }
    
    return $body;
  }

  public function fetch( $autenti_commerce_id) {
		$url = $this->api->get_base_url() . '/v2/autenti_commerces/' . $autenti_commerce_id;

		$args = array(
			'timeout'         => AUTENTIFY_CHECK_TIMEOUT,
      'headers'         => array(
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $this->auth->get_bearer_token()
      )
		);

    $body = null;
    $response = null;
    try {
      $response = wp_remote_get( $url, $args );
      $body = json_decode( wp_remote_retrieve_body( $response ) );
    } catch (Exception $e) {
      // delete_post_meta( $order_id, 'autenti_commerce' );
    }

    if ( is_wp_error( $response ) ) {
      // delete_post_meta( $order_id, 'autenti_commerce' );
    }
    
    return $body;
  }
}