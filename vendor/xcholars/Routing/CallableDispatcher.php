<?php

Namespace Xcholars\Routing;

use Xcholars\ApplicationContract;

use ReflectionFunction;

use Closure;

class CallableDispatcher extends RouteDispatcher
{
    /**
    * Dispatch a request to a given controller and method.
    *
    * @param \Closure $closure
    * @param string $method
    * @return mixed
    */
    public function dispatch(Closure $closure, array $parameters)
    {
         $reflector = new ReflectionFunction($closure);

         $dependencies = $this->resolveDependencies($reflector);

         $this->addParametersToRequest($dependencies, $parameters);

         return $reflector->invoke(...$dependencies);
    }

}
