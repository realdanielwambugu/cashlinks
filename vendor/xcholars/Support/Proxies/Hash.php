<?php

Namespace Xcholars\Support\Proxies;

class Hash extends proxy
{
    /**
    * get the accessor class.
    *
    * @return object Xcholars\Auth\Hash
    */
    public static function getAccessor()
    {
        return \Xcholars\Auth\Hash::class;
    }

}
