<?php

Namespace Xcholars\Database;

use Xcholars\Settings\SettingsContract;

use InvalidArgumentException;

class DatabaseManager implements ConnectionResolverContract
{
   /**
    * The application instance.
    *
    * @var object Xcholars\Database\ApplicationContract
    */
    private $settings;

   /**
    * The database connection factory instance.
    *
    * @var object Xcholars\Database\ConnectionInterface
    */
    private $factory;

   /**
    * The active connection instances.
    *
    * @var array
    */
    private $connections = [];

   /**
    * Create new instance of DatabaseManager
    *
    * @return object Xcholars\Database\SettingsContract
    * @return object Xcholars\Database\ConnectionInterface
    */
    public function __construct(SettingsContract $settings, ConnectionFactory $factory)
    {
        $this->settings = $settings;

        $this->factory = $factory;
    }

   /**
    * Get a database connection instance.
    *
    * @param  string|null  $name
    * @return object Xcholars\Database\ConnectionInterface
    */
    public function connection($name = null)
    {
        $name = $name ?? $this->getDefaultConfigName();

        if (!isset($this->connections[$name]))
        {
            $this->connections[$name] = $this->makeConnection($name);
        }

        return $this->connections[$name];
    }

   /**
    * Make the database connection instance.
    *
    * @param string  $name
    * @return object Xcholars\Database\Connection
    */
    private function makeConnection($name)
    {
        $config = $this->getConfiguration($name);

        return $this->factory->make($config, $name);
    }

   /**
    * Get the configuration for a connection.
    *
    * @param string|null $name
    * @return array
    *
    * @throws object \InvalidArgumentException
    */
    protected function getConfiguration($name = null)
    {
        $connections = $this->settings->get('database.connections');

        if (!$config = $connections[$name])
        {
            throw new InvalidArgumentException(
                "Database connection [{$name}] not configured."
            );
        }

        $config['options'] = $connections['options'];

        return $config;
    }

   /**
    * Get the default configuration name.
    *
    * @return string
    */
    private function getDefaultConfigName()
    {
        return $this->settings->get('database.default');
    }

   /**
    * Dynamically pass methods to the default connection.
    *
    * @param  string  $method
    * @param  array  $parameters
    * @return mixed
    */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->connection(), $method], $parameters);
    }
}
