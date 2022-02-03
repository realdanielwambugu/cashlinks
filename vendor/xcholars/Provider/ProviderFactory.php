<?php

Namespace Xcholars\Provider;

use Xcholars\ApplicationContract;

use Xcholars\Settings\SettingsContract;


class ProviderFactory
{
  /**
   * all  providers instances.
   *
   * @var object  Xcholars\ApplicationContract
   */
   private $app;

  /**
   * setting class instance
   *
   * @var object Xcholars\Settings\Setting
   */
   private $settings;

  /**
   * Create new ProviderFactory instance
   *
   * @param object Xcholars\Settings\SettingsContract $settings
   * @param object Xcholars\ApplicationContract $app
   * @return void
   */
   public function __construct(ApplicationContract $app, SettingsContract $settings)
   {
      $this->app = $app;

      $this->settings = $settings;
   }

  /**
   * Create new instances of Provider
   *
   * @return array
   */
   public function build()
   {
      $providers = [];

      foreach ($this->getProviderClasses() as $providerClass)
      {
         $providers[] = new $providerClass($this->app);
      }

      return $providers;
   }

  /**
   * get Providers class names from app\config\app.config.php
   *
   * @return array
   */
   private function getProviderClasses()
   {
      return $this->settings->get('app.providers');
   }


}
