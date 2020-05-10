<?php

declare(strict_types=1);

namespace Tabuna\Breadcrumbs;

use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use function Opis\Closure\serialize;

class BreadcrumbsServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Manager::class);

        \Illuminate\Support\Facades\Route::middlewareGroup('breadcrumbs', [
            BreadcrumbsMiddleware::class,
        ]);

        if (Route::hasMacro('breadcrumbs')) {
            return;
        }

        Route::macro('breadcrumbs', function (callable $closure) {
            $this->middleware('breadcrumbs')
                ->defaults(BreadcrumbsMiddleware::class, serialize($closure));

            return $this;
        });
    }
}
