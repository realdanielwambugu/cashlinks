<?php

Namespace Xcholars\Provider;


class ProvidersBootstrapper
{
  /**
   * all providers instances.
   *
   * @var array
   */
   private $providers = [];

  /**
   * Create new instance of ProvidersBootstrapper
   *
   * @param object Xcholars\Provider\ProviderCollection
   * @return void
   */
   public function __construct(ProviderCollection $collection)
   {
        $this->providers = $collection->getProviders();
   }

  /**
   * boot registered providers
   *
   * @return void
   */
   public function boot()
   {
       foreach ($this->providers as $provider)
       {
            if ($provider->isBootable() && $provider->isRegistered())
            {
                $provider->boot();

                $provider->markAsBooted();
            }
       }
   }

}
