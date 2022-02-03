<?php

Namespace Xcholars\Middleware;

use Xcholars\Provider\ServiceProvider;


class MiddlewareServiceProvider extends ServiceProvider
{
  /**
   * HTTP middlewares
   * They are run during every request.
   *
   * @var array
   */
   protected $middleware = [];

   /**
    * Route middleware groups.
    *
    * @var array
    */
    protected $middlewareGroups  = [];

   /**
    * Route middlewares.
    * They May be assigned to groups or used individually
    *
    * @var array
    */
    protected $routeMiddleware  = [];

   /**
    * register bindings with the service container.
    *
    * @return object
    */
    public function register()
    {

    }

   /**
    * Activities to be performed after bindings are registerd.
    *
    * @return void
    */
    public function boot()
    {
        $httpKernel = $this->app->make(\Xcholars\Http\KernelContract::class);

        $httpKernel->setMiddleware($this->middleware);

        $httpKernel->getRouter()->setMiddlewareGroups($this->middlewareGroups)
                                ->setAliasMiddleware($this->routeMiddleware);
    }
}
