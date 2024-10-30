<?php

class Autentify_Autenti_Commerce_Data_Builder {
    private $order;

    public function __construct($order_id) {
        $this->order = wc_get_order($order_id);

        if (!$this->order) {
            throw new Exception("Order not found.");
        }
    }

    public function build() {
        $tax_identifier = get_post_meta($this->order->get_id(), '_billing_cpf', true);
        $customer_builder = new Autentify_Autenti_Commerce_Customer_Builder($this->order, $tax_identifier);
        $customer = $customer_builder->build();

        $order_builder = new Autentify_Autenti_Commerce_Order_Builder(
            $this->order, $customer->get_addresses()[0], $customer, $tax_identifier
        );

        $array_data = array(
            'external_order_id' => (string) $this->order->get_id(),
            'tax_identifier' => $tax_identifier,
            'customer' => $customer_builder->build()->to_array(),
            'order' => $order_builder->build()
        );

        return $array_data;
    }
}