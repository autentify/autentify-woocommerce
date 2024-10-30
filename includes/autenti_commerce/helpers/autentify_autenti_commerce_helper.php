<?php

class Autentify_Autenti_Commerce_Helper {
    private static $instance;

    private function __construct() {}

    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new Autentify_Autenti_Commerce_Helper();
        }

        return self::$instance;
    }

    public function get_autenti_face_formatted_status($status) {
        switch ($status) {
            case "autenti_face":
                return "AutentiFace";
            case "autenti_face_error":
            case "autenti_face_phone_error":
                return "Erro - AutentiFace)";
        }
    }

    public function get_formatted_status($autenti_commerce, $use_autenti_face, $useHtml) {
        if ($autenti_commerce->is_initialized()) {
            return "Inicializada";
        } elseif ($autenti_commerce->is_waiting() || (!$use_autenti_face && $autenti_commerce->is_autenti_face())) {
            return "Em Análise";
        } elseif ($autenti_commerce->is_approved()) {
            return "Aprovada";
        } elseif ($autenti_commerce->is_rejected()) {
            return "Reprovada";
        } elseif ($autenti_commerce->is_autenti_face()) {
            return Autentify_Autenti_Commerce_Helper::get_instance()->get_autenti_face_formatted_status(
                $autenti_commerce->get_status()
            );
        } elseif ($autenti_commerce->is_error()) {
            return "Erro";
        }
    }

    public function get_autenti_face_status_css_class($status) {
        switch ($status) {
            case "autenti_face":
                return "";
            case "autenti_face_error":
            case "autenti_face_phone_error":
                return "status-warning";
        }
    }

    public function get_status_css_class($autenti_commerce, $use_autenti_face, $useHtml) {
        if ($autenti_commerce->is_initialized()) {
            return "";
        } elseif ($autenti_commerce->is_waiting() || (!$use_autenti_face && $autenti_commerce->is_autenti_face())) {
            return "";
        } elseif ($autenti_commerce->is_approved()) {
            return "status-success";
        } elseif ($autenti_commerce->is_rejected()) {
            return "status-danger";
        } elseif ($autenti_commerce->is_autenti_face()) {
            return Autentify_Autenti_Commerce_Helper::get_autenti_face_status_css_class(
                $autenti_commerce->get_status()
            );
        } elseif ($autenti_commerce->is_error()) {
            return "status-warning";
        }
    }

    public function get_status_html($autenti_commerce) {
        $helper = Autentify_Autenti_Commerce_Helper::get_instance();

        $status_css_class = $helper->get_status_css_class($autenti_commerce, true, false);
        $formatted_status = $helper->get_formatted_status($autenti_commerce, true, false);
        $html_score = "<mark class=\"autentify-analysis-status $status_css_class\"><span>"
                . "$formatted_status</span></mark>";
        return $html_score;
    }
}