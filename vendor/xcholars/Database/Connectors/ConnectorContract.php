<?php

namespace Xcholars\Database\Connectors;

interface ConnectorContract
{
    /**
     * Establish a database connection.
     *
     * @param  array  $config
     * @return object \PDO
     */
    public function connect(array $config);
}
