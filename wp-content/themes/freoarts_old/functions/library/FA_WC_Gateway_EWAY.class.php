<?php

if (!class_exists('FA_WC_Gateway_EWAY') && class_exists('WC_Gateway_EWAY')) {

    class FA_WC_Gateway_EWAY extends WC_Gateway_EWAY
    {

        public function public_request_access_code($order) {

            return $this->request_access_code($order);
        }

        public function public_set_token_customer_id($order, $token_customer_id) {

            $this->set_token_customer_id($order, $token_customer_id);
        }
    }
}