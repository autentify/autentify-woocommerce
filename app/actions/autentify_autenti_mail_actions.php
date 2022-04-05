<?php

/** AutentiMail POST */
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

    $order_id = sanitize_text_field( $_REQUEST['param1'] );
    $order = wc_get_order( $order_id );
    $email = $order->get_billing_email();

    if ( ! Autentify_Email_Helper::get_instance()->is_valid_email( $email ) ) {
      $response['success'] = false;
      $response['message'] = 'E-mail inválido.';
      echo json_encode( $response );
      die();
    }

    $autenti_mail_DAO = Autentify_Autenti_Mail_DAO::get_instance();
    if ( Autentify_Cpf_Helper::get_instance()->is_valid( $order->billing_cpf ) ) {
      $formatted_autenti_mail_cpf = Autentify_Autenti_Mail_Helper::get_instance()->format( $order->billing_cpf );
      $autenti_mail_response = $autenti_mail_DAO->check( $email, $formatted_autenti_mail_cpf );
    } else {
      $autenti_mail_response = $autenti_mail_DAO->check( $email );
    }

    $response['success'] = $autenti_mail_response->status == '201';
    $response['message'] = '';
    
    if ( ! $response['success'] ) {
      $response['message'] = Autentify_Api::get_instance()->get_error_messsage( $autenti_mail_response->code );
    } else {
      $response['message'] = 'Consulta finalizada com sucesso!';
      $autentify_autenti_mail = Autentify_Autenti_Mail::with_encoded_json($autenti_mail_response->autenti_mail);
      $response['autenti_mail'] = $autentify_autenti_mail->to_json();

      // WooCommerce 3.0 or later.
      if ( ! method_exists( $order, 'update_meta_data' ) ) {
        update_post_meta( $order_id, "autenti_mail", $autenti_mail_response->autenti_mail );
      } else {
        $order->update_meta_data( "autenti_mail", $autenti_mail_response->autenti_mail );
        $order->save();
      }
    }

    echo json_encode( $response );
    die();
  }
  add_action( 'wp_ajax_autentify_autenti_mail_post', 'autentify_autenti_mail_post' );
}