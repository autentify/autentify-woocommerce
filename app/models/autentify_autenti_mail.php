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

  public static function with_encoded_json( $encoded_json ) {
    $instance = new self();
    $instance->id = $encoded_json->id;
    $instance->email = $encoded_json->email;

    if ( isset( $encoded_json->cpf ) ) {
      $instance->cpf = $encoded_json->cpf;
    }

    $instance->risk_score = $encoded_json->risk_score;
    $instance->risk_score_msg = $encoded_json->risk_score_msg;
    $instance->description = $encoded_json->description;
    $instance->created_at = $encoded_json->created_at;
    $instance->updated_at = $encoded_json->updated_at;
    return $instance;
  }

  public function get_risk_score_html() {
    $css_color = Autentify_Score_Helper::get_instance()->get_risk_score_css_color($this->risk_score);
    $html_score = "<span style='color: #" . $this->get_risk_score_css_color() . ";'>$this->risk_score</span>";
    return $html_score;
  }

  private function get_risk_score_css_color() {
    if ( $this->risk_score <= 300 ) {
      return "e54270";
    } elseif ( $this->risk_score > 300 && $this->risk_score <= 600 ) {
      return "f7b924";
    }
    return "3ac47d";
  }

  public function get_risk_score_msg_pt_br() {
    $translated_risk_score_msg = " Risco";
    switch ( $this->risk_score_msg ) {
      case 'high':
        $translated_risk_score_msg = "Alto" . $translated_risk_score_msg;
        break;
      case 'mid':
        $translated_risk_score_msg = "Médio" . $translated_risk_score_msg;
        break;
      case 'low':
        $translated_risk_score_msg = "Baixo" . $translated_risk_score_msg;
        break;
      default:
        $translated_risk_score_msg = "Não identificado";
        break;
    }

    return $translated_risk_score_msg;
  }

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
}