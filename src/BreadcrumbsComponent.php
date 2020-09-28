<?php

declare(strict_types=1);

namespace Tabuna\Breadcrumbs;

use Illuminate\View\Component;
use Illuminate\Support\Collection;

class BreadcrumbsComponent extends Component
{
    /**
     * @var Manager
     */
    public $breadcrumbs;

    /**
     * @var string|null
     */
    public $route;

    /**
     * @var mixed|null
     */
    public $parameters;

    /**
     * @var string|null
     */
    public $class;

    /**
     * @var string|null
     */
    public $active;

    /**
     * Create a new component instance.
     *
     * @param Manager     $manager
     * @param string|null $route
     * @param mixed|null  $parameters
     * @param string|null $class
     * @param string|null $active
     */
    public function __construct(
        Manager $manager,
        string $route = null,
        $parameters = null,
        string $class = null,
        string $active = null
    )
    {
        $this->breadcrumbs = $manager;
        $this->route = $route;
        $this->parameters = $parameters;
        $this->class = $class;
        $this->active = $active;
    }

    /**
     * @return Collection
     * @throws \Throwable
     */
    public function generate(): Collection
    {
        if ($this->route !== null) {
            return $this->breadcrumbs->generate($this->route, $this->parameters);
        }

        return $this->breadcrumbs->current($this->parameters);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return $this->breadcrumbs->has($this->route)
            ? view('breadcrumbs::breadcrumbs')
            : '';
    }
}
