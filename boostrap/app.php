<?php

   /*
   |----------------------------------------------------------------------------
   | Create New Instance of the Application
   |----------------------------------------------------------------------------
   |
   | First create a new Xcholars Application instance. The Application class
   | extends The service cotainer class used for binding application services and
   | resolving dependencies.
   */

    $app = new Xcholars\Application();

   /*
   |----------------------------------------------------------------------------
   | Bootstrap the Exception Handler
   |----------------------------------------------------------------------------
   |
   | second we boot the Application Exception/handler dependency. Xcholars uses
   | whoops dependency for error handling.
   |
   |*/

    $app->bootExceptionHandler();

    /*
    |---------------------------------------------------------------------------
    | Load Application environment variables
    |---------------------------------------------------------------------------
    |
    | Load app environment variables using vlucas a dependency for loading
    | .env files.
    |
    |*/

    $app->loadEnvironmentVariables(dirname(__DIR__));

    /*
    |---------------------------------------------------------------------------
    | Register services with the Application
    |---------------------------------------------------------------------------
    |
    | register some essential service classes with the Application to be reolved
    | later when needed.
    |
    |*/

    $app->singleton(\Xcholars\ApplicationContract::class, function ($app)
    {
        return $app;
    });

    $app->bind(\Xcholars\Container\ContainerContract::class, function ($container)
    {
        return new \Xcholars\Container\Container;
    });

    $app->singleton(Xcholars\Settings\SettingsContract::class, function ($app)
    {
        return new Xcholars\Settings\Settings($app->getBasePath() . 'config/*.php');
    });

    $app->singleton(Xcholars\Provider\ProviderCollection::class);

    /*
    |---------------------------------------------------------------------------
    | Bootstrap The Application
    |---------------------------------------------------------------------------
    | Bootstrap the Application by invoking the 'boot' Method and return the
    | Application instance to the calling script.
    |
    |*/

    $app->boot();

    return $app;
