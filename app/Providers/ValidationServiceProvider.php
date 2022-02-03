<?php

Namespace App\providers;

use Xcholars\Validation\ValidationServiceProvider as ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
   /**
    * Validation rules mappings namespace.
    *
    * @var array
    */
    protected $namespace = 'App\Validation';

   /**
    * The Validation rules mappings for the application.
    *
    * @var array
    */
    protected $mappings = [
        // 'signup' => \App\Validation\ForSignup::class,

    ];
}
