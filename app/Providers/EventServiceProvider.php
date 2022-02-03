<?php

Namespace App\providers;

use Xcholars\Events\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
   /**
    * The event listener mappings for the application.
    *
    * @var array
    */
    protected $listen = [
        \App\Events\UserRegitered::class => [
            \App\Listeners\SendWelcomeEmail::class,
        ],

        \App\Events\EmailExist::class => [
            \App\Listeners\SendResetCodeEmail::class,
        ],
    ];

   /**
    * register bindings with the service container.
    *
    * @return object
    */
    public function register()
    {
        parent::register();


    }

   /**
    * Register any events for your application.
    *
    * @return void
    */
    public function boot()
    {
        parent::boot();

        //code
    }
}
