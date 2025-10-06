<?php

namespace TheStart\Core;

use Timber\Site;
use TheStart\Core\Assets;
use TheStart\Core\Context;
use TheStart\Core\ThemeSupport;
use TheStart\PostTypes\PostTypeManager;
use TheStart\Taxonomies\TaxonomyManager;
use TheStart\Features\WooCommerce\ProductManager;
use TheStart\Providers\HookProvider;

class TheStart extends Site
{
    /**
     * @var HookProvider[]
     */
    protected array $providers = [];

    public function __construct()
    {
        new Assets();
        new Context();
        new ThemeSupport();
        new PostTypeManager();
        new TaxonomyManager();
        new ProductManager();

        $this->registerProviders();

        parent::__construct();
    }

    /**
     * Register all hook providers
     */
    private function registerProviders(): void
    {
        $this->providers = $this->getProviders();

        foreach ($this->providers as $provider) {
            $provider->register();
        }
    }

    /**
     * Get all provider instances
     * 
     * @return HookProvider[]
     */
    private function getProviders(): array
    {
        return [];
    }
}
