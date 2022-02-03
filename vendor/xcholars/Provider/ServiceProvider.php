<?php

Namespace Xcholars\Provider;

use Xcholars\ApplicationContract;

abstract class ServiceProvider implements ServiceProviderContract
{
  /**
   * Application instance.
   *
   * @var object
   */
   protected $app;

  /**
   * Provider bootstrapping status | default false.
   *
   * @var object
   */
   protected $booted = false;

  /**
   * Provider registration status | default false.
   *
   * @var object
   */
   protected $registered = false;

  /**
   * set the application instance.
   *
   * @param object Xcholars\Application
   * @return void;
   */
   public function __construct(ApplicationContract $app)
   {
        $this->app = $app;
   }

  /**
   * register bindings to the service container.
   *
   * @return object
   */
   public function register()
   {
        $className = get_called_class();

        throw new \Exception("Class {$className} should implement {register} method");
   }

  /**
   * Check if provider is registered
   *
   * @param string
   * @return bool
   */
   public function isRegistered()
   {
        return $this->registered;
   }

  /**
   * Check if provider is booted
   *
   * @param string
   * @return bool
   */
   public function isBooted()
   {
        return $this->booted;
   }

  /**
   * check if provider has the boot method
   *
   * @param object $provider
   * @return bool
   */
   public function isBootable()
   {
        return method_exists($this, 'boot');
   }

  /**
   * Mark provider as registerd after invoking register method
   *
   * @param object
   * @return bool
   */
   public function markAsRegistered()
   {
        $this->registered = true;
   }

  /**
   * Mark provider as booted after invoking boot method
   *
   * @param object
   * @return bool
   */
   public function markAsBooted()
   {
        $this->booted = true;
   }

}
