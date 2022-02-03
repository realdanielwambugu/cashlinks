<?php

Namespace Xcholars\Http;

use Xcholars\ApplicationContract;

abstract class AbstractFactory
{
   /**
    * service Container instance
    *
    * @var object Xcholars\ApplicationContract
    */
    protected $app;

   /**
    * Create a new route Group registrar instance.
    *
    * @param object Xcholars\Routing\Group\GroupStack $stack
    * @param object  Xcholars\ApplicationContract $app
    * @return void
    */
    public function __construct(ApplicationContract $app)
    {
        $this->app = $app;
    }

}
