<?php

Namespace Xcholars\Pipeline;

use Xcholars\Provider\ServiceProvider;

class PipelineSeviceProvider extends ServiceProvider
{
  /**
   * register bindings with the service container.
   *
   * @return object
   */
   public function register()
   {
        $this->app->bind(
            \Xcholars\Pipeline\PipelineContract::class,
            \Xcholars\Pipeline\Pipeline::class
        );
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
