<?php

class Autentify_Autenti_Mail {
  private $id;
  private $email;
  private $risk_score;
  private $risk_score_msg;
  private $created_at;
  private $updated_at;

  public function __construct( $encoded_autenti_mail_in_json ) {
    $this->id = $encoded_autenti_mail_in_json->id;
    $this->email = $encoded_autenti_mail_in_json->email;
    $this->risk_score = $encoded_autenti_mail_in_json->risk_score;
    $this->risk_score_msg = $encoded_autenti_mail_in_json->risk_score_msg;
    $this->description = $encoded_autenti_mail_in_json->description;
    $this->created_at = $encoded_autenti_mail_in_json->created_at;
    $this->updated_at = $encoded_autenti_mail_in_json->updated_at;
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
}