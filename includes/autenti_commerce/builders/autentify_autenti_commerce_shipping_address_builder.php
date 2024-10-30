<?php

class Autentify_Autenti_Commerce_Shipping_Address_Builder {
    private $order;
    private $billing_address;

    public function __construct($order, $billing_address) {
        $this->order = $order;
        $this->billing_address = $billing_address;
    }

    public function build() {
        $shipping_values = [
            $this->order->get_shipping_address_1(),
            $this->order->get_shipping_city(),
            $this->order->get_shipping_state(),
            $this->order->get_shipping_country(),
            $this->order->get_shipping_postcode(),
        ];

        if (empty(array_filter($shipping_values))) {
            return $this->billing_address;
        }

        return new Autentify_Autenti_Commerce_Address(
            "residential",
            $this->order->get_shipping_address_1(),
            $this->order->get_shipping_city(),
            $this->order->get_shipping_state(),
            $this->order->get_shipping_country(),
            $this->order->get_shipping_postcode(),
            $this->order->get_shipping_address_2()
        );
    }
}