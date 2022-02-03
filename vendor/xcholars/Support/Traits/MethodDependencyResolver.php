<?php

Namespace Xcholars\Support\Traits;

use Xcholars\Support\Proxies\App;

trait MethodDependencyResolver
{
    /**
    * Loop through all parameters and resolve type hinted dependencies
    *
    * @param object ReflectionMethod $reflector
    * @param array $parameters
    * @return array
    */
    public function resolveDependencies($reflector, array $parameters = [])
    {
        $parameters = array_values($parameters);

        $dependencies = [];

        $count = 0;

        foreach ($reflector->getParameters() as $parameter)
        {
            if ($parameter->isDefaultValueAvailable()) continue;

            if (($type = $parameter->getType()) && !$type->isBuiltin())
            {
                $dependencies[] = $this->resolve($type->getName());

                continue;
            }

            $dependencies[] =  $parameters[$count];

            $count++;
        }

        return $dependencies;
    }

    /**
    * resolve dependency class with the application
    *
    * @param string $name
    * @return object
    */
    public function resolve($name)
    {
        if (property_exists($this, 'app'))
        {
            return $this->app->make($name);
        }

        if (function_exists('app'))
        {
            return app()->make($name);
        }

        if (class_exists(App::class))
        {
            return App::make($name);
        }

        return new $name;
    }
}
