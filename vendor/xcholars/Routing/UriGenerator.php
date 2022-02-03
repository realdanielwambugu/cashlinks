<?php

Namespace Xcholars\Routing;

use Xcholars\Support\Proxies\Str;

class UriGenerator
{
   /**
    * create new instance of UriGenerator
    *
    * @var objectXcholars\Routing\RouteCollection
    */
    private $collection;

   /**
    * create new instance of UriGenerator
    *
    * @param object Xcholars\Routing\RouteCollection
    * @return void
    */
    public function __construct(RouteCollection $collection)
    {
        $this->collection = $collection;
    }

    public function preparePath($routeNameOrUri, array $parameters)
    {
        $uri =  $routeNameOrUri;

        if ($route = $this->collection->getNamedRoute($routeNameOrUri))
        {
            $uri = $route->getUri();
        }

        $uri = rtrim(Str::splitBefore('{', $uri), '/');

        return $this->appendParameters($uri, $parameters);
    }

    public function appendParameters($uri, array $parameters)
    {
        foreach ($parameters as $parameter)
        {
            $uri .= '/' . $parameter;
        }

        return $uri;
    }
}
