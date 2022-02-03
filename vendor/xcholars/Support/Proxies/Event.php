<?php

Namespace Xcholars\Support\Proxies;

class Event extends proxy
{
   /**
    * get the accessor class.
    *
    * @return object Xcholars\ApplicationContract
    */
    public static function getAccessor()
    {
        return \Xcholars\Events\DispatcherContract::class;
    }

}
