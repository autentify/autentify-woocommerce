<?php

class Autentify_Autenti_Commerce_Status_Updater_Service {
    private $autenti_commerce_client;
    private $autenti_commerce;

    public function __construct($autenti_commerce) {
        $this->autenti_commerce = $autenti_commerce;
        $this->autenti_commerce_client = Autentify_Autenti_Commerce_Client::get_instance();
    }

    public function update($order) {
        if (!$this->autenti_commerce->can_update_status()) {
            return $this->autenti_commerce;
        }

        $response = $this->autenti_commerce_client->fetch($this->autenti_commerce->get_id());

        if ($response == null) {
            return $this->autenti_commerce;
        }

		$updated_autenti_commerce = Autentify_Autenti_Commerce::with_encoded_json($response);
        $updated_autenti_commerce->update_status_updated_at();

        $order->update_meta_data('autenti_commerce', $updated_autenti_commerce->to_std_class());
        $order->save();

        return $updated_autenti_commerce;
    }
}