<?php

Namespace Xcholars\Events;

use Xcholars\Provider\ServiceProvider;

use Xcholars\Events\DispatcherContract;

use Xcholars\Events\ListenerFactory;

class EventServiceProvider extends ServiceProvider
{
   /**
    * The event listener mappings for the application.
    *
    * @var array
    */
    protected $listen = [];

   /**
    * register bindings with the service container.
    *
    * @return object
    */
    public function register()
    {
        $this->app->singleton(
            DispatcherContract::class,
            \Xcholars\Events\Dispatcher::class
        );
    }

   /**
    * Register any events for your application.
    *
    * @return void
    */
    public function boot()
    {
        $dispatcher = $this->app->make(DispatcherContract::class);

        foreach ($this->listen as $event => $listeners)
        {
            foreach (array_unique($listeners) as $listener)
            {
                $dispatcher->listen($event, $listener);
            }
        }



    }

}
