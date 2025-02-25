<?php

declare(strict_types=1);

namespace Tabuna\Breadcrumbs\Tests;

use Orchestra\Testbench\TestCase as BaseCase;
use Tabuna\Breadcrumbs\Breadcrumbs;
use Tabuna\Breadcrumbs\BreadcrumbsServiceProvider;

class TestCase extends BaseCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            BreadcrumbsServiceProvider::class,
            TestServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app): array
    {
        return [
            'Breadcrumbs' => Breadcrumbs::class,
        ];
    }
}
