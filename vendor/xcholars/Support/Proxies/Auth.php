<?php

Namespace Xcholars\Support\Proxies;

class Auth extends proxy
{
    /**
    * get the accessor class.
    *
    * @return object Xcholars\ApplicationContract
    */
    public static function getAccessor()
    {
        return \Xcholars\Auth\AuthManagerContract::class;
    }

}
