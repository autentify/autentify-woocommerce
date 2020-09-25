<?php

class Autentify_User_Check_DAO {
  private $api;
  private $auth;
  private $user_checks;
  private static $instance;

  private function __construct() {
    $this->api = Autentify_Api::get_instance();
    $this->auth = Autentify_Auth::get_instance();
  }

  public static function get_instance() {
    if ( ! isset( self::$instance ) ) {
      self::$instance = new Autentify_User_Check_DAO();
    }
    return self::$instance;
  }

  /**
   * @param $emails Array that contains a list of emails (String).
   * @return a success or an error object.
   */
  public function insert_many( $emails ) {
    $curl = curl_init();
    
		$url = $this->api->get_base_url() . "/v1/user_checks";
    
		$post_body = array(
			'emails'    => implode(',', $emails)
		);

		$args = array(
			'body'        => $post_body,
			'timeout'     => '10',
      'headers' => array(
        'Authorization' => 'Bearer ' . $this->auth->get_bearer_token()
      )
		);

		$response = wp_remote_post( $url, $args );
		$body = json_decode( wp_remote_retrieve_body( $response ) );
    
    return $body;
  }

  /**
   * @return array Returns a hash map of email and UserCheck object.
   */
  public function get_all() {
    if ( isset( $this->user_checks ) ) return $this->user_checks;

    $curl = curl_init();
    
		$url = $this->api->get_base_url() . "/v1/user_checks";

    $args = array(
			'timeout'     => '10',
      'headers' => array(
        'Authorization' => 'Bearer ' . $this->auth->get_bearer_token()
      )
    );

    $response = wp_remote_get( $url, $args );
		$body = json_decode( wp_remote_retrieve_body( $response ) );

    if ( ! isset( $body ) ) return [];

    $user_checks = array();
    foreach ( $body as $obj ) {
      array_push(
        $user_checks, $obj->email
      );
      $user_checks[$obj->email] = new Autentify_User_Check( $obj->id, $obj->email, $obj->score, $obj->score_msg,
        $obj->facial_biometric_validation_status, $obj->created_at,
        $obj->updated_at
      );
    }
    
    $this->user_checks = $user_checks;
    return $this->user_checks;
  }
}