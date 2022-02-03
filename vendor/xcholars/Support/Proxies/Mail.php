<?php

Namespace Xcholars\Support\Proxies;

class Mail extends proxy
{
    /**
    * get the accessor class.
    *
    * @return object \Xcholars\Mail\Mailer
    */
    public static function getAccessor()
    {
        return \Xcholars\Mail\Mailer::class;
    }

}
