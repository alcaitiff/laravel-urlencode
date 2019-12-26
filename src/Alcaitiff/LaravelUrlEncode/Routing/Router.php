<?php

namespace Alcaitiff\LaravelUrlEncode\Routing;

use Alcaitiff\LaravelUrlEncode\Routing\Route;
use Illuminate\Routing\Router as Rtr;

class Router extends Rtr {
  /**
   * Create a new Route object.
   *
   * @param  array|string $methods
   * @param  string  $uri
   * @param  mixed  $action
   * @return \Illuminate\Routing\Route
   */
  protected function newRoute($methods, $uri, $action) {
    return new Route($methods, $uri, $action);
  }

  public function cloneMiddleware(Rtr $router) {
    $this->middleware = $router->getMiddleware();
    $this->middlewareGroups = $router->getMiddlewareGroups();
    $this->middlewarePriority = $router->middlewarePriority;

  }

}