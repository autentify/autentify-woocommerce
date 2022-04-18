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

    $autenti_mail_post_meta = get_post_meta( $order_id, 'autenti_mail', true );
    $has_autenti_mail = isset( $autenti_mail_post_meta ) && ! empty( $autenti_mail_post_meta );

    if ( ($has_autenti_mail && ! property_exists( $autenti_mail_post_meta, 'status')) ||
        ($has_autenti_mail && property_exists( $autenti_mail_post_meta, 'status') && $autenti_mail_post_meta->status == 202 && ($autenti_mail_post_meta->created_at + AUTENTIFY_CHECK_TIMEOUT) > Time()) ) {
      $response['success'] = false;
      $response['message'] = 'Consulta em andamento.';
      echo json_encode( $response );
      die();
    }

    $order = wc_get_order( $order_id );
    $autenti_mail = new Autentify_Autenti_Mail( $order->get_billing_email(), get_post_meta( $order->get_id(), '_billing_cpf', true ) );

    if ( ! $autenti_mail->has_valid_email() ) {
      $response['success'] = false;
      $response['message'] = 'E-mail inválido.';
      echo json_encode( $response );
      die();
    }

    $autenti_mail_DAO = Autentify_Autenti_Mail_DAO::get_instance();
    if ( $autenti_mail->has_valid_cpf() ) {
      $autenti_mail_response = $autenti_mail_DAO->check( $order_id, $autenti_mail->get_email(), $autenti_mail->get_cpf() );
    } else {
      $autenti_mail_response = $autenti_mail_DAO->check( $order_id, $autenti_mail->get_email() );
    }

    $response['success'] = $autenti_mail_response->status == '201';
    $response['message'] = '';
    
    if ( ! $response['success'] ) {
      $response['message'] = Autentify_Api::get_instance()->get_error_messsage( $autenti_mail_response->code );
    } else {
      $response['message'] = 'Consulta finalizada com sucesso!';
      $autentify_autenti_mail = Autentify_Autenti_Mail::with_encoded_json( $autenti_mail_response->autenti_mail );
      $response['autenti_mail'] = $autentify_autenti_mail->to_json();

      update_post_meta( $order_id, 'autenti_mail', $autenti_mail_response->autenti_mail );
    }

    echo json_encode( $response );
    die();
  }
  
  add_action( 'wp_ajax_autentify_autenti_mail_post', 'autentify_autenti_mail_post' );
}

if ( get_option( 'autentify_auto_order_check' ) == 'true' ) {
  if ( ! function_exists( 'autentify_autenti_mail_check' ) ) {
    function autentify_autenti_mail_check( $order_id ) {
      $autenti_mail_post_meta = get_post_meta( $order_id, 'autenti_mail', true );
      $has_autenti_mail = isset( $autenti_mail_post_meta ) && ! empty( $autenti_mail_post_meta );

    if ( ($has_autenti_mail && ! property_exists( $autenti_mail_post_meta, 'status')) ||
        ($has_autenti_mail && property_exists( $autenti_mail_post_meta, 'status') && $autenti_mail_post_meta->status == 202 && ($autenti_mail_post_meta->created_at + AUTENTIFY_CHECK_TIMEOUT) > Time()) ) {
        return;
      }

      $order = wc_get_order( $order_id );
      $autenti_mail = new Autentify_Autenti_Mail( $order->get_billing_email(), get_post_meta( $order->get_id(), '_billing_cpf', true ) );

      if ( ! $autenti_mail->has_valid_email() ) return;

      $autenti_mail_DAO = Autentify_Autenti_Mail_DAO::get_instance();
      if ( $autenti_mail->has_valid_cpf() ) {
        $autenti_mail_response = $autenti_mail_DAO->check( $order_id, $autenti_mail->get_email(), $autenti_mail->get_cpf() );
      } else {
        $autenti_mail_response = $autenti_mail_DAO->check( $order_id, $autenti_mail->get_email() );
      }

      if ( isset( $autenti_mail_response->autenti_mail ) ) {
        update_post_meta( $order_id, 'autenti_mail', $autenti_mail_response->autenti_mail );
      }
    }

    add_action( 'wp_async_autentify_autenti_mail_check', 'autentify_autenti_mail_check' );
  }

  if ( ! function_exists( 'autentify_check_autenti_mail_order_status_changed' ) ) {
    function autentify_check_autenti_mail_order_status_changed( $order_id, $old_status, $new_status ) {
      if( $new_status == 'processing' ) {
        wp_schedule_single_event( 1, 'wp_async_autentify_autenti_mail_check', [ $order_id ] );
      }
    }

    add_action( 'woocommerce_order_status_changed', 'autentify_check_autenti_mail_order_status_changed', 10, 3 );
  }
}