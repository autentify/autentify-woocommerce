<?php

class Autentify_Score_Helper {
  private static $instance;
  
  private function __construct() {}

  public static function get_instance() {
    if ( ! isset( self::$instance ) ) {
      self::$instance = new Autentify_Score_Helper();
    }
    return self::$instance;
  }

  public function get_risk_score_html($score) {
    $css_color = Autentify_Score_Helper::get_instance()->get_risk_score_css_color($score);
    $html_score = "<span style='color: #" . $css_color . ";'>$score</span>";
    return $html_score;
  }

  public function get_risk_score_css_color($score) {
    if ( $score <= 300 ) {
      return "e54270";
    } elseif ( $score > 300 && $score <= 600 ) {
      return "f7b924";
    }
    return "3ac47d";
  }

  public function get_risk_score_msg($default_score_msg) {
    $translated_risk_score_msg = " Risco";
    switch ($default_score_msg) {
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