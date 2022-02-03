<?php

Namespace Xcholars\Support\Proxies;

class Response extends proxy
{
    /**
    * get the accessor class.
    *
    * @return object Xcholars\ApplicationContract
    */
    public static function getAccessor()
    {
         return \Xcholars\Http\Response::class;
    }

}
