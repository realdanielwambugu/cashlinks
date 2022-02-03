<?php
  /**
   * Php Web development Skeleton.
   *
   * @package Xcholars.
   * @author   Daniel N Wambugu <pairplanet@gmail.com>.
   *
   */

   /*
   |--------------------------------------------------------------------------
   | Require in The class AutoLoader.
   |--------------------------------------------------------------------------
   |  Require in the composer AutoLoader file for autoloading the Application
   | classes following the psr-4" standard.
   */

   require_once __DIR__ . '/../vendor/autoload.php';

   /*
   |--------------------------------------------------------------------------
   | Require in The Application Boostraper file
   |--------------------------------------------------------------------------
   |
   | This registers & boots all service providers to get them ready
   | for use by the Application.
   |
   */

   $app = require_once __DIR__ . '/../boostrap/app.php';

   /*
   |--------------------------------------------------------------------------
   | Handle The Request
   |--------------------------------------------------------------------------
   |
   | Use the returned Xcholars Application instance Handle the User
   | request through the http kernel and send the back the given response.
   |
   */

   $factory = $app->make(\Xcholars\Http\RequestFactory::class);

   $request = $factory->createFromGlobals();

   $kernal = $app->make(\Xcholars\Http\KernelContract::class);

   $response = $kernal->handle($request);

   $response->send();
