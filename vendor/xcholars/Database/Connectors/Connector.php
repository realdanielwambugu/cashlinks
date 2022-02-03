<?php

namespace Xcholars\Database\Connectors;

use Exception;

use PDO;

class Connector
{
   /*
    * Create a new PDO connection.
    *
    * @param  string  $dsn
    * @param  array  $config
    * @param  array  $options
    * @return \PDO
    *
    * @throws \Exception
    */
    protected function createConnection($dsn, array $config)
    {
        [$username, $password, $options] = [
            $config['username'] ?? null,
            $config['password'] ?? null,
            $config['options'] ?? '',
        ];

        try {
            return $this->createPdoConnection(
                $dsn, $username, $password, $options
            );

        } catch (Exception $error)
        {
            throw new Exception(
                "Database connection failed: [{$error}]"
            );
        }

    }

   /**
    * Create a new PDO connection instance.
    *
    * @param  string  $dsn
    * @param  string  $username
    * @param  string  $password
    * @param  array  $options
    * @return object \PDO
    */
    private function createPdoConnection($dsn, $username, $password, $options)
    {
        return new PDO($dsn, $username, $password, $options);
    }

}
