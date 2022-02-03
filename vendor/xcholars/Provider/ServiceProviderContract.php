<?php

Namespace Xcholars\Provider;

use Xcholars\ApplicationContract;

interface ServiceProviderContract
{
   /**
    * set the application instance.
    *
    * @param object Xcholars\Application
    * @return void;
    */
    public function __construct(ApplicationContract $app);

   /**
    * register bindings to the service container.
    *
    * @return object
    */
    public function register();

   /**
    * Check if provider is registered
    *
    * @param string
    * @return bool
    */
    public function isRegistered();

   /**
    * Check if provider is booted
    *
    * @param string
    * @return bool
    */
    public function isBooted();

   /**
    * check if provider has the boot method
    *
    * @param object $provider
    * @return bool
    */
    public function isBootable();

   /**
    * Mark provider as registerd after invoking register method
    *
    * @param object
    * @return bool
    */
    public function markAsRegistered();

   /**
    * Mark provider as booted after invoking boot method
    *
    * @param object
    * @return bool
    */
    public function markAsBooted();
}
