<?php

Namespace Xcholars\Support\Proxies;

class Gate extends proxy
{
    /**
    * get the accessor class.
    *
    * @return object Xcholars\Auth\Access\GateContract
    */
    public static function getAccessor()
    {
        return \Xcholars\Auth\Access\GateContract::class;
    }

}
