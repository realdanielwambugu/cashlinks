<?php

Namespace Xcholars\Events;

use Xcholars\ApplicationContract;

class ListenerFactory
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

   /**
    * Create a new route Group registrar instance.
    *
    * @param object Xcholars\Routing\Group\GroupStack $stack
    * @param object  Xcholars\ApplicationContract $app
    * @return void
    */
    public function make($listener)
    {
        return $this->app->make($listener);
    }

}
