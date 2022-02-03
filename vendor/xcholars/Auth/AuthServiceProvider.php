<?php

Namespace Xcholars\Auth;

use Xcholars\Provider\ServiceProvider;

use \Xcholars\Auth\Access\GateContract;

use \Xcholars\Auth\Access\Gate;

class AuthServiceProvider extends ServiceProvider
{
   /**
    * The policy mappings for the application.
    *
    * @var array
    */
    protected $policies = [];

   /**
    * register bindings with the service container.
    *
    * @return object
    */
    public function register()
    {
        $this->app->singleton(
            \Xcholars\Auth\AuthManagerContract::class,
            \Xcholars\Auth\AuthManager::class
        );

        $this->registerAccessGate();

        $this->registerPolicies();
    }

   /**
    * Register the access gate service.
    *
    * @return void
    */
    private function registerAccessGate()
    {
        $this->app->singleton(GateContract::class, function ($app)
        {
            return new Gate($app, function () use ($app)
            {
                return call_user_func(
                    $app->make(
                        \Xcholars\Auth\AuthManagerContract::class
                    )->userResolver()
                );
            });
        });
    }

   /**
    * Register the application's policies.
    *
    * @return void
    */
    public function registerPolicies()
    {
        $gate = $this->app->make(GateContract::class);

        foreach ($this->policies as $key => $value)
        {
            $gate->policy($key, $value);
        }
    }
}
