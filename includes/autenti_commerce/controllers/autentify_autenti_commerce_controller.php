<?php

class Autentify_Autenti_Commerce_Controller {
    public function autentify_autenti_commerce_initiate_analysis() {
        if ( ! ( $_SERVER['REQUEST_METHOD'] === 'POST' ) ) {
            die();
        }

        if ( ! current_user_has_role( 'administrator' ) ) {
            $response['success'] = 'false';
            echo json_encode( $response );
            die();
        }

        $order_id = sanitize_text_field( $_REQUEST['param1'] );

        $autenti_commerce_post_meta = get_post_meta( $order_id, 'autenti_commerce', true );
        $has_autenti_commerce = isset( $autenti_commerce_post_meta ) && ! empty( $autenti_commerce_post_meta );

        if ( $has_autenti_commerce ) {
            $response['success'] = false;
            $response['message'] = 'Consulta em andamento.';
        }

        $autenti_commerce_data = (new Autentify_Autenti_Commerce_Data_Builder($order_id))->build();

        $autenti_commerce_client = Autentify_Autenti_Commerce_Client::get_instance();
        $autenti_commerce_response = $autenti_commerce_client->initiate_analysis( $autenti_commerce_data );

        $response['success'] = $autenti_commerce_response->status == '201';
        $response['message'] = '';

        if ( ! $response['success'] ) {
            $response['message'] = Autentify_Api::get_instance()->get_error_messsage( $autenti_commerce_response->code );
        } else {
            $response['message'] = 'Consulta finalizada com sucesso!';
            $response['autenti_commerce'] = $autenti_commerce_data;

            $autenti_commerce = Autentify_Autenti_Commerce::with_encoded_json(
                $autenti_commerce_response->autenti_commerce
            );

            $response['status_html'] = Autentify_Autenti_Commerce_Helper::get_instance()->get_status_html(
                $autenti_commerce
            );

            update_post_meta( $order_id, 'autenti_commerce', $autenti_commerce_response->autenti_commerce );
        }

        echo json_encode( $response );
        die();
    }

    public function autentify_autenti_commerce_update_analysis() {
        if ( ! ( $_SERVER['REQUEST_METHOD'] === 'POST' ) ) {
          die();
        }
    
        if ( ! current_user_has_role( 'administrator' ) ) {
            die();
        }
    
        $order_id = sanitize_text_field( $_REQUEST['order_id'] );

        $autenti_commerce_post_meta = get_post_meta( $order_id, 'autenti_commerce', true );

        if ($autenti_commerce_post_meta === null || $autenti_commerce_post_meta === '') {
            die();
        }

		$autenti_commerce = Autentify_Autenti_Commerce::with_encoded_json(
            $autenti_commerce_post_meta
        );

		$status_updater_service = new Autentify_Autenti_Commerce_Status_Updater_Service(
            $autenti_commerce
        );

		$updated_autenti_commerce = $status_updater_service->update( $order_id );
        $encoded_autenti_commerce = json_encode( $updated_autenti_commerce->to_json() );

        echo $encoded_autenti_commerce;
        die();
    }
}