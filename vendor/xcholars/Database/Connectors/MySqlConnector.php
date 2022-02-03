<?php

namespace Xcholars\Database\Connectors;

use PDO;

class MySqlConnector extends Connector implements ConnectorContract
{
   /**
    * Establish a database connection.
    *
    * @param  array  $config
    * @return object \PDO
    */
    public function connect(array $config)
    {
        $dsn = $this->getDsn($config);

        $connection = $this->createConnection($dsn, $config);

        $this->configureEncoding($connection, $config);

        $this->setStrictMode($connection, $config);

        return $connection;
    }

   /**
    * Get the DSN string for a host  configuration.
    *
    * @param  array  $config
    * @return string
    */
    private function getDsn(array $config)
    {
        extract($config, EXTR_SKIP);

        return "mysql:host={$host};dbname={$database}";
    }

   /**
    * Set the connection character set and collation.
    *
    * @param object \PDO  $connection
    * @param  array  $config
    * @return object void|\PDO
    */
    private function configureEncoding(PDO $connection, array $config)
    {
        if (! isset($config['charset']))
        {
            return $connection;
        }

        $connection->prepare(
            "set names '{$config['charset']}'".$this->getCollation($config)
        )->execute();
    }

   /**
   * Enable strict mode.
    *
    * @param object \PDO  $connection
    * @param  array  $config
    * @return void
    */
    private function setStrictMode(PDO $connection, array $config)
    {
        if (isset($config['strict']))
        {
            $connection->prepare(
                "set session sql_mode='STRICT_TRANS_TABLES'"
            )->execute();
        }
    }

   /**
    * Get the collation for the configuration.
    *
    * @param  array  $config
    * @return string
    */
    private function getCollation(array $config)
    {
        return isset($config['collation']) ? " collate '{$config['collation']}'" : '';
    }

}
