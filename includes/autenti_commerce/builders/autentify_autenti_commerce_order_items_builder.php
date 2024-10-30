<?php

class Autentify_Autenti_Commerce_Order_Items_Builder {
    private $order;

    public function __construct($order) {
        $this->order = $order;
    }

    public function build() {
        $items = [];
        $order_items = $this->order->get_items();

        foreach ($order_items as $item_id => $item) {
            $product = $item->get_product();
            $quantity = $item->get_quantity();
            $code = empty($product->get_sku()) ? (string) $product->get_id() : $product->get_sku();
            $total = (string) ($item->get_total() / $quantity);

            for ($i = 0; $i < $quantity; $i++) {
                $items[] = new Autentify_Autenti_Commerce_Item($code, $item->get_name(), $total);
            }
        }

        return $items;
    }
}