<?php

Namespace Xcholars\Support\Proxies;

class DB extends proxy
{
    /**
    * get the accessor class.
    *
    * @return object \Xcholars\Database\DatabaseManager
    */
    public static function getAccessor()
    {
         return \Xcholars\Database\DatabaseManager::class;
    }

}
