<?php

namespace Xcholars\Auth;

use InvalidArgumentException;

trait CreatesUserProviders
{
   /**
    * Hash instance
    *
    * @var object Xcholars\Auth\Hash;
    */
    private $hash;

   /**
    * Create the user provider implementation for the driver.
    *
    * @param  string|null  $provider
    * @return object Xcholars\Auth\UserProviderContract|null
    *
    * @throws object \InvalidArgumentException
    */
    public function createUserProvider($provider = null)
    {
        if (is_null($config = $this->getProviderConfiguration($provider)))
        {
            return;
        }

        switch ($driver = $config['driver'] ?? null)
        {
           case 'database':
               return $this->createDatabaseProvider($config);
           case 'orm':
               return $this->createOrmProvider($config);
           default:
                throw new InvalidArgumentException(
                    "Authentication user provider [{$driver}] is not defined."
                );
       }
    }

    /**
    * Get the user provider configuration.
    *
    * @param  string|null  $provider
    * @return array|null
    */
    private function getProviderConfiguration($provider)
    {
        if ($provider = $provider ?: $this->getDefaultUserProvider())
        {
            return $this->settings->get('auth.providers.' . $provider);
        }
    }

   /**
    * Get the default user provider name.
    *
    * @return string
    */
    public function getDefaultUserProvider()
    {
        return $this->settings->get('auth.defaults.provider');
    }


   /**
    * Create an instance of the Eloquent user provider.
    *
    * @param  array  $config
    * @return object Xcholars\Auth\OrmUserProvider
    */
    protected function createOrmProvider($config)
    {
        return new OrmUserProvider($config['model'], $this->hash);
    }

   /**
    * Create an instance of the database user provider.
    *
    * @param  array  $config
    * @return
    */
    private function createDatabaseProvider($config)
    {

    }
}
