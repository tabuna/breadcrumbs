<?php

declare(strict_types=1);

namespace Tabuna\Breadcrumbs;

use Closure;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Laravel\SerializableClosure\SerializableClosure;

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

        Route::macro('breadcrumbs', function (Closure $closure) {
            /** @var Route $this */
            $this->middleware('breadcrumbs')
                ->defaults(BreadcrumbsMiddleware::class, serialize(new SerializableClosure($closure)));

            return $this;
        });
    }
}
