<?php

Namespace Xcholars\Support\Proxies;

class App extends proxy
{
    /**
    * get the accessor class.
    *
    * @return object Xcholars\ApplicationContract
    */
    public static function getAccessor()
    {
        return \Xcholars\ApplicationContract::class;
    }

}
