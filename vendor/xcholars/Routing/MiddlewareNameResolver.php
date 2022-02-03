<?php

Namespace Xcholars\Routing;

use Closure;

class MiddlewareNameResolver
{
   /**
    * Route middleware groups.
    *
    * @var array
    */
    private $middlewareGroups  = [];

   /**
    *  alias name for middlewares classes.
    *
    * @var array
    */
    private $aliasMap  = [];

   /**
    * Create new Router MiddlewareNameResolver.
    *
    * @param  array  $aliasMap
    * @param  array  $middlewareGroups
    * @return void
    */
    public function __construct(array $aliasMap, array $middlewareGroups)
    {
        $this->aliasMap = $aliasMap;

        $this->middlewareGroups = $middlewareGroups;
    }

   /**
    * Parse the middleware group and format it for usage.
    *
    * @param  string|Closure $name
    * @return array
    */
    public function resolve($name)
    {
        if ($closure = $this->getClosureMiddleware($name))
        {
            return $closure;
        }

        if ($this->isMiddlewareGroup($name))
        {
            return $this->parseMiddlewareGroup($name);
        }

        return $this->prepareMiddlewareClassName($name);
    }

   /**
    * check if the name references a middleware group
    *
    * @param string|Closure $name
    * @return bool
    */
    public function isMiddlewareGroup($name)
    {
        return isset($this->middlewareGroups[$name]);
    }

   /**
    * When the middleware is a Closure, this Closure instance
    *
    * @param  string|Closure $name
    * @return array|bool
    */
    public function getClosureMiddleware($name)
    {
        if ($name instanceof Closure)
        {
            return $name;
        }

        $alias = $this->aliasMap[$name] ?? null;

        return $alias instanceof Closure ? $alias : false;
    }

   /**
    * Parse the middleware group and format it for usage.
    *
    * @param string $name
    * @return array
    */
    private function parseMiddlewareGroup($name)
    {
        $results = [];

        foreach ($this->middlewareGroups[$name] as $middleware)
        {
            if ($this->isMiddlewareGroup($middleware))
            {
                $results = array_merge(
                    $results, $this->parseMiddlewareGroup($middleware)
                );

                continue;
            }

               $results[] = $this->prepareMiddlewareClassName($middleware);
        }

        return $results;
    }

   /**
    * Parse the middleware class name and format it for usage.
    *
    * @param string $name
    * @return array
    */
    public function prepareMiddlewareClassName($name)
    {
        $results = [];

        [$name, $parameters] = array_pad(
            explode(':', $name, 2), 2, null
        );
        
        $middleware = $this->aliasMap[$name] ?? $name;

        return $middleware . ($parameters ? ':' . $parameters : '');
    }
}
