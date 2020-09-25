<?php

/** User Check Single POST */
if ( ! function_exists( 'autentify_user_check_post' ) ) {
  function autentify_user_check_post() {
    if ( ! ( $_SERVER['REQUEST_METHOD'] === 'POST' ) ) {
      die();
    }

    if ( ! current_user_has_role( 'administrator' ) ) {
      $response['success'] = "false";
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

    $user_check_DAO = Autentify_User_Check_DAO::get_instance();
    $user_check_response = $user_check_DAO->insert_many( [$response['email']] );

    $response['success'] = $user_check_response->status == "201";
    $response['message'] = '';
    
    if ( ! $response['success'] ) {
      $response['message'] = Autentify_Api::get_instance()->get_error_messsage( $user_check_response->code );
    } else {
      $response['message'] = "Consulta inicializada com sucesso!";
    }

    echo json_encode( $response );
    die();
  }
  add_action( 'wp_ajax_autentify_user_check_post', 'autentify_user_check_post' );
}

/** User Check Bulk POST */
if ( ! function_exists( 'autentify_user_check_post_bulk_hundle_action_edit_shop_order' ) ) {
  function autentify_user_check_post_bulk_hundle_action_edit_shop_order( $redirect_to, $action, $post_ids ) {
    if ( $action !== 'autentify_user_check_post_bulk' ) {
      return $redirect_to; // Exit
    }
    
    $count_invalid_emails = 0;
    $emails = array();
    foreach ( $post_ids as $post_id ) {
      $order = wc_get_order( $post_id );
      $email = $order->get_billing_email();
      $has_email = isset( $email ) && ! empty( $email );
      
      if ( $has_email && ! Autentify_Email_Helper::get_instance()->is_valid_email( $email ) ) {
        $count_invalid_emails++;
        continue;
      }

      $is_checked_email = array_key_exists( $email, Autentify_User_Check_DAO::get_instance()->get_all() );
      if ( $has_email && ! $is_checked_email ) {
        array_push( $emails, $email );
      }
    }

    // remove duplacated emails
    $emails = array_unique( $emails );

    $user_check_DAO = Autentify_User_Check_DAO::get_instance();
    $user_check_insert_response = $user_check_DAO->insert_many( $emails );

    $insert_success = $user_check_insert_response->status == "201";
    $success_msg = '';
    $api_error_code = 0;
    
    if ( ! $insert_success ) {
      $api_error_code = $user_check_insert_response->code;
    } else {
      $success_msg = "Número de consultas inicializadas: " . sizeof( $emails );
    }

    remove_query_arg( array(
      'autentify_user_check_post_bulk', 'success',
      'success_msg', 'api_error_code',
      'count_invalid_emails',
    ) );

    return $redirect_to = add_query_arg( array(
      'autentify_user_check_post_bulk' => '1',
      'success' => $insert_success,
      'success_msg' => $success_msg,
      'api_error_code' => $api_error_code,
      'count_invalid_emails' => $count_invalid_emails
    ), $redirect_to );
  }
  add_filter( 'handle_bulk_actions-edit-shop_order', 'autentify_user_check_post_bulk_hundle_action_edit_shop_order', 10, 3 );
}

if ( ! function_exists( 'autentify_user_check_post_bulk_action_admin_notice' ) ) {
  function autentify_user_check_post_bulk_action_admin_notice() {
    if ( empty( $_REQUEST['autentify_user_check_post_bulk'] ) ) {
      remove_query_arg( array(
        'autentify_user_check_post_bulk', 'success',
        'success_msg', 'api_error_code',
        'count_invalid_emails',
      ) );
      return; // Exit
    }

    $success_msg = wp_kses( $_REQUEST['success_msg'], array( 'a' => array( 'href' => array(), 'title' => array(), 'target' => array() ) ) );

    $success = boolval( $_REQUEST['success'] );
    $api_error_code = intval( $_REQUEST['api_error_code'] );
    $api_message = $success ? $success_msg : Autentify_Api::get_instance()->get_error_messsage( $api_error_code );
    $count_invalid_emails = intval( $_REQUEST['count_invalid_emails'] );

    $notice_classes = "notice";
    $notice_classes .= $success ? " notice-success" : " notice-error fade";
    $notice_msg = "<div class='$notice_classes'><p>";
    $notice_msg .= $api_message;
    $notice_msg .= $count_invalid_emails > 0 ? "<br>E-mails inválidos removidos: $count_invalid_emails" : "";
    $notice_msg .= '</p></div>';
      
    echo $notice_msg;
  }
  add_action( 'admin_notices', 'autentify_user_check_post_bulk_action_admin_notice' );
}