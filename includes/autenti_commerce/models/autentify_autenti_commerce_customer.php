<?php

class Autentify_Autenti_Commerce_Customer {
    private $tax_identifier;
    private $name;
    private $email;
    private $birthdate;
    private $registered_at;
    private $documents;
    private $addresses;
    private $phones;

    public function __construct($tax_identifier, $name, $email, $birthdate, $registered_at, $documents, $addresses, $phones) {
        $this->tax_identifier = $tax_identifier;
        $this->name = $name;
        $this->email = $email;
        $this->birthdate = $this->convert_date_format($birthdate);
        $this->registered_at = date('c', strtotime($registered_at));
        $this->documents = $documents;
        $this->addresses = $addresses;
        $this->phones = $phones;
    }

    public function get_tax_identifier() { return $this->tax_identifier; }
    public function get_name() { return $this->name; }
    public function get_email() { return $this->email; }
    public function get_birthdate() { return $this->birthdate; }
    public function get_registered_at() { return $this->registered_at; }
    public function get_documents() { return $this->documents; }
    public function get_addresses() { return $this->addresses; }
    public function get_phones() { return $this->phones; }

    private function convert_date_format($date) {
        return date('Y-m-d', strtotime($date));
    }

    public function to_array() {
        $tmp_documents = array_map(function($document) {
            return $document->to_array();
        }, $this->get_documents());

        $tmp_addresses = array_map(function($address) {
            return $address->to_array();
        }, $this->get_addresses());

        $data = array(
            'name' => $this->get_name(),
            'email' => $this->get_email(),
            'registered_at' => $this->get_registered_at(),
            'documents' => $tmp_documents,
            'addresses' => $tmp_addresses,
        );

        if ($this->get_birthdate()) {
            $data['birth_date'] = $this->get_birthdate();
        }

        if ($this->get_phones()) {
            $data['phones'] = array_map(function($phone) {
                return $phone->to_array();
            }, $this->get_phones());
        }

        return $data;
    }
}