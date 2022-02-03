<?php

Namespace Xcholars;

use Xcholars\Provider\ServiceProvider;

use Xcholars\Support\Contracts\DateContract;

class AppServiceProvider extends ServiceProvider
{
  /**
   * register bindings with the service container.
   *
   * @return object
   */
   public function register()
   {
       $this->app->bind(DateContract::class, function ($app)
       {
           return new \Xcholars\Support\CustomDateTime;
       });

   }

}
