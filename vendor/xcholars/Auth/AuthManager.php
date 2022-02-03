<?php

Namespace Xcholars\Auth;

use Xcholars\ApplicationContract;

use Xcholars\Settings\SettingsContract;

use Xcholars\Http\Session\Manager as Session;

use InvalidArgumentException;

class AuthManager implements AuthManagerContract
{
    use CreatesUserProviders;

   /**
    * Application base path
    *
    * @var object Xcholars\Settings\SettingsContract
    */
    private $settings;

   /**
    * Session insatance
    *
    * @var object Xcholars\Sesssion\Manager
    */
    private $session;

   /**
    * The array of created "drivers".
    *
    * @var array
    */
    private $guards = [];

   /**
    * The user resolver shared by various services.
    *
    * @var object \Closure
    */
    protected $userResolver;

   /**
    * Create new AuthManager instance
    *
    * @param object Xcholars\Settings\SettingsContract
    * @return void
    */
    public function __construct(SettingsContract $settings, Hash $hash, Session $session)
    {
        $this->settings = $settings;

        $this->hash = $hash;

        $this->session = $session;

        $this->userResolver = function ($guard = null)
        {
            return $this->guard($guard)->user();
        };
    }

   /**
    * Attempt to get the guard
    *
    * @param  string  $name
    * @return mixed
    */
    public function guard($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        if ($guard = $this->guards[$name]  ?? null)
        {
            return $guard;
        }

        return $this->guards[$name] = $this->resolve($name);

    }

   /**
    * Resolve the given guard.
    *
    * @param  string  $name
    * @return
    *
    * @throws object InvalidArgumentException
    */
    private function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config))
        {
            throw new InvalidArgumentException(
                "Auth guard [{$name}] is not defined."
            );
        }

        $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

        if (method_exists($this, $driverMethod))
        {
            return $this->{$driverMethod}($name, $config);
        }

        throw new InvalidArgumentException(
            "Auth driver [{$config['driver']}] for guard [{$name}] is not defined."
        );
    }

   /**
    * Create a session based authentication guard.
    *
    * @param  string  $name
    * @param  array  $config
    * @return object Xcholars\Auth\SessionGuard
    */
    public function createSessionDriver($name, $config)
    {
        $provider = $this->createUserProvider($config['provider'] ?? null);

        return new SessionGuard($name, $provider, $this->session);
    }

   /**
    * Get the guard configuration.
    *
    * @param  string  $name
    * @return array
    */
    protected function getConfig($name)
    {
        return $this->settings->get("auth.guards.{$name}");
    }

   /**
    * Get the user resolver callback.
    *
    * @return object \Closure
    */
    public function userResolver()
    {
        return $this->userResolver;
    }

   /**
    * Get the default authentication driver name.
    *
    * @return string
    */
    public function getDefaultDriver()
    {
        return $this->settings->get('auth.defaults.guard');

    }

   /**
    * Dynamically call the default driver instance.
    *
    * @param  string  $method
    * @param  array  $parameters
    * @return mixed
    */
    public function __call($method, $parameters)
    {
        return $this->guard()->{$method}(...$parameters);
    }
}
