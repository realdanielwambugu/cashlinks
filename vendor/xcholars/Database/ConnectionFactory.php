<?php

Namespace Xcholars\Database;

use Xcholars\ApplicationContract;

use InvalidArgumentException;

class ConnectionFactory
{
   /**
    * The application instance.
    *
    * @var object Xcholars\Database\ApplicationContract
    */
    private $app;

   /**
    * Database connectors for each database driver
    *
    * @var array
    */
    private $connectors = [];

   /**
    * Database connections class for each database driver
    *
    * @var array
    */
    private $SupportedDrivers = [];

   /**
    * Create new instance of ConnectionFactory
    *
    * @param object Xcholars\Database\ApplicationContract
    * @return void;
    */
    public function __construct(ApplicationContract $app)
    {
        $this->app = $app;
    }

   /**
    * set connectors for each database driver
    *
    * @param array $connectors
    * @return $this
    */
    public function setConnectors(array $connectors)
    {
        $this->connectors = $connectors;

        return $this;
    }

   /**
    * set connections class for each database driver
    *
    * @param array $connectors
    * @return $this
    */
    public function setSupportedDriver(array $SupportedDrivers)
    {
        $this->SupportedDrivers = $SupportedDrivers;

        return $this;
    }

    /**
    * Establish a PDO connection based on the configuration.
    *
    * @param  array  $config
    * @param  string  $name
    * @return object Xcholars\Database\Connection
    */
    public function make(array $config, $name)
    {
        $config['name'] = $name;

        $pdo = $this->createPdoResolver($config);

        return $this->createConnection(
                 $config['driver'],
                 $pdo,
                 $config['database'],
                 $config
             );
    }

  /**
    * Create a new connection instance.
    *
    * @param  string  $driver
    * @param  string \PDO|\Closure  $pdo
    * @param  string  $database
    * @param  array  $config
    * @return object Xcholars\Database\Connection
    *
    * @throws object \InvalidArgumentException
    */
    protected function createConnection($driver, $pdo, $database, array $config = [])
    {
        if (!$connection = $this->SupportedDrivers[$driver])
        {
            throw new InvalidArgumentException(
                "Unsupported driver [{$driver}]."
            );
        }

        return new $connection($pdo, $database, $config);
    }

   /**
    * Create a new Closure that resolves to a PDO instance where there is no configured host.
    *
    * @param  array  $config
    * @return object \Closure
    */
    private function createPdoResolver(array $config)
    {
        return function () use ($config)
        {
            return $this->createConnector($config)->connect($config);
        };
    }

   /**
    * Create a connector instance based on the configuration.
    *
    * @param  array  $config
    * @return object Xcholars\Database\Connectors\ConnectorInterface
    *
    * @throws object \InvalidArgumentException
    */
    public function createConnector(array $config)
    {
        $connector = $config['driver'] ?? null;

        if (!$connector)
        {
            throw new InvalidArgumentException(
                "A driver must be specified for connection [{$config['name']}]"
            );
        }

        return new $this->connectors[$connector];
    }
}
