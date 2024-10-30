<?php

class Autentify_Autenti_Mail {
  private $id;
  private $email;
  private $cpf;
  private $risk_score;
  private $risk_score_msg;
  private $description;
  private $created_at;
  private $updated_at;

  public function __construct($email, $cpf = null) {
    $this->email = $email;
    $this->cpf = Autentify_Autenti_Mail_Helper::get_instance()->format_cpf( $cpf );
  }

  /**
   * Instances an Autentify_Autenti_Mail using the encoded JSON.
   * @param stdClass The object must contain the encoded JSON with the
   * Autentify_Autenti_Mail attribute names.
   * @return Autentify_Autenti_Mail The created instance.
   */
  public static function with_encoded_json( $encoded_json ) {
    $instance = property_exists( $encoded_json, 'cpf' ) ? new self( $encoded_json->email, $encoded_json->cpf ) : new self( $encoded_json->email );
    $instance->id = $encoded_json->id;
    $instance->risk_score = $encoded_json->risk_score;
    $instance->risk_score_msg = $encoded_json->risk_score_msg;
    $instance->description = $encoded_json->description;
    $instance->created_at = $encoded_json->created_at;
    $instance->updated_at = $encoded_json->updated_at;
    return $instance;
  }

  /**
   * Creates the risk score HTML to be used in orders page.
   * @return String The risk score in HTML.
   */
  public function get_risk_score_html() {
    $css_color = Autentify_Score_Helper::get_instance()->get_risk_score_css_color($this->risk_score);
    $html_score = "<span style='color: #" . $this->get_risk_score_css_color() . ";'>"
        . "<b>$this->risk_score</b></span>";
    return $html_score;
  }

  /**
   * Chooses the risk score css color by risk score.
   * @return String The CSS color value in hexadecimal.
   */
  private function get_risk_score_css_color() {
    if ( $this->risk_score <= 300 ) {
      return "e54270";
    } elseif ( $this->risk_score > 300 && $this->risk_score <= 600 ) {
      return "f7b924";
    }
    return "3ac47d";
  }

  /**
   * Chooses the pt-br risk score message by the default risk score message that is
   * in English.
   * @return String The translated risk score message.
   */
  public function get_risk_score_msg_pt_br() {
    $translated_risk_score_msg = " Risco";
    $status_css_class = '';
    switch ( $this->risk_score_msg ) {
      case 'high':
        $status_css_class = 'status-danger';
        $translated_risk_score_msg = "Alto" . $translated_risk_score_msg;
        break;
      case 'mid':
        $status_css_class = 'status-warning';
        $translated_risk_score_msg = "Médio" . $translated_risk_score_msg;
        break;
      case 'low':
        $status_css_class = 'status-success';
        $translated_risk_score_msg = "Baixo" . $translated_risk_score_msg;
        break;
      default:
        $translated_risk_score_msg = "Não identificado";
        break;
    }

    $translated_risk_score_html = '<div class="autentify-analysis-status '
        . $status_css_class . '">'
        . '<span>' . $translated_risk_score_msg . '</span></div>';

    return $translated_risk_score_html;
  }

  /**
   * Creates an Array using the attributes names as keys and attribute values as values.
   * @return Array The Array with the keys and values.
   */
  public function to_json() {
    $autenti_mail_in_json = [
      "id" => $this->id,
      "email" => $this->email
    ];

    if ( isset( $this->cpf ) ) {
      $autenti_mail_in_json["cpf"] = $this->cpf;
    }
    
    $autenti_mail_in_json["risk_score"] = $this->risk_score;
    $autenti_mail_in_json["risk_score_html"] = $this->get_risk_score_html();
    $autenti_mail_in_json["risk_score_msg"] = $this->risk_score_msg;
    $autenti_mail_in_json["risk_score_msg_pt_br"] = $this->get_risk_score_msg_pt_br();
    $autenti_mail_in_json["created_at"] = $this->created_at;
    $autenti_mail_in_json["updated_at"] = $this->updated_at;

    return $autenti_mail_in_json;
  }

  /**
   * Creates the check button in HTML for orders page. The check button only is enabled
   * if the email is isset and it is not empty.
   * @return String The values contains the button HTML.
   */
  public function get_check_btn_in_html( $order_id, $admin_ajax_url ) {
    $check_btn_with_email = "<a href='#' class='button button-primary'"
        . "onclick='startIndividualCheck(\"$order_id\", \"$admin_ajax_url\")'>"
        . "Iniciar Consulta</a>";

    if ( isset ( $this->email ) && ! empty( $this->email ) ) {
      return $check_btn_with_email;
    }

    return "Sem e-mail";
  }
  
  /**
   * Calls is_valid method from Autentify_Email_Helper to validate
   * the email attribute.
   * @return Boolean The value is true if the email is valid.
   */
  public function has_valid_email() {
    return Autentify_Email_Helper::get_instance()->is_valid( $this->email );
  }

  /**
   * Calls is_valid method from Autentify_Cpf_Helper to validate
   * the cpf attribute.
   * @return Boolean The value is true if the cpf is valid.
   */
  public function has_valid_cpf() {
    return Autentify_Cpf_Helper::get_instance()->is_valid( $this->cpf );
  }

  public function get_email() {
    return $this->email;
  }

  public function get_cpf() {
    return $this->cpf;
  }
}