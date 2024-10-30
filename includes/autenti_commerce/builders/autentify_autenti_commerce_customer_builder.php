<?php

class Autentify_Autenti_Commerce_Customer_Builder {
    private $order;
    private $tax_identifier;

    public function __construct($order, $tax_identifier) {
        $this->order = $order;
        $this->tax_identifier = $tax_identifier;
    }

    public function build() {
        $billing_first_name = $this->order->get_billing_first_name();
        $billing_last_name = $this->order->get_billing_last_name();
        $billing_email = $this->order->get_billing_email();
        $birthdate = get_post_meta($this->order->get_id(), '_billing_birthdate', true);
        $formatted_phone = get_post_meta($this->order->get_id(), '_billing_phone', true);
        
        $name = "$billing_first_name $billing_last_name";
        $documents = [new Autentify_Autenti_Commerce_Document('individual_tax', $this->tax_identifier)];

        $addresses = [
            new Autentify_Autenti_Commerce_Address(
                "residential",
                $this->order->get_billing_address_1(),
                $this->order->get_billing_city(),
                $this->order->get_billing_state(),
                $this->order->get_billing_country(),
                $this->order->get_billing_postcode(),
                $this->order->get_billing_address_2()
            )
        ];

        $phones = [
            new Autentify_Autenti_Commerce_Phone(
                "cellular",
                55,
                $formatted_phone
            )
        ];

        return new Autentify_Autenti_Commerce_Customer(
            $this->tax_identifier, $name, $billing_email, $birthdate,
            $this->order->get_date_created(), $documents, $addresses, $phones
        );

        return $customer_data;
    }
}