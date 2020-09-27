<?php

declare(strict_types=1);

namespace Tabuna\Breadcrumbs\Tests;

use Illuminate\Support\ServiceProvider;

class TestServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'test_breadcrumbs');
    }
}
