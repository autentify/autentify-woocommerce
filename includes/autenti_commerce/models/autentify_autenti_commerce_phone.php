<?php

class Autentify_Autenti_Commerce_Phone {
    private $kind;
    private $country;
    private $area;
    private $number;

    public function __construct($kind, $country, $formatted_number) {
        $this->kind = $kind;
        $this->country = $country;

        $area_and_number = $this->extract_phone_details($formatted_number);

        $this->area = $area_and_number['area'];
        $this->number = $area_and_number['number'];
    }

    public function get_kind() { return $this->kind; }
    public function get_country() { return $this->country; }
    public function get_area() { return $this->area; }
    public function get_number() { return $this->number; }

    private function extract_phone_details($phone) {
        preg_match('/\((\d{2})\)\s*(\d{4,5})-(\d{4})/', $phone, $matches);

        if (count($matches) > 0) {
            $area = $matches[1];
            $number = $matches[2] . $matches[3];

            return [
                'area' => (int) $area,
                'number' => (int) $number
            ];
        }

        return null;
    }

    public function to_array() {
        return array(
            'kind' => $this->get_kind(),
            'country' => $this->get_country(),
            'area' => $this->get_area(),
            'number' => $this->get_number()
        );
    }
}