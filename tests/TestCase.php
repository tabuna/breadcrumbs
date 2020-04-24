<?php

declare(strict_types=1);

namespace Tabuna\Breadcrumbs\Tests;

use Orchestra\Testbench\TestCase as BaseCase;
use Tabuna\Breadcrumbs\Breadcrumbs;
use Tabuna\Breadcrumbs\BreadcrumbsServiceProvider;
use Tabuna\Breadcrumbs\Trail;


class TestCase extends BaseCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();


        /* Install application */
        $this->loadLaravelMigrations();
        $this->withFactories(__DIR__ . '/factories');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $config = config();
        $config->set('app.debug', true);

        // set up database configuration
        $config->set('database.connections.breadcrumbs', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $config->set('database.default', 'breadcrumbs');
    }


    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            BreadcrumbsServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Breadcrumbs' => Breadcrumbs::class,
        ];
    }
}
