<?php

Namespace Xcholars\Support\Proxies;

class Route extends proxy
{
    /**
    * get the accessor class.
    *
    * @return object Xcholars\routing\RouterContract
    */
    public static function getAccessor()
    {
        return \Xcholars\Routing\Router::class;
    }

}
