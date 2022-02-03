<?php

Namespace Xcholars\Http;

use Xcholars\Provider\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
{
  /**
   * register bindings with the service container.
   *
   * @return object
   */
   public function register()
   {
        $this->app->singleton(
            \Xcholars\Http\KernelContract::class,
            \Xcholars\Http\Kernel::class
        );

        $this->app->singleton(\Xcholars\Http\Request::class);

        $this->app->singleton(\Xcholars\Http\Response::class);

        $this->app->singleton(\Xcholars\Http\Session\Manager::class);

   }

  /**
   * Activities to be performed after bindings are registerd.
   *
   * @return void
   */
   public function boot()
   {
        $session = $this->app->make(\Xcholars\Http\Session\Manager::class);

        $session->start();

        $session->getstore()->getSessions();

        $this->app->make(\Xcholars\Http\Response::class)->setSession($session);
   }
}
