<?php

Namespace Xcholars\Routing;

use Xcholars\ApplicationContract;

use Xcholars\Exceptions\BadMethodCallException;

use Xcholars\Routing\Group\GroupStack;

use LogicException;

use Closure;

class RouteFactory
{
   /**
    * The Group Stack instance
    *
    * @var object Xcholars\Routing\Group\GroupStack
    */
    private $stack;

   /**
    * service Container instance
    *
    * @var object Xcholars\ApplicationContract
    */
    private $app;

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
    * get the route group stack from the application
    *
    * @return object Xcholars\Routing\Group\GroupStack
    */
    public function getGroupStack()
    {
        return $this->stack ?? $this->app->make(GroupStack::class);
    }

   /**
    * Create a new route registrar instance.
    *
    * @param object Xcholars\Routing\Router $router
    * @return void
    */
    public function makeRouteWith($methods, $uri, $action)
    {
        $uri = $this->prependLastGroupUriPrefix($uri);

        if(is_null($action))
        {
            $action = $this->makeMissingActionClosure($uri);
        }

        $elements = [$methods, $uri, $action];

        $route = $this->app->make(Route::class)->addElements($elements);

        if (!$this->getGroupStack()->isEmpty())
        {
            $this->addGroupAttributesToRoute($route);
        }

        return $route;
    }

   /**
    * Prefix the given URI with the last group prefix.
    *
    * @param  string  $uri
    * @return string
    */
    private function prependLastGroupUriPrefix($uri)
    {
        if ($lastPrefix = $this->getGroupStack()->getLastGroupUriPrefix())
        {
            return rtrim('/' . $lastPrefix . '/' . ltrim($uri, '/'), '/');
        }

        return '/' . ltrim($uri, '/');
    }

   /**
    * add group attributes to route action defined in a group closure
    *
    * @param string $uri
    * @param string|array $action
    * @return array
    */
    private function addGroupAttributesToRoute(Route $route)
    {
        if ($route->hasControllerAction())
        {
            $route->setAction($this->prependLastGroupNamespace($route->getAction()));
        }

        $route->setGroupMiddlware($this->getGroupStack()->getLastGroupMiddlewares());

        $route->setName($this->getGroupStack()->getLastGroupNameprefix());

        return $route;
    }

   /**
    * Create closure object for null route action.
    *
    * @param string $uri
    * @return Closure
    */
    public function makeMissingActionClosure($uri)
    {
        return function () use($uri)
        {
            throw new LogicException("Route for [{$uri}] has no action");
        };
    }

   /**
    * Prepend the last group namespace onto action controller
    *
    * @param string $controllerName
    * @return string
    */
    public function prependLastGroupNamespace($controllerName)
    {
        if ($lastNamespace = $this->getGroupStack()->getLastGroupNamespace())
        {
            return $lastNamespace . '\\' . $controllerName;
        }

        return $controllerName;
    }

}
