<?php

Namespace Xcholars\Support\Proxies;

class Str extends proxy
{
    /**
    * get the accessor class.
    *
    * @return object Xcholars\Support\Configuration
    */
    public static function getAccessor()
    {
         return \Xcholars\Support\StringMethods::class;
    }

}
