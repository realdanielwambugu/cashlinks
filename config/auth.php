<?php

 return [

    'auth' => [
     /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard"
    */

    'defaults' => [
       'guard' => 'web',
       'provider' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    | Define every Authentication guard for tha Application
    */

    'guards' => [
        'web' => [
           'driver' => 'session',
           'provider' => 'users',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database.
    */

    'providers' => [
        'users' => [
            'driver' => 'orm',
            'model' => App\Models\User::class,
        ],
    ],

   'hash' => [
        'algo' => PASSWORD_BCRYPT,
        'cost' => 10,
    ],

   ],
 ];
