<?php

namespace Alcaitiff\LaravelUrlEncode;

use Alcaitiff\LaravelUrlEncode\Routing\Router;
use Alcaitiff\LaravelUrlEncode\Routing\UrlGenerator;
use Alcaitiff\LaravelUrlEncode\Routing\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route as RouteFacade;

class RouteServiceProvider extends ServiceProvider {

  //Laravel stinky strings
  const ROUTER = 'router';
  const EVENTS = 'events';
  const ENV = 'env';
  const TESTING = 'testing';
  const ROUTES = 'routes';
  const REQUEST = 'request';
  const URL = 'url';
  const SESSION = 'session';
  const WEB = 'web';
  const API = 'api';
  const API_ROUTE_FILE = 'routes/api.php';
  const WEB_ROUTE_FILE = 'routes/web.php';
  const SET_ROUTER = 'setRouter';
  /**
   * This namespace is applied to your controller routes.
   *
   * In addition, it is set as the URL generator's root namespace.
   *
   * @var string
   */
  protected $namespace = 'App\Http\Controllers';

  /**
   * Define your route model bindings, pattern filters, etc.
   *
   * @return void
   */
  public function boot() {
    $originalRouter = $this->app[static::ROUTER];
    $this->app->singleton(
      static::ROUTER,
      function ($app) {
        $router = new Router($app[static::EVENTS], $app);
        if ($app[static::ENV] == static::TESTING) {
          $router->disableFilters();
        }
        return $router;
      }
    );

    $this->app->singleton(
      static::URL,
      function ($app) {
        $routes = $app[static::ROUTER]->getRoutes();
        $app->instance(static::ROUTES, $routes);
        $url = new UrlGenerator(
          $routes, $app->rebinding(
            static::REQUEST,
            function ($app, $request) {
              $app[static::URL]->setRequest($request);
            }
          )
        );
        $url->setSessionResolver(function () {
          return $this->app[static::SESSION];
        });
        $app->rebinding(static::ROUTES, function ($app, $routes) {
          $app[static::URL]->setRoutes($routes);
        });
        return $url;
      });
    parent::boot();
    $this->_load($originalRouter);
    //You can change the app router but you can't change the kernel router
    //and kernel uses his own router to dispatch
    //so we need to change the kernel router too
    $kernel = $this->app[\Illuminate\Contracts\Http\Kernel::class];
    if(is_callable([$kernel, static::SET_ROUTER])){
      $kernel->setRouter($this->app[static::ROUTER]);
    }
    
  }

  private function _load($originalRouter) {
    $this->app[static::ROUTER]->setRoutes($this->app[static::ROUTER]->getRoutes());
    $this->app[static::ROUTER]->cloneMiddleware($originalRouter);
  }

  public function register() {
    $this->app->bind(\Illuminate\Support\Facades\Route::class, Route::class);
    $this->app->bind(\Illuminate\Contracts\Routing\UrlGenerator::class, UrlGenerator::class);
  }

  /**
   * Define the routes for the application.
   *
   * @return void
   */
  public function map() {
    $this->mapApiRoutes();
    $this->mapWebRoutes();
  }

  /**
   * Define the "web" routes for the application.
   *
   * These routes all receive session state, CSRF protection, etc.
   *
   * @return void
   */
  protected function mapWebRoutes() {
    RouteFacade::middleware(static::WEB)
      ->namespace($this->namespace)
      ->group(base_path(static::WEB_ROUTE_FILE));
  }

  /**
   * Define the "api" routes for the application.
   *
   * These routes are typically stateless.
   *
   * @return void
   */
  protected function mapApiRoutes() {
    RouteFacade::prefix(static::API)
      ->middleware(static::API)
      ->namespace($this->namespace)
      ->group(base_path(static::API_ROUTE_FILE));
  }
}
