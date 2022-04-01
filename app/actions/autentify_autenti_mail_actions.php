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
    $response['email'] = sanitize_email( $_REQUEST['param2'] );

    if ( ! Autentify_Email_Helper::get_instance()->is_valid_email( $response['email'] ) ) {
      $response['success'] = false;
      $response['message'] = 'E-mail inválido.';
      echo json_encode( $response );
      die();
    }

    $autenti_mail_DAO = Autentify_Autenti_Mail_DAO::get_instance();
    $autenti_mail_response = $autenti_mail_DAO->check( $response['email'] );

    $response['success'] = $autenti_mail_response->status == '201';
    $response['message'] = '';
    
    if ( ! $response['success'] ) {
      $response['message'] = Autentify_Api::get_instance()->get_error_messsage( $autenti_mail_response->code );
    } else {
      $response['message'] = 'Consulta finalizada com sucesso!';

      $risk_score_html = Autentify_Score_Helper::get_instance()->get_status_html( $autenti_mail_response->autenti_mail->risk_score );
	    $autenti_mail_response->autenti_mail->risk_score_html = $risk_score_html;
      $risk_score_msg_pt_br = Autentify_Score_Helper::get_instance()->get_risk_score_msg( $autenti_mail_response->autenti_mail->risk_score_msg );
	    $autenti_mail_response->autenti_mail->risk_score_msg_pt_br = $risk_score_msg_pt_br;

      $response['autenti_mail'] = $autenti_mail_response->autenti_mail;

      $order = wc_get_order( $order_id );

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