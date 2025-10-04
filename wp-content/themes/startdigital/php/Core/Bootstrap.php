<?php

namespace TheStart\Core;

class Bootstrap
{
    public function __construct()
    {
        new TimberBootstrap();
        new HelperLoader();

        if (class_exists('Timber')) {
            new TheStart();
        }
    }
}
