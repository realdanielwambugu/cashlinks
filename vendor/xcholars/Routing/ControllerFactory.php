<?php

Namespace Xcholars\Routing;

use Xcholars\ApplicationContract;

use Xcholars\Support\proxies\Str;

class ControllerFactory
{
   /**
   	* Application instance
   	*
   	* @var object
   	*/
    private $app;

   /**
    * Create new ControllerFactory instance.
    *
    * @param object Xcholars\ApplicationContract
    * @return void
    */
    public function __construct(ApplicationContract $app)
    {
        $this->app = $app;
    }

    public function make($controller)
    {
        $class = $this->parseController($controller);

        return $this->app->make($class);
    }

   /**
    * Extract controller class if th $controller is  a route action
    *
    * @param object Xcholars\ApplicationContract
    * @return void
    */
    public function parseController($controller)
    {
          if (str::contains($controller, '@'))
          {
              $controller = str::splitBefore('@', $controller);
          }

          return $controller;
    }


}
