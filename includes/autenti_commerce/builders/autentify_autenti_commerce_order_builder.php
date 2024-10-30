<?php

class Autentify_Autenti_Commerce_Order_Builder {
    private $order;
    private $items_builder;
    private $payment;
    private $shipping_address;

    public function __construct($order, $billing_address, $customer, $tax_identifier) {
        $this->order = $order;
        $this->items_builder = new Autentify_Autenti_Commerce_Order_Items_Builder($this->order);

        $this->payment = new Autentify_Autenti_Commerce_Payment(
            $order->get_total(),
            '1234',
            $customer->get_name(),
            $tax_identifier
        );

        $shipping_address_builder = new Autentify_Autenti_Commerce_Shipping_Address_Builder($this->order, $billing_address);
        $this->shipping_address = $shipping_address_builder->build($billing_address);
    }

    public function build() {
        $items = array_map(function($item) {
            return $item->to_array();
        }, $this->items_builder->build($this->order));

        $payments = array(
            $this->payment->to_array()
        );

        return array(
            'category' => 'Genérica',
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'shipping_address' => $this->shipping_address->to_array(),
            'items' => $items,
            'payments' => $payments
        );
    }
}