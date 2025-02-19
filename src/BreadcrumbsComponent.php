<?php

declare(strict_types=1);

namespace Tabuna\Breadcrumbs;

use Illuminate\Support\Collection;
use Illuminate\View\Component;

class BreadcrumbsComponent extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public Manager $manager,
        public ?string $route = null,
        public $parameters = null,
        public ?string $class = null,
        public ?string $active = null,
        public bool $escape = true,
    ) {}

    /**
     * @throws \Throwable
     *
     * @return Collection
     */
    public function generate(): Collection
    {
        if ($this->route !== null) {
            return $this->manager->generate($this->route, $this->parameters);
        }

        return $this->manager->current($this->parameters);
    }

    /**
     * Determine if the component should be rendered.
     *
     * @return bool
     */
    public function shouldRender(): bool
    {
        return $this->manager->has($this->route);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('breadcrumbs::breadcrumbs');
    }

    /**
     * @param Crumb $crumb
     *
     * @return string|null
     */
    public function title(Crumb $crumb): ?string
    {
        $title = $crumb->title();

        return $this->escape ? e($title) : $title;
    }
}
