<?php

class Autentify_Autenti_Commerce_Document {
    private $kind;
    private $external_id;

    public function __construct($kind, $external_id) {
        $this->kind = $kind;
        $this->external_id = $external_id;
    }

    public function get_kind() { return $this->kind; }
    public function get_external_id() { return $this->external_id; }

    public function to_array() {
        return array(
            'kind' => $this->get_kind(),
            'external_id' => $this->get_external_id()
        );
    }
}