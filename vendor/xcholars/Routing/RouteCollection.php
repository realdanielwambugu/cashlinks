<?php

Namespace Xcholars\Routing;

use Xcholars\Exceptions\BadMethodCallException;

use Xcholars\Http\Request;

use Closure;

class RouteCollection
{
   /**
    * All registred Route instances b
    *
    * @var array
    */
    private $allRoutes = [];

   /**
    * All registred Route instances
    *
    * @var array
    */
    private $routes = [];

   /**
    * All registred Route instances with named actions
    *
    * @var array
    */
    private $namedRoutes = [];

   /**
    * All registred Route instances with controller actions
    *
    * @var array
    */
    private $controllerRoutes = [];

   /**
    * Add Route to all routes with http it responds to method|verb as the key
    *
    * @param object  Xcholars\Routing\Route
    * @return object Xcholars\Routing\Route
    */
    public function add(Route $route)
    {
        foreach ($route->getMethods() as $method)
        {
            $this->allRoutes[$method . $route->getUri()] = $route;

            $this->routes[$method][$route->getUri()] = $route;
        }

        $this->addToNamedRoutes($route);

        $this->addToControllerRoutes($route);
    }

   /**
    * Add Route to named routes with http it action name as the key
    *
    * @param object  Xcholars\Routing\Route
    * @return object Xcholars\Routing\Route
    */
    public function addToNamedRoutes(Route $route)
    {
        if ($route->hasName())
        {
            $name = $route->getName();

            $this->namedRoutes[$name] = $route;
        }
    }

   /**
    * get route with named action
    *
    * @return array|string
    */
    public function getNamedRoute($name)
    {
        $this->refreshLookUps();

        return $this->namedRoutes[$name] ?? null;
    }

   /**
    * Add Route to collection with http it target controller as the key
    *
    * @param object  Xcholars\Routing\Route
    * @return object Xcholars\Routing\Route
    */
    public function addToControllerRoutes(Route $route)
    {
        if ($route->hasControllerAction())
        {
            $controllerName = $route->getAction();

            $this->controllerRoutes[$controllerName] = $route;
        }
    }

   /**
    * get routes with controller actions
    *
    * @return object Xcholars\Routing\Route
    */
    public function getControllerRoutes()
    {
        return $this->controllerRoutes;
    }

   /**
    * Find the route matching the given request
    *
    * @param object  Xcholars\Http\Request
    * @return object Xcholars\Routing\Route
    */
    public function match(Request $request)
    {
        $routes = $this->routes[$request->getMethod()];

        $routes = !count($routes) ? $this->allRoutes : $routes;

        $route = $this->matchRequestToRoutes($request, $routes);

        return $route->bind($request);
    }

   /**
    * Find the route matching the given request
    *
    * @param object  Xcholars\Routing\Route
    * @param array $routes
    * @return object Xcholars\Routing\Route
    */
    public function matchRequestToRoutes(Request $request, array $routes)
    {
        foreach ($routes as $route)
        {
            if ($route->matches($request))
            {
                return $route;
            }
        }
    }



   /**
    * get all routes from collection
    *
    * @return array
    */
    public function getAllRoutes()
    {
        return $this->allRoutes;
    }

   /**
    * Refresh the name & controller look-up list
    *
    * @return void
    */
    public function refreshLookUps()
    {
        foreach ($this->allRoutes as $route)
        {
            $this->addToNamedRoutes($route);

            $this->addToControllerRoutes($route);
        }
    }

}
