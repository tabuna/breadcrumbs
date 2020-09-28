<?php


namespace Tabuna\Breadcrumbs\Tests;

use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;
use Tabuna\Breadcrumbs\Trail;

class ComponentTest extends TestCase
{

    public function testSimple(): void
    {
        $this->getComponent('simple')->assertSee('Home');
    }

    public function testClass(): void
    {
        $this->getComponent('class')
            ->assertSee('Home')
            ->assertSee('<li class="item">', false)
            ->assertSee('<li class="item active">', false);
    }

    public function testRoute(): void
    {
        $this->getComponent('route')
            ->assertSee('Static Page');
    }

    public function testParameters(): void
    {
        $this->getComponent('parameters')
            ->assertSee('value 1')
            ->assertSee('value 2')
            ->assertSee('value 3');
    }

    /**
     * @param string $template
     *
     * @return TestResponse
     */
    protected function getComponent(string $template): TestResponse
    {
        Route::get('static', function (string $view) {
        })
            ->name('static')
            ->breadcrumbs(function (Trail $trail) {
                return $trail->push('Static Page');
            });

        Route::get('component/{view}', function (string $view) {
            return view("test_breadcrumbs::$view");
        })
            ->name('breadcrumbs.components')
            ->breadcrumbs(function (Trail $trail, $value = null, $value2 = null) {
                return $trail
                    ->push('Home')
                    ->push('Arguments', $value)
                    ->push('Arguments2', $value2);
            });

        return $this->get("component/$template");
    }
}
