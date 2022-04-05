<?php

class Autentify_Email_Helper {
  private static $instance;

  private function __construct() {}

  public static function get_instance() {
    if ( ! isset( self::$instance ) ) {
      self::$instance = new Autentify_Email_Helper();
    }

    return self::$instance;
  }
  
  function is_valid( $email ) {
    if ( preg_match( "/\A[a-zA-Z0-9.!\#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*\z/", $email ) ) {
      return true;
    }

    return false;
  }
}