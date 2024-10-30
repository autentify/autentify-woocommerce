<?php

class Autentify_Autenti_Commerce {
    private $id;
    private $tax_identifier;
    private $order;
    private $customer;
    private $status;
    private $created_at;
    private $updated_at;
    private $status_updated_at;

    public function __construct($tax_identifier, $order, $customer) {
        $this->tax_identifier = $tax_identifier;
        $this->order = $order;
        $this->customer = $customer;
    }

    /**
     * Instances an Autentify_Autenti_Commerce using the encoded JSON.
     * @param stdClass The object must contain the encoded JSON with the
     * Autentify_Autenti_Commerce attribute names.
     * @return Autentify_Autenti_Commerce The created instance.
     */
    public static function with_encoded_json( $encoded_json ) {
        $order = null; // build
        $customer = null; // build
        $instance = new self( $encoded_json->tax_identifier, $order, $customer );

        $instance->id = $encoded_json->id;
        $instance->tax_identifier = $encoded_json->tax_identifier;
        $instance->status = $encoded_json->status;
        $instance->created_at = $encoded_json->created_at;
        $instance->updated_at = $encoded_json->updated_at;
        
        if ( isset( $encoded_json->status_updated_at ) ) {
            $instance->status_updated_at = $encoded_json->status_updated_at;
        } else {
            $instance->status_updated_at = null;
        }

        return $instance;
    }

    /**
     * Creates an Array using the attributes names as keys and attribute values as values.
     * @return Array The Array with the keys and values.
     */
    public function to_json() {
        $autenti_mail_in_json = [
            "id" => $this->id,
            "tax_identifier" => $this->tax_identifier
        ];

        $autenti_mail_in_json["status"] = $this->get_status();
        
        $helper = Autentify_Autenti_Commerce_Helper::get_instance();
        $autenti_mail_in_json["status_html"] = $helper->get_status_html($this);

        return $autenti_mail_in_json;
    }

    /**
     * Creates the check button in HTML for orders page. The check button only is enabled
     * if the tax_identifier is isset and it is not empty.
     * @return String The values contains the button HTML.
     */
    public function get_check_btn_in_html( $order_id ) {
        $check_btn_with_tax_identifier = "<a href='#' class='button button-primary'"
            . "onclick='postAutentiCommerce(\"$order_id\")'>"
            . "Consultar</a>";

        return $check_btn_with_tax_identifier;
    }

    /**
     * Calls is_valid method from Autentify_Cpf_Helper to validate
     * the cpf attribute.
     * @return Boolean The value is true if the cpf is valid.
     */
    public function has_valid_cpf() {
        return Autentify_Cpf_Helper::get_instance()->is_valid( $this->tax_identifier );
    }

    public function get_tax_identifier() {
        return $this->tax_identifier;
    }

    public function is_valid() {
        return false;
    }

    public function is_initialized() {
        $statuses = [
            "undefined", "received", "initial", "no_analysis"
        ];

        return in_array($this->get_status(), $statuses);
    }

    public function is_waiting() {
        $statuses = [
            "not_identified", "waiting_for_analysis",
            "under_analysis", "waiting_positive_feedback"
        ];

        return in_array($this->get_status(), $statuses);
    }

    public function is_approved() {
        $statuses = ["approved"];

        return in_array($this->get_status(), $statuses);
    }

    public function is_rejected() {
        $statuses = [
            "rejected", "rejected_suspected", "rejected_fraud_confirmed", "loss",
            "automatically_rejected", "rejected_excluded", "declined_credit"
        ];

        return in_array($this->get_status(), $statuses);
    }

    public function is_autenti_face() {
        $statuses = [
            "autenti_face", "autenti_face_error",
            "autenti_face_phone_error"
        ];

        return in_array($this->get_status(), $statuses);
    }

    public function is_autenti_face_error() {
        $statuses = [
            "autenti_face_error", "autenti_face_phone_error"
        ];

        return in_array($this->get_status(), $statuses);
    }

    public function is_error() {
        $statuses = ["error"];

        return in_array($this->get_status(), $statuses);
    }

    public function can_update_status() {
        if ($this->is_approved() || $this->is_rejected() || $this->is_autenti_face_error()) {
            return false;
        }

        if ($this->status_updated_at == null) {
            return true;
        }

        $status_updated_at_date_time = new DateTime($this->status_updated_at);

        $created_at_date_time = new DateTime($this->created_at);
        $created_at_date_time->setTimezone(new DateTimeZone('America/Sao_Paulo'));

        $interval = $created_at_date_time->diff($status_updated_at_date_time);

        if ($interval->days > 2 || ($interval->days === 2 && $interval->h > 0) ||
                ($interval->days === 2 && $interval->h === 0 && $interval->i > 0) ||
                ($interval->days === 2 && $interval->h === 0 && $interval->i === 0 && $interval->s > 0)) {
            return false;
        }

        return true;
    }

    public function get_id() {
        return $this->id;
    }

    public function get_status() {
        return $this->status;
    }

    public function update_status_updated_at() {
        $current_date_time = new DateTime();
        $current_date_time->setTimezone(new DateTimeZone('America/Sao_Paulo'));
        $this->status_updated_at = $current_date_time->format('Y-m-d\TH:i:s.vP');
    }

    public function to_std_class() {
        $stdClass = new stdClass();
        $stdClass->id = $this->get_id();
        $stdClass->tax_identifier = $this->get_tax_identifier();
        $stdClass->status = $this->get_status();
        $stdClass->created_at = $this->created_at;
        $stdClass->updated_at = $this->updated_at;
        $stdClass->status_updated_at = $this->status_updated_at;

        return $stdClass;
    }
}