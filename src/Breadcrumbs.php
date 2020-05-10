<?php

declare(strict_types=1);

namespace Tabuna\Breadcrumbs;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * Class Breadcrumbs.
 *
 * @method static bool has(string $name = null)
 * @method static Collection current()
 * @method static void for(string $route, \Closure $definition)
 */
class Breadcrumbs extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Manager::class;
    }
}
