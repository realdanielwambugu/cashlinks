<?php

Namespace Xcholars\Support\Proxies;

class Settings extends proxy
{
    /**
    * get the accessor class.
    *
    * @return object Xcholars\ApplicationContract
    */
    public static function getAccessor()
    {
        return \Xcholars\Settings\SettingsContract::class;
    }

}
