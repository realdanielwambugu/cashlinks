<?php

Namespace Xcholars\Routing;

use Xcholars\ApplicationContract;

use Xcholars\Http\Request;

class RouteDispatcher
{
    use \Xcholars\Support\Traits\MethodDependencyResolver;

   /**
    * Application instance
    *
    * @var object
    */
    private $app;

   /**
    * Create new ControllerDispatcher instance.
    *
    * @param object Xcholars\ApplicationContract
    * @return void
    */
    public function __construct(ApplicationContract $app)
    {
        $this->app = $app;
    }


    /**
    * Add $_GET request parameters to the Request
    *
    * @param array $dependencies
    * @param array $parameters
    * @return void
    */
    public function addParametersToRequest(array $dependencies, array $parameters)
    {
        foreach ($dependencies as $dependency)
        {
            if (!$dependency instanceof Request) continue;

            foreach ($parameters as $key => $value)
            {
                $dependency->query->set($key, $value);
            }
        }
    }

}
