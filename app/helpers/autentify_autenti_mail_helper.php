<?php

class Autentify_Autenti_Mail_Helper {
  private static $instance;
  
  private function __construct() {}

  public static function get_instance() {
    if ( ! isset( self::$instance ) ) {
      self::$instance = new Autentify_Autenti_Mail_Helper();
    }
    return self::$instance;
  }

  /**
   * Formats the String CPF with special characters into a String
   * with only numbers and without left zeros.
   * @param String $cpf The String that contains the CPF to be formatted.
   * @return String with The formatted cpf.
   */
  public function format_cpf( $cpf ) {
    if (! isset( $cpf )) return $cpf;

    $cpf = trim( $cpf );
    if ( empty( $cpf ) ) return $cpf;

    $cpf = preg_replace( "/[^0-9]/", "", $cpf );
    $cpf = ltrim( $cpf, "0" );
    
    return $cpf;
  }
}