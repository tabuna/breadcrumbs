<p align="center">
    <img src="https://raw.githubusercontent.com/tabuna/breadcrumbs/master/laravel-breadcrumbs.svg" height="58px;" alt="Laravel Breadcrumbs">
</p>


<p align="center">
<img src="https://github.com/tabuna/breadcrumbs/workflows/run-tests/badge.svg" alt="Tests">
<a href="https://codecov.io/gh/tabuna/breadcrumbs"><img src="https://codecov.io/gh/tabuna/breadcrumbs/branch/master/graph/badge.svg" alt="codecov"></a>
<a href="https://packagist.org/packages/tabuna/breadcrumbs"><img src="https://img.shields.io/packagist/dt/tabuna/breadcrumbs.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/tabuna/breadcrumbs"><img src="https://img.shields.io/packagist/v/tabuna/breadcrumbs.svg" alt="Latest Version on Packagist"></a>
</p>


## Introduction

Breadcrumbs display a list of links indicating the position of the current page in the whole site hierarchy. For example, breadcrumbs like `Home / Sample Post / Edit` means the user is viewing an edit page for the "Sample Post." He can click on "Sample Post" to view that page or click on "Home" to return to the homepage.


> [Home](#) / [Sample Post](#) / Edit

This package for the [Laravel framework](https://laravel.com/) will make it easy to build breadcrumbs in your application.

## Installation

Run this at the command line:
```php
$ composer require tabuna/breadcrumbs
```
This will update `composer.json` and install the package into the `vendor/` directory.

## Define your breadcrumbs

Now you can define breadcrumbs directly in the route files:

```php
use Tabuna\Breadcrumbs\Trail;

// Home
Route::get('/', fn () => view('home'))
    ->name('home')
    ->breadcrumbs(fn (Trail $trail) =>
        $trail->push('Home', route('home'))
);

// Home > About
Route::get('/about', fn () => view('home'))
    ->name('about')
    ->breadcrumbs(fn (Trail $trail) =>
        $trail->parent('home')->push('About', route('about'))
);
```

You can also get arguments from the request:

```php
Route::get('/category/{category}', function (Category $category){
    //In this example, the category object is your Eloquent model.
    //code...
})
    ->name('category')
    ->breadcrumbs(fn (Trail $trail, Category $category) =>
        $trail->push($category->title, route('category', $category->id))
);
```

## Like to use a separate route file?

You can do this simply by adding the desired file to the service provider

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BreadcrumbsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        require base_path('routes/breadcrumbs.php');
    }
}
```

Then it will be your special file in the route directory:

```php
// routes/breadcrumbs.php


// Photos
Breadcrumbs::for('photo.index', fn (Trail $trail) =>
    $trail->parent('home')->push('Photos', route('photo.index'))
);
```



## Route resource

When using resources, a whole group of routes is declared for which you must specify values manually

```php
// routes/web.php

Route::resource('photos', 'PhotoController');
````

It’s better to specify this in service providers, since route files can be cached

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Tabuna\Breadcrumbs\Breadcrumbs;
use Tabuna\Breadcrumbs\Trail;

class BreadcrumbsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Breadcrumbs::for('photos.index', fn (Trail $trail) =>
             $trail->push('Photos', route('home'))
        );
        
        Breadcrumbs::for('photos.create', fn (Trail $trail) =>
            $trail
                ->parent('photos.index', route('photos.index'))
                ->push('Add new photo', route('home'))
        );
    }
}
```

## Output the breadcrumbs use Blade Component

You can use the output component:

```blade
<ul>
    <x-tabuna-breadcrumbs/>
</ul>
```

To define classes of list items, you can specify:

```blade
<x-tabuna-breadcrumbs
  class="item"
  active="active"
/>
```

You can also pass parameters:

```blade
<x-tabuna-breadcrumbs
    parameters="['value 1', 'value 2', 'value 3']"
/>
```

And call named routes explicitly:

```blade
<x-tabuna-breadcrumbs
    route="static"
/>
```

## Output the breadcrumbs use Blade view

In order to display breadcrumbs on the desired page, simply call:

```blade
@if(Breadcrumbs::has())
    @foreach (Breadcrumbs::current() as $crumbs)
        @if ($crumbs->url() && !$loop->last)
            <li class="breadcrumb-item">
                <a href="{{ $crumbs->url() }}">
                    {{ $crumbs->title() }}
                </a>
            </li>
        @else
            <li class="breadcrumb-item active">
                {{ $crumbs->title() }}
            </li>
        @endif
    @endforeach
@endif
```

And results in this output:

> [Home](#) / About

## Credits

For several years, I successfully used the [Dave James Miller](https://github.com/davejamesmiller/laravel-breadcrumbs) package to solve my problems, but he stopped developing and supporting it. After a long search for alternatives, I liked the [Dwight Watson](https://github.com/dwightwatson) package, but the isolation of breadcrumbs from the announcement of the routes did not give me rest. That's why I created this package. It uses the code of both previous packages.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
