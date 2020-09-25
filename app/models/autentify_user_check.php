<?php

class Autentify_User_Check {
  private $id;
  private $email;
  private $score;
  private $score_msg;
  private $facial_biometric_validation_status;
  private $created_at;
  private $updated_at;

  public function __construct( $id, $email, $score, $score_msg,
    $facial_biometric_validation_status, $created_at, $updated_at ) {
    $this->id = $id;
    $this->email = $email;
    $this->score = $score;
    $this->score_msg = $score_msg;
    $this->facial_biometric_validation_status = $facial_biometric_validation_status;
    $this->created_at = $created_at;
    $this->updated_at = $updated_at;
  }

  public function get_html_score() {
    $htmlScore = $this->score == 0 && $this->score_msg == "Pendente" ? "Aguardando..." :
      "<span style='color: #". $this->get_score_css_color() . ";'>$this->score</span>";
    return $htmlScore;
  }

  private function get_score_css_color() {
    if ( $this->score <= 300 ) {
      return "e54270";
    } elseif ( $this->score > 300 && $this->score <= 600 ) {
      return "f7b924";
    }
    return "3ac47d";
  }

  public function get_score_msg() {
    return $this->score_msg;
  }
}