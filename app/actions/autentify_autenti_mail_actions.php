<?php

/** User Check Single POST */
if ( ! function_exists( 'autentify_autenti_mail_post' ) ) {
  function autentify_autenti_mail_post() {
    if ( ! ( $_SERVER['REQUEST_METHOD'] === 'POST' ) ) {
      die();
    }

    if ( ! current_user_has_role( 'administrator' ) ) {
      $response['success'] = 'false';
      echo json_encode( $response );
      die();
    }

    $response['email'] = sanitize_email( $_REQUEST['param1'] );

    if ( ! Autentify_Email_Helper::get_instance()->is_valid_email( $response['email'] ) ) {
      $response['success'] = false;
      $response['message'] = 'E-mail inválido.';
      echo json_encode( $response );
      die();
    }

    $autenti_mail_DAO = Autentify_Autenti_Mail_DAO::get_instance();
    $autenti_mail_response = $autenti_mail_DAO->check( $response['email'] );

    echo json_encode( $autenti_mail_response );
    die();

    $response['success'] = $autenti_mail_response->status == '201';
    $response['message'] = '';
    
    if ( ! $response['success'] ) {
      $response['message'] = Autentify_Api::get_instance()->get_error_messsage( $autenti_mail_response->code );
    } else {
      $response['message'] = 'Consulta finalizada com sucesso!';
      // GET the order ID
      // Save the order meta data: external_id, risk_score, risk_score-msg and description
    }

    echo json_encode( $response );
    die();
  }
  add_action( 'wp_ajax_autentify_autenti_mail_post', 'autentify_autenti_mail_post' );
}