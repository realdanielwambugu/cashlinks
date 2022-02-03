<?php

Namespace Xcholars\Support\Proxies;

class Schema extends proxy
{

   /**
    * Get a schema builder instance for a connection.
    *
    * @param string|null  $name
    * @return object Xcholars\Database\Schema\Builder
    */
    public static function connection($name = null)
    {
        return static::resolveAccessor(
            \Xcholars\Database\DatabaseManager::class
        )->connection($name)->getSchemaBuilder();

    }

    /**
    * get the accessor class.
    *
    * @return object Xcholars\Database\Schema\Builder
    */
    public static function getAccessor()
    {
        return static::connection();
    }

}
