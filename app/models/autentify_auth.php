<?php

class Autentify_Auth {
	private $bearer_token;
	private $error;
	private $is_authenticated;
  private $api;
	private static $instance;

	private function __construct() {
    $this->api = Autentify_Api::get_instance();
  }

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Autentify_Auth();
		}
		return self::$instance;
	}

	/**
	 * @return CURL JSON response decoded
	 */
	private function authenticate() {
		$curl = curl_init();

		$post_body = array(
			'api_token'    => $this->api->get_token()
		);

		$args = array(
			'body'        => $post_body,
			'timeout'     => '10',
		);

		$url = $this->api->get_base_url() . "/v2/auth";
		$response = wp_remote_post( $url, $args );
		$body = json_decode( wp_remote_retrieve_body( $response ) );

		return $body;
	}

	private function set_bearer_token() {
		$response = $this->authenticate();
		if ( ! isset( $response->bearer_token ) ) {
			$this->error = $response == null ?
				"Autentify API não está disponível no momento. Por favor, aguade um pouco, e tente novamente mais tarde." :
				$response;
		} else {
			$this->bearer_token = $response->bearer_token;
			$this->set_bearer_token_cookie();
		}
	}

	private function set_bearer_token_cookie() {
		$domain = preg_replace( "(^https?://)", "", get_site_url() );
		// 3480 = 58 minutes
		setcookie( "autentify_bt", $this->bearer_token, time() + 3480, "/",
			$_SERVER['HTTP_HOST'], $this->is_secure_conn(), true
		);
	}

	public function invalidate_bearer_token_cookie() {
		if ( isset( $_COOKIE['autentify_bt'] ) ) {
    	unset( $_COOKIE['autentify_bt'] );
			setcookie( "autentify_bt", "", time() - 3600, "/",
				$_SERVER['HTTP_HOST'], $this->is_secure_conn(), true
			);
		}
	}

	private function is_secure_conn() {
		$is_secure = false;
		if ( isset($_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
				$is_secure = true;
		}
		elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
							|| ! empty( $_SERVER['HTTP_X_FORWARDED_SSL'] ) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on' ) {
				$is_secure = true;
		}
		return $is_secure;
	}

	public function get_bearer_token() {
		if ( ! isset( $_COOKIE['autentify_bt'] ) ) {
			$this->set_bearer_token();
		} else {
			$this->bearer_token = sanitize_text_field( $_COOKIE['autentify_bt'] );
		}

		if ( isset( $this->bearer_token ) ) {
			$this->is_authenticated = true;
			return $this->bearer_token;
		} else {
			return $this->error;
		}
	}

	public function is_authenticated() {
		$this->get_bearer_token();
		return $this->is_authenticated;
	}
}