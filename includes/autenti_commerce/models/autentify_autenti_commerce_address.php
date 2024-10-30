<?php

class Autentify_Autenti_Commerce_Address {
    private $kind;
    private $street;
    private $city;
    private $state;
    private $country;
    private $postal_code;
    private $complement;

    public function __construct($kind, $street, $city, $state, $country, $postal_code, $complement) {
        $this->kind = $kind;
        $this->street = $street;
        $this->city = $city;
        $this->state = $state;
        $this->country = $country;
        $this->postal_code = preg_replace('/\D/', '', $postal_code);
        $this->complement = $complement;
    }

    public function get_kind() { return $this->kind; }
    public function get_street() { return $this->street; }
    public function get_city() { return $this->city; }
    public function get_state() { return $this->state; }
    public function get_country() { return $this->country; }
    public function get_postal_code() { return $this->postal_code; }
    public function get_complement() { return $this->complement; }

    public function to_array() {
        $data = array(
            'kind' => 'residential',
            'street' => $this->get_street(),
            'number' => 'N/A',
            'city' => $this->get_city(),
            'state_abbreviation' => $this->get_state(),
            'country' => $this->get_country(),
            'postal_code' => $this->get_postal_code(),
        );

        if ($this->get_complement()) {
            $data['complement'] = $this->get_complement();
        }

        return $data;
    }
}