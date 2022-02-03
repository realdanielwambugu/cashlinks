<?php

Namespace App\providers;

use Xcholars\Middleware\MiddlewareServiceProvider as ServiceProvider;


class MiddlewareServiceProvider extends ServiceProvider
{
  /**
   * HTTP middlewares
   * They are run during every request.
   *
   * @var array
   */
   protected $middleware = [


   ];

   /**
    * Route middleware groups.
    *
    * @var array
    */
    protected $middlewareGroups  = [
       'web' => [

        ],

    ];

   /**
    * Route middlewares.
    * They May be assigned to groups or used individually
    *
    * @var array
    */
    protected $routeMiddleware  = [
        'auth' => \App\Middleware\Auth::class,
        'guest' => \App\Middleware\Guest::class,
    ];

}
