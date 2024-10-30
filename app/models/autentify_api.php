<?php

class Autentify_Api {
	private $base_url;
	private $token;
  private $is_available;
  private static $instance;

  private function __construct() {
    $this->base_url = "https://painel.autentify.com.br/api";
    $this->token = AUTENTIFY_API_TOKEN;
  }

  public static function get_instance() {
    if ( ! isset( self::$instance ) ) {
      self::$instance = new Autentify_Api();
    }
    return self::$instance;
  }

  public function get_base_url() {
    return $this->base_url;
  }

  public function set_token($token) {
    $this->token = $token;
  }

  public function get_token() {
    return $this->token;
  }

  public function get_error_messsage( $code ) {
    switch ( $code ) {
      case 1:
        return 'Erro interno do servidor.';
      case 2:
        return 'API Token inválido.';
      case 3:
        return 'Token Bearer inválido.';
      case 4:
        return 'Data de pesquisa inválida.';
      case 5:
        return 'Você precisa de pelo menos 1 e-mail para realizar a(s) consulta(s).';
      case 6:
        return 'E-mail(s) inválido(s).';
      case 7:
        return 'Um ou mais e-mails que você está tentando consultar já pertence a sua lista de e-mail consultados. Tente novamente, por favor.';
      case 8:
        return 'Você não tem crédito suficiente para realizar a ação desejada. Para adquirir mais créditos visite: <a href="https://www.painel.autentify.com.br" target="_blank">www.painel.autentify.com.br</a>';
      case 13:
        return 'Dados de consulta inválidos.';
      case 16:
        return 'E-mail inválido.';
      case 17:
        return 'CPF inválido.';
      case 18:
        return 'ID inválido.';
      case 19:
        return 'Tempo excedido.';
      default:
        return 'Erro não identificado. Por favor, Espere alguns minutos e tente novamente.';
    }
  }

  public function is_available() {
    if ( isset( $this->is_available ) ) return $this->is_available;

    $url = $this->get_base_url() . "/v2/status";

    // Check, if a valid url is provided
    if( ! filter_var( $url, FILTER_VALIDATE_URL ) ){
        return false;
    }

		$args = array(
			'timeout'     => '10',
		);

    $response = wp_remote_get( $url, $args );
    
    $this->is_available = $response instanceof WP_Error ? false : true;
    return $this->is_available;
  }
}