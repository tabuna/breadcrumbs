<?php

declare(strict_types=1);

namespace Tabuna\Breadcrumbs\Tests;

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Breadcrumbs;
use Tabuna\Breadcrumbs\Trail;

class BreadcrumbsTest extends TestCase
{
    public function testBreadcrumbsDefined(): void
    {
        Route::get('/breadcrumbs-home', function () {
            return Breadcrumbs::current()->toJson();
        })->name('breadcrumbs-home');

        Breadcrumbs::for('breadcrumbs-home', function (Trail $trail) {
            return $trail->push('Home', 'http://localhost/');
        });

        $this->get('/breadcrumbs-home')
            ->assertJson([
                [
                    'title' => 'Home',
                    'url'   => 'http://localhost/',
                ],
            ]);
    }

    public function testBreadcrumbsUndefined(): void
    {
        Route::get('/undefined', function () {
            return Breadcrumbs::current()->toJson();
        })->name('breadcrumbs-home');

        $this->get('/undefined')->assertOk();
    }

    public function testBreadcrumbsParent(): void
    {

        Route::get('/', function () {
        })
            ->name('home')
            ->breadcrumbs(function (Trail $trail) {
                $trail->push('Home', route('home'));
            });

        Route::get('/about', function () {
            return Breadcrumbs::current()->toJson();
        })
            ->name('about')
            ->breadcrumbs(function (Trail $trail) {
                return $trail->parent('home')->push('About', route('about'));
            });

        $this->get('/about')
            ->assertJson([
                [
                    'title' => 'Home',
                    'url'   => 'http://localhost',
                ],
                [
                    'title' => 'About',
                    'url'   => 'http://localhost/about',
                ],
            ]);
    }

    public function testBreadcrumbsRoute(): void
    {
        Route::get('breadcrumbs-about-test', function () {
            return Breadcrumbs::current()->toJson();
        })
            ->name('breadcrumbs.about')
            ->breadcrumbs(function (Trail $trail) {
                return $trail->push('About', \route('breadcrumbs.about'));
            });

        $this->get('breadcrumbs-about-test')
            ->assertJson([
                [
                    'title' => 'About',
                    'url'   => 'http://localhost/breadcrumbs-about-test',
                ],
            ]);
    }

    public function testBreadcrumbsParameters(): void
    {
        $random = random_int(10, 100);

        Route::get('breadcrumbs-about-test/{bind}', function (UrlBind $bind) {
            $bind->getRouteKey();

            return Breadcrumbs::current()->toJson();
        })
            ->middleware(SubstituteBindings::class)
            ->name('breadcrumbs.about')
            ->breadcrumbs(function (Trail $trail, $bind) {
                return $trail->push('Sum', $bind);
            });

        $this->get(\route('breadcrumbs.about', $random))
            ->assertJson([
                [
                    'title' => 'Sum',
                    'url'   => $random + $random,
                ],
            ]);
    }

    public function testBreadcrumbsParamsForCurrent(): void
    {
        $params = ['value 1', 'value 2', 'value 3'];

        Route::get('breadcrumbs-about-test', function (UrlBind $bind) use ($params) {
            $bind->getRouteKey();

            return Breadcrumbs::current($params)->toJson();
        })
            ->middleware(SubstituteBindings::class)
            ->name('breadcrumbs.about')
            ->breadcrumbs(function (Trail $trail, $value, $value2, $value3) {
                return $trail
                    ->push('Arguments', $value)
                    ->push('Arguments2', $value2)
                    ->push('Arguments3', $value3);
            });

        $this->get(\route('breadcrumbs.about', $params))
            ->assertJson([
                [
                    'title' => 'Arguments',
                    'url'   => $params[0],
                ],
                [
                    'title' => 'Arguments2',
                    'url'   => $params[1],
                ],
                [
                    'title' => 'Arguments3',
                    'url'   => $params[2],
                ],
            ]);
    }

    public function testBreadcrumbsForOtherRoute(): void
    {
        Route::get('/breadcrumbs-home', function () {
            return null;
        })->name('breadcrumbs-home');

        Route::get('/breadcrumbs-home-other', function () {
            return Breadcrumbs::generate('breadcrumbs-home')->toJson();
        })->name('breadcrumbs-home');

        Breadcrumbs::for('breadcrumbs-home', function (Trail $trail) {
            return $trail->push('Home', 'http://localhost/');
        });

        $this->get('/breadcrumbs-home-other')->assertJson([
            [
                'title' => 'Home',
                'url'   => 'http://localhost/',
            ],
        ]);
    }

    public function testBreadcrumbsOverwrite(): void
    {
        $domains = [
            '127.0.0.1',
            'localhost',
            'foo.com',
            'bar.com',
        ];

        foreach ($domains as $domain) {
            Route::domain($domain)
                ->name('breadcrumbs-overwrite')
                ->get('/overwrite', function () {
                    return response()->json([
                        'exist' => Breadcrumbs::has('breadcrumbs-overwrite'),
                    ]);
                })
                ->breadcrumbs(function (Trail $trail) {
                    return $trail->push('overwrite');
                });
        }

        $this->get('/overwrite')->assertJson([
            'exist' => true,
        ]);
    }

    public function testBreadcrumbsCacheDefined(): void
    {
        Route::get('/breadcrumbs-home', function () {

            Route::getRoutes()->refreshNameLookups();
            $route = Route::getRoutes()->getByName('breadcrumbs-home');
            $route->parameters = null;

            return Breadcrumbs::current()->toJson();
        })->name('breadcrumbs-home');


        Breadcrumbs::for('breadcrumbs-home', function (Trail $trail) {
            return $trail->push('Home', 'http://localhost/');
        });

        $this->get('/breadcrumbs-home')
            ->assertJson([
                [
                    'title' => 'Home',
                    'url'   => 'http://localhost/',
                ],
            ]);
    }

    public function testBreadcrumbsTimesOne(): void
    {
        $log = storage_path() . '/logs/laravel.log';

        file_put_contents($log, '');

        Route::get('times', function () use ($log) {

            Breadcrumbs::current();

            $logCollection = [];

            foreach (file($log) as $line_num => $line) {
                $logCollection[] = [
                    'line'    => $line_num,
                    'content' => htmlspecialchars($line),
                ];
            }

            return count($logCollection);
        })->name('times')
            ->breadcrumbs(function (Trail $trail) {
                $trail->push('times one');

                Log::debug('Dashboard pushed');
            });

        $count = $this->get('/times')->content();

        $this->assertEquals('1', $count);
    }

    public function testBreadcrumbsIdempotency(): void
    {
        Route::get('/breadcrumbs-home', function () {
            Breadcrumbs::current();
            Breadcrumbs::generate('breadcrumbs-home');

            return Breadcrumbs::current()->toJson();
        })->name('breadcrumbs-home');

        Breadcrumbs::for('breadcrumbs-home', function (Trail $trail) {
            return $trail->push('Home', 'http://localhost/');
        });

        $this->get('/breadcrumbs-home')
            ->assertExactJson([
                [
                    'title' => 'Home',
                    'url'   => 'http://localhost/',
                ],
            ]);
    }
}
