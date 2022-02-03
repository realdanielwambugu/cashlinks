<?php

Namespace App\providers;

use Xcholars\Auth\AuthServiceProvider as ServiceProvider;

use Xcholars\Support\Proxies\Gate;

class AuthServiceProvider extends ServiceProvider
{
   /**
    * The policy mappings for the application.
    *
    * @var array
    */
    protected $policies = [
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Thread::class => \App\Policies\ThreadPolicy::class,
    ];

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

    }
}
