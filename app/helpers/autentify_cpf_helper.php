<?php

class Autentify_Cpf_Helper {
  private static $instance;
  
  private function __construct() {}

  public static function get_instance() {
    if ( ! isset( self::$instance ) ) {
      self::$instance = new Autentify_Cpf_Helper();
    }
    return self::$instance;
  }

  /**
   * Formats the String CPF with special characters into a String
   * with only numbers and without left zeros.
   * @param String $cpf The String that contains the CPF to be formatted.
   * @return String with The formatted object.
   */
  public function format( $cpf ) {
    $cpf = trim($cpf);
    if ( $cpf == null || empty($cpf) ) return $cpf;

    $cpf = preg_replace( "/[^0-9]/", "", $cpf );
    $cpf = strval( (int) $cpf );
    
    return $cpf;
  }
}