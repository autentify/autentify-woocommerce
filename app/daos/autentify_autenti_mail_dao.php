<?php

class Autentify_Autenti_Mail_DAO {
  private $api;
  private $auth;
  private static $instance;

  private function __construct() {
    $this->api = Autentify_Api::get_instance();
    $this->auth = Autentify_Auth::get_instance();
  }

  public static function get_instance() {
    if ( ! isset( self::$instance ) ) {
      self::$instance = new Autentify_Autenti_Mail_DAO();
    }
    return self::$instance;
  }

  /**
   * This method makes a POST in AutentiMail API to check the email and CPF.
   * @param $email String that contains the email.
   * @param $cpf String that contains the CPF.
   * @return a success or an error object.
   */
  public function check( $email, $cpf = null) {
    $curl = curl_init();
    
		$url = $this->api->get_base_url() . '/v2/autenti_mails';
    
		$request_body = array(
			'email'    => $email
		);

    $cpf = Autentify_Cpf_Helper::get_instance()->format( $cpf );

    if ( ! empty( $cpf ) ) {
      $request_body['cpf'] = $cpf;
    }

		$args = array(
			'body'            => $request_body,
			'timeout'         => '10',
      'headers'         => array(
        'Authorization' => 'Bearer ' . $this->auth->get_bearer_token()
      )
		);

		$response = wp_remote_post( $url, $args );
		$body = json_decode( wp_remote_retrieve_body( $response ) );
    
    return $body;
  }
}