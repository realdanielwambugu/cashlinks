<?php

Namespace Xcholars\Routing;

use Xcholars\ApplicationContract;

use ReflectionMethod;

class ControllerDispatcher extends RouteDispatcher
{
    /**
    * Dispatch a request to a given controller and method.
    *
    * @param object $controller
    * @param string $method
    * @return mixed
    */
    public function dispatch($controller, $method, array $parameters)
    {
        $reflector = new ReflectionMethod($controller, $method);

        $dependencies = $this->resolveDependencies($reflector);

        $this->addParametersToRequest($dependencies, $parameters);

        return $reflector->invokeArgs($controller, $dependencies);
    }

}
