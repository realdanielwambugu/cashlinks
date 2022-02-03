<?php

Namespace Xcholars\Support\Proxies;

class Date extends proxy
{
    /**
    * get the accessor class.
    *
    * @return object Xcholars\Support\Contracts\DateContract
    */
    public static function getAccessor()
    {
        return \Xcholars\Support\Contracts\DateContract::class;
    }

}
