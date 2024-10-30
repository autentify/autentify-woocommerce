<?php

class Autentify_Autenti_Commerce_Item {
    private $code;
    private $description;
    private $amount;

    public function __construct($code, $description, $amount) {
        $this->code = $code;
        $this->description = $description;
        $this->amount = $amount;
    }

    public function get_code() { return $this->code; }
    public function get_description() { return $this->description; }
    public function get_amount() { return $this->amount; }

    public function to_array() {
        $data = array(
            "code" => $this->get_code(),
            "description" => $this->get_description(),
            "amount" => $this->get_amount(),
        );

        return $data;
    }
}