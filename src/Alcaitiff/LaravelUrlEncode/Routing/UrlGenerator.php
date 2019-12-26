<?php

namespace Alcaitiff\LaravelUrlEncode\Routing;

use Illuminate\Routing\UrlGenerator as UrlGen;
use Alcaitiff\LaravelUrlEncode\Routing\RouteUrlGenerator;

class UrlGenerator extends UrlGen {

  /**
   * Get the Route URL generator instance.
   *
   * @return \Illuminate\Routing\RouteUrlGenerator
   */
  protected function routeUrl() {
    if (!$this->routeGenerator) {
      $this->routeGenerator = new RouteUrlGenerator($this, $this->request);
    }
    return $this->routeGenerator;
  }

}