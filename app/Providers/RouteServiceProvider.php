<?php

Namespace App\providers;

use Xcholars\Routing\RouteServiceProvider as ServiceProvider;

use Xcholars\Support\Proxies\RouteGroup as Group;

class RouteServiceProvider extends ServiceProvider
{
  /**
   * Namespace applied to controller routes.
   *
   * @var string
   */
   protected $namespace = 'App\Controllers';

  /**
   * Activities to be performed after bindings are registered.
   *
   * @return void
   */
   public function boot()
   {
   	    Parent::boot();

        $this->mapWebRoutes();
   }

  /**
   * Define applicatin web routes.
   *
   * @return void
   */
   private function mapWebRoutes()
   {
   		Group::middleware('web')
   			  ->namespace($this->namespace)
   			  ->members('route/web.php');
   }

}
