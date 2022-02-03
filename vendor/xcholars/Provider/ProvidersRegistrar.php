<?php

Namespace Xcholars\Provider;

use Xcholars\ApplicationContract;

class ProvidersRegistrar
{
  /**
   * all  providers instances.
   *
   * @var array
   */
   private $providers = [];

  /**
   * Create new instance of ProvidersRegistrar
   *
   * @param object Xcholars\Provider\ProviderCollection
   * @return void
   */
   public function __construct(ProviderCollection $collection)
   {
        $this->providers = $collection->getProviders();
   }

  /**
   * register services defined in the provider with the service Container.
   *
   * @return void
   */
   public function register()
   {
        foreach ($this->providers as $provider)
        {
            if (!$provider->isRegistered())
            {
                $provider->register();

                $provider->markAsRegistered();
            }
        }
   }

}
