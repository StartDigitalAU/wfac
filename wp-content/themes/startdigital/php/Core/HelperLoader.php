<?php

namespace TheStart\Core;

class HelperLoader
{
    public function __construct()
    {
        $this->load_helpers();
    }

    private function load_helpers()
    {
        $helpers = glob(get_template_directory() . '/helpers/*/*.php');
        foreach ($helpers as $helper) {
            if (file_exists($helper)) {
                include_once $helper;
            }
        }
    }
}
