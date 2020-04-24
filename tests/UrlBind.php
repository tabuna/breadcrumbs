<?php

declare(strict_types=1);

namespace Tabuna\Breadcrumbs\Tests;

use Illuminate\Contracts\Routing\UrlRoutable;

class UrlBind implements UrlRoutable
{

    public function getRouteKey()
    {
        return 'bind';
    }

    public function getRouteKeyName()
    {
        return 'bind';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return  $value + $value;
    }

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        return 'bind';
    }
}
