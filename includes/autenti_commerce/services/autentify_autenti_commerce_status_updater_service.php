<?php

class Autentify_Autenti_Commerce_Status_Updater_Service {
    private $autenti_commerce_client;
    private $autenti_commerce;

    public function __construct($autenti_commerce) {
        $this->autenti_commerce = $autenti_commerce;
        $this->autenti_commerce_client = Autentify_Autenti_Commerce_Client::get_instance();
    }

    public function update($order_id) {
        if (!$this->autenti_commerce->can_update_status()) {
            return $this->autenti_commerce;
        }

        $response = $this->autenti_commerce_client->fetch($this->autenti_commerce->get_id());

        if ($response == null) {
            return $this->autenti_commerce;
        }

		$updated_autenti_commerce = Autentify_Autenti_Commerce::with_encoded_json($response);
        $updated_autenti_commerce->update_status_updated_at();

        update_post_meta($order_id, 'autenti_commerce', $updated_autenti_commerce->to_std_class());

        return $updated_autenti_commerce;
    }
}