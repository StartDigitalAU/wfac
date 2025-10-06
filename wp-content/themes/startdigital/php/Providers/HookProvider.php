<?php

namespace TheStart\Providers;

/**
 * Abstract Hook Provider
 * All hook providers extend this
 */
abstract class HookProvider
{
    /**
     * Register all hooks for this provider
     */
    abstract public function register(): void;

    /**
     * Helper to add action hook
     */
    protected function addAction(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        add_action($hook, $callback, $priority, $acceptedArgs);
    }

    /**
     * Helper to add filter hook
     */
    protected function addFilter(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        add_filter($hook, $callback, $priority, $acceptedArgs);
    }
}
