<?php

declare(strict_types=1);

namespace Tabuna\Breadcrumbs;

use Closure;
use Illuminate\Contracts\Routing\Registrar as Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class Trail
{
    /**
     * The breadcrumb trail.
     *
     * @var Collection
     */
    protected $breadcrumbs;

    /**
     * Create a new instance of the generator.
     *
     * @param Router    $router
     * @param Registrar $registrar
     */
    public function __construct(
        protected Router $router,
        protected Registrar $registrar
    ) {
        $this->breadcrumbs = new Collection;
    }

    /**
     * Register a definition with the registrar.
     *
     * @param string  $name
     * @param Closure $definition
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function register(string $name, Closure $definition): void
    {
        $this->registrar->set($name, $definition);
    }

    /**
     * Generate the collection of breadcrumbs from the given route.
     *
     * @param string $route
     * @param array  $parameters
     *
     * @throws \Throwable
     *
     * @return Collection
     */
    public function generate(string $route, array $parameters = []): Collection
    {
        $this->breadcrumbs = $this->breadcrumbs->whenNotEmpty(function () {
            return new Collection();
        });

        $parameters = $this->getRouteByNameParameters($route, $parameters);

        if ($route && $this->registrar->has($route)) {
            $this->call($route, $parameters);
        }

        return $this->breadcrumbs;
    }

    /**
     * @param string $name
     * @param array  $parameters
     *
     * @return array
     */
    private function getRouteByNameParameters(string $name, array $parameters): array
    {
        if (! empty($parameters)) {
            return $parameters;
        }

        $route = Route::currentRouteName() === $name
            ? Route::current()
            : Route::getRoutes()->getByName($name);

        return optional($route)->parameters ?? $parameters;
    }

    /**
     * Call the breadcrumb definition with the given parameters.
     *
     * @param string $name
     * @param array  $parameters
     *
     * @throws \Throwable
     *
     * @return void
     */
    protected function call(string $name, array $parameters): void
    {
        $definition = $this->registrar->get($name);

        $parameters = Arr::prepend(array_values($parameters), $this);

        call_user_func_array($definition, $parameters);
    }

    /**
     * Call a parent route with the given parameters.
     *
     * @param string $name
     * @param mixed  $parameters
     *
     * @throws \Throwable
     *
     * @return Trail
     */
    public function parent(string $name, ...$parameters): self
    {
        $this->call($name, $parameters);

        return $this;
    }

    /**
     * Add a breadcrumb to the collection.
     *
     * @param string      $title
     * @param string|null $url
     *
     * @return Trail
     */
    public function push(string $title, ?string $url = null): self
    {
        $this->breadcrumbs->push(new Crumb($title, $url));

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return $this->registrar->has($name);
    }
}
