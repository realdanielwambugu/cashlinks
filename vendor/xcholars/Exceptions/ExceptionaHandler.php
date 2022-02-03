<?php

Namespace Xcholars\Exceptions;


class ExceptionaHandler
{

    public function boot()
    {
        $whoops = new \Whoops\Run;

        $handler = new \Whoops\Handler\PrettyPageHandler;

        $whoops->pushHandler($handler);

        $whoops->register();
    }
}
