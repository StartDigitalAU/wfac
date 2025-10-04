<?php

namespace TheStart\Core;

use Timber\Site;
use TheStart\Core\Assets;
use TheStart\Core\Context;
use TheStart\Core\ThemeSupport;
use TheStart\PostTypes\PostTypeManager;
use TheStart\Taxonomies\TaxonomyManager;
use TheStart\Features\WooCommerce\ProductManager;


class TheStart extends Site
{
    public function __construct()
    {
        new Assets();
        new Context();
        new ThemeSupport();
        new PostTypeManager();
        new TaxonomyManager();
        new ProductManager();

        parent::__construct();
    }
}
