<?php

Namespace App\providers;

use Xcholars\AppServiceProvider as ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
  /**
   * register bindings with the service container.
   *
   * @return object
   */
   public function register()
   {
        Parent::register();
   }

  /**
   * Activities to be performed after bindings are registerd.
   *
   * @return void
   */
   public function boot()
   {
       //boot
   }
}
