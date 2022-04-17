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
   * Formats the String CPF adding special characters and left zeros.
   * @param String $cpf The String that contains the CPF to be formatted.
   * @return String with The formatted cpf.
   */
  public function format( $cpf ) {
    if ( ! isset( $cpf ) ) return $cpf;

    $cpf = trim( $cpf );
    if ( empty( $cpf ) ) return $cpf;

    $cpf = preg_replace( "/[^0-9]/", "", $cpf );
    $cpf = ltrim( $cpf, "0" );

    $numberOfZerosToAdd = 11 - strlen($cpf);
    for ($i = 0; $i < $numberOfZerosToAdd; $i++) {
      $cpf = "0$cpf";
    }
    
    $cpf = substr_replace($cpf, ".", 3, 0);
    $cpf = substr_replace($cpf, ".", 7, 0);
    $cpf = substr_replace($cpf, "-", 11, 0);
    
    return $cpf;
  }

  /**
   * Checks if the CPF is valid.
   * @param String $cpf The String that contains the CPF to be checked.
   * @return Boolean The value is true if the CPF is valid.
   */
  public function is_valid( $cpf ) {
    if ( ! isset( $cpf ) ) return $cpf;

	  $cpf = Autentify_Cpf_Helper::get_instance()->format( $cpf );
		$cpf = preg_replace( '/[^0-9]/', '', $cpf );

		if ( 11 !== strlen( $cpf ) || preg_match( '/^([0-9])\1+$/', $cpf ) ) {
			return false;
		}

		$digit = substr( $cpf, 0, 9 );
		for ( $j = 10; $j <= 11; $j++ ) {
			$sum = 0;

			for ( $i = 0; $i < $j - 1; $i++ ) {
				$sum += ( $j - $i ) * intval( $digit[ $i ] );
			}

			$summod11 = $sum % 11;
			$digit[ $j - 1 ] = $summod11 < 2 ? 0 : 11 - $summod11;
		}

		return intval( $digit[9] ) === intval( $cpf[9] ) && intval( $digit[10] ) === intval( $cpf[10] );
	}
}