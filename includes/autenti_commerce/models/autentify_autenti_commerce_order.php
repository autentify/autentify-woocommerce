<?php

class Autentify_Autenti_Commerce_Order {
    private $category;
    private $ip_address;
    private $shipping_address;
    private $items;
    private $payments;

    public function __construct($category, $ip_address, $shipping_address, $items, $payments) {
        $this->category = $category;
        $this->ip_address = $ip_address;
        $this->shipping_address = $shipping_address;
        $this->items = $items;
        $this->payments = $payments;
    }

    public function get_category() { return $this->category; }
    public function get_ip_address() { return $this->ip_address; }
    public function get_shipping_address() { return $this->shipping_address; }
    public function get_items() { return $this->items; }
    public function get_payments() { return $this->payments; }

    public function to_array() {
        $data = [
            'category' => 'Genérica',
            'ip_address' => $this->get_ip_address(),
            'shipping_address' => [$this->get_shipping_address()],
            'items' => $this->get_items(),
            'payments' => $this->get_payments()
        ];

        return $data;
    }
}