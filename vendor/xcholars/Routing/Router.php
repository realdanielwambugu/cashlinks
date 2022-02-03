<?php

Namespace Xcholars\Routing;

use Xcholars\Http\Request;

use Xcholars\Pipeline\PipelineContract;

use Xcholars\Http\ResponseContract;

use Closure;

class Router
{
   /**
    * The Route routeFactory instance
    *
    * @var object Xcholars\Routing\RouteFactory;
    */
    private $routeFactory;

   /**
    * The Route routeFactory instance
    *
    * @var object Xcholars\Http\ResponseFactory;
    */
    private $responseFactory;

   /**
    * The Route Collection instance
    *
    * @var object Xcholars\Routing\RouteCollection;
    */
    private $collection;

   /**
    * The http verbs|methods supported by the router.
    *
    * @var array
    */
    private $verbs = ['GET', 'HEAD', 'POST'];

   /**
    * Route middleware groups.
    *
    * @var array
    */
    private $middlewareGroups  = [];

   /**
    * Route alias middlewares.
    *
    * @var array
    */
    private $aliasMiddleware  = [];

   /**
    * Create new Router instance.
    *
    * @param object Xcholars\Routing\FactoryBuilder
    * @param object Xcholars\Routing\RouteCollection
    *
    * @return void
    */
    public function __construct(Factory $factory, RouteCollection $collection)
    {
        $this->routeFactory = $factory->makeRouteFactory();

        $this->responseFactory = $factory->makeResponseFactory();

        $this->collection = $collection;
    }

   /**
    * Register a new GET route
    *
    * @param string $uri
    * @param string|null|Closure $action
    *
    * @return object Xcholars\Routing\Route;
    */
    public function get($uri, $action = null)
    {
        $methods = ['GET', 'HEAD'];

        return $this->register($methods, $uri, $action);
    }

    /**
    * Register a new POST route.
    *
    * @param string $uri
    * @param string|null|Closure $action
    *
    * @return object Xcholars\Routing\Route;
    */
    public function post($uri, $action = null)
    {
        return $this->register('POST', $uri, $action);
    }

    /**
    * Register a new route responding to all verbs.
    *
    * @param string $uri
    * @param string|null|Closure $action
    *
    * @return object Xcholars\Routing\Route;
    */
    public function any($uri, $action = null)
    {
        return $this->register($this->verbs, $uri, $action);
    }

    /**
    * Register a new route responding to all verbs.
    *
    * @param  array|string  $methods
    * @param string $uri
    * @param string|null|Closure $action
    *
    * @return object Xcholars\Routing\Route;
    */
    public function match(array $methods, $uri, $action = null)
    {
        $methods = array_map('mb_strtoupper', $methods);

        return $this->register($methods, $uri, $action);
    }

    /**
    * Register a new route that returns a view.
    *
    * @param string $uri
    * @param string $view
    * @param array $data
    */
    public function view($uri, $view, array $data = [])
    {
        return $this->match(['GET', 'HEAD'], $uri,
               'ViewController@__invoke')
               ->SetDefault('view', $view)
               ->SetDefault('data', $data);
    }

    /**
    *  Register a new Fallback route with the router.
    *
    * @param string|Closure $action
    * @return object Xcholars\Routing\Route;
    */
    public function fallback($action)
    {
        $uriPlaceholder = 'uriPlaceholder';

        return $this->register('GET', "{{$uriPlaceholder}}", $action)
                    ->assert($uriPlaceholder, '.*')
                    ->setAsFallback();
    }

    /**
    * Register a new route with the Router
    *
    * @param array $methods
    * @param string $uri
    * @param string|null|Closure $action
    *
    * @return object Xcholars\Routing\Route;
    */
    public function register($methods, $uri, $action)
    {
        $route = $this->routeFactory->makeRouteWith($methods, $uri, $action);

        $this->collection->add($route);

        return $route;
    }

   /**
    * set the given middleware groups for the route
    *
    * @param array $middlewareGroups
    * @return $this
    */
    public function setMiddlewareGroups(array $middlewareGroups)
    {
        $this->middlewareGroups = $middlewareGroups;

        return $this;
    }

   /**
    * set the given route alias middlewares
    * They May be assigned to groups or used individually
    *
    * @param array $middleware
    * @return $this
    */
    public function setAliasMiddleware(array $middleware)
    {
        $this->aliasMiddleware = $middleware;

        return $this;
    }

   /**
    * set the given route alias middlewares
    * They May be assigned to groups or used individually
    *
    * @param object Xcholars\Http\Request $request
    * @param object Xcholars\Pipeline\PipelineContract $pipeline
    * @return $this
    */
    public function dispatch(Request $request, PipelineContract $pipeline)
    {
        $route = $this->collection->match($request);

        $middlewares = array_reverse($this->gatherRouteMiddleware($route));

        return $pipeline->send($request)
                ->through($middlewares)
                ->then(function () use($route)
                {
                    $result = $route->run();

                    if ($result instanceof ResponseContract)
                    {
                        return $result->prepare();
                    }

                    return $this->responseFactory->makeResponseWith(
                         $result, 200, [
                            'Content-Type' => 'text/html',
                            'charset' => 'UTF-8'
                        ]
                    )->prepare();

                });
    }

   /**
    * Gather the middleware for the given route with resolved class names.
    *
    * @param object Xcholars\Routing\Route $route
    * @return array
    */
    public function gatherRouteMiddleware(Route $route)
    {
        $excluded = $this->reolveMiddlewareNames($route->getExcludedMiddleware());

        $middleware = $this->reolveMiddlewareNames($route->getMiddleware());

        return array_values(array_diff($middleware, $excluded));
    }

   /**
    * Resolve class names for the given route middlewares
    *
    * @param array $middlewares
    * @return array
    */
    public function reolveMiddlewareNames(array $middlewares)
    {
        return array_flatten(array_map(function ($name)
                {
                    return (new MiddlewareNameResolver(
                        $this->aliasMiddleware, $this->middlewareGroups
                    ))->resolve($name);

                }, $middlewares));
    }
}
