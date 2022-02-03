<?php

Namespace Xcholars\Provider;


class ProviderCollection
{
  /**
   * all  providers instances.
   *
   * @var array
   */
   private $providers = [];

  /**
   * Create new ProviderCollection instance
   *
   * @param object Xcholars\Provider\ProviderFactory $factory
   * @return void
   */
   public function __construct(ProviderFactory $factory)
   {
       $this->providers = $factory->build();
   }

  /**
   * get all provider instances
   *
   * @return array
   */
   public function getProviders()
   {
      return $this->providers;
   }

}
