<?php

declare(strict_types=1);

namespace Tabuna\Breadcrumbs;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use function Opis\Closure\serialize;

class BreadcrumbsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap your package's services.
     */
    public function boot()
    {
        Blade::component('tabuna-breadcrumbs', BreadcrumbsComponent::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Manager::class);
        $this->loadViewsFrom(__DIR__ . '/../views', 'breadcrumbs');

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
