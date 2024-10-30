<?php

class Autentify_Autenti_Commerce_Payment {
    private $amount;
    private $card_last_numbers;
    private $cardholder_name;
    private $cardholder_document_external_id;

    public function __construct($amount, $card_last_numbers, $cardholder_name, $cardholder_document_external_id) {
        $this->amount = (string) $amount;
        $this->card_last_numbers = $card_last_numbers;
        $this->cardholder_name = $cardholder_name;
        $this->cardholder_document_external_id = $cardholder_document_external_id;
    }

    public function get_amount() { return $this->amount; }
    public function get_card_last_numbers() { return $this->card_last_numbers; }
    public function get_cardholder_name() { return $this->cardholder_name; }
    public function get_cardholder_document_external_id() { return $this->cardholder_document_external_id; }

    public function to_array() {
        $data = array(
            "amount" => $this->get_amount(),
            "card_last_numbers" => $this->get_card_last_numbers(),
            "cardholder_name" => $this->get_cardholder_name(),
            "cardholder_document_external_id" => $this->get_cardholder_document_external_id()
        );

        return $data;
    }
}