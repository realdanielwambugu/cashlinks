<?php

Namespace Xcholars\Routing;

use Xcholars\Support\Proxies\Str;

use Xcholars\Pipeline\Pipeline;

use Xcholars\Http\Request;

use Xcholars\Routing\Validators\ValidatorContract;

use Xcholars\Routing\Validators\UriValidator;

use Xcholars\Routing\Validators\MethodValidator;

use Closure;

use ReflectionFunction;

class Route
{
   /**
    * fallback route status | defaults to false
    *
    * @var string
    */
    private $fallback = false;

   /**
 	* The route Action : controller || closure
 	*
 	* @var object|string
 	*/
 	private $action;

   /**
    * The route controller instance
    *
    * @var object
    */
    private $controller;

    /**
 	* middlewares for the route
 	*
 	* @var array
 	*/
 	private $middlewares = [];

    /**
 	* middleware that should be removed from the given route.
 	*
 	* @var array
 	*/
 	private $excludedMiddleware = [];

   /**
    * Globally available parameter patterns.
    *
    * @var array
    */
    private $patterns = [];

   /**
    * The route action name
    *
    * @var string
    */
    private $name;

    /**
 	* The Uri the route should respond to
 	*
 	* @var string
 	*/
 	private $uri;

    /**
 	* Http verbs the oute should respond to.
 	*
 	* @var array
 	*/
 	private $methods = [];

   /**
    * The parameter names for the route.
    *
    * @var array
    */
    private $parameterNames = [];

   /**
    * The array of matched parameters.
    *
    * @var array
    */
    private $parameters = [];

   /**
    * The default values for the route.
    *
    * @var object Xcholars\Routing\RouteCompiler
    */
    private $compiler;

   /**
    * The default values for the route.
    *
    * @var object Xcholars\Http\Factory
    */
    private $factory;

   /**
    * The default values for the route.
    *
    * @var object Xcholars\Http\ControllerFactory
    */
    private $ControllerFactory;

   /**
    * Get the compiled version of the route.
    *
    * @var object Symfony\Modified\Routing\CompiledRoute
    */
    private $compiled;

   /**
    * set default data
    *
    * @var array
    */
    private $defaults = [];

   /**
    * Create new Route instance.
    *
    * @param object Xcholars\Routing\RouteCompiler
    * @param object Xcholars\Routing\Factory
    * @return void
    */
    public function __construct(RouteCompiler $compiler, Factory $factory)
    {
        $this->compiler = $compiler;

        $this->factory = $factory;

        $this->ControllerFactory = $factory->makeControllerFactory();
    }

   /**
    * set Route elements for compiling
    *
    * @param array $elements
    * @return object $this
    */
    public function addElements(array $elements)
    {
        [$methods, $uri, $action] = $elements;

        $this->methods = is_string($methods) ? (array) $methods : $methods;

        $this->uri = $uri;

        $this->action = $action;

        return $this;
    }

   /**
    * apply middleware to route
    *
    * @return $this
    */
    public function withMiddleware($middleware)
    {
        $this->middlewares = array_merge($this->middlewares, func_get_args());

        return $this;
    }

   /**
    * Get all middlewares.
    *
    * @return array
    */
    public function getMiddleware()
    {
        return $this->middlewares;
    }


   /**
    * Specify middleware that should be removed from the given route.
    *
    * @param array|string  $middleware
    * @return $this
    */
    public function withoutMiddleware($middleware)
    {
        $this->excludedMiddleware = is_array($middleware)
                                    ? $middleware
                                    : func_get_args();

        return $this;
    }

   /**
    * Get the middleware should be removed from the route.
    *
    * @return array
    */
    public function getExcludedMiddleware()
    {
        return $this->excludedMiddleware;
    }

   /**
    * Mark this route as a fallback route.
    *
    * @return $this
    */
    public function setAsFallback()
    {
        $this->fallback = true;

        return $this;
    }

   /**
    * Check if route is marked as fallback
    *
    * @return bool
    */
    public function isFallback()
    {
        return $this->fallback;
    }

   /**
    * Set a default value for the route.
    *
    * @param string  $key
    * @param mixed  $value
    * @return $this
    */
    public function SetDefault($key, $value)
    {
        $this->defaults[$key] = $value;

        return $this;
    }

   /**
    * get a default value for the route
    *
    * @param string  $key
    * @return $this
    */
    public function getDefault($key = null)
    {
        return $this->defaults[$key]  ?? $this->defaults;
    }

    /**
    * set route parameters patterns eg:'[id' => '[0-9]+']
    *
    * @param string $expression
    * @return object $this
    */
    public function assert($name, $expression = null)
    {
        foreach ($this->parseAssertArgs($name, $expression) as $name => $expression)
        {
             $this->patterns[$name] = $expression;
        }

        return $this;
    }

    /**
    * parse argumemts to assert method into an array.
    * @param array|string $name
    * @param string $expression
    * @return array
    */
    public function parseAssertArgs($name, $expression)
    {
        return is_array($name) ? $name : [$name => $expression];
    }

    /**
    * get Http verbs the oute should respond to.
    *
    * @return array
    */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
    * get the uri this route responds to
    *
    * @return string
    */
    public function getUri()
    {
        return $this->uri;
    }

    /**
    * get the route action name
    *
    * @param string $name
    * @return void
    */
    public function setName($name)
    {
        $this->name = $this->hasName() ? $this->name . $name : $name ;

        return $this;
    }

    /**
    * check if the action name is set
    *
    * @return bool
    */
    public function hasName()
    {
        return !empty($this->getName());
    }

    /**
    * get the route action name
    *
    * @return string
    */
    public function getName()
    {
        return $this->name;
    }

    /**
    * set group middlware for this route
    *
    * @param array $middlewares
    * @return void
    */
    public function setGroupMiddlware(array $middlewares)
    {
        $this->middlewares = array_merge($this->middlewares, $middlewares);
    }

   /**
    * set the route action
    *
    * @param string|Closure
    * @return void
    */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

   /**
    * get route action
    *
    * @return object Xcholars\Routing\RouteAction
    */
    public function getAction()
    {
        return $this->action;
    }

    /**
    * check if the action is a controller
    *
    * @return bool
    */
    public function hasControllerAction()
    {
        if(is_string($this->action))
        {
            return Str::contains($this->action, '@');
        }

        return false;
    }

   /**
    * Run the route action and return the response.
    *
    * @return mixed
    */
    public function run()
    {
        if ($this->hasControllerAction())
        {
            return $this->runController();
        }

        return $this->runCallable();
    }

   /**
    * Run the route action and return the response.
    *
    * @return mixed
    */
    private function runController()
    {
        return $this->controllerDispatcher()->dispatch(
               $this->getController(),
               $this->getControllerMethod(),
               $this->getFilledParameters()
            );
    }

   /**
    * Run the route action and return the response.
    *
    * @return mixed
    */
   protected function runCallable()
   {
       return $this->factory->make(
                        CallableDispatcher::class
                    )->dispatch(
                        $this->action,
                        $this->getFilledParameters()
                    );
   }

   /**
    * Get the controller instance for the route.
    *
    * @return object
    */
    private function getController()
    {
        return $this->controller = $this->ControllerFactory->make($this->action);
    }

   /**
    * Get the controller method used for the route.
    *
    * @return object
    */
    private function getControllerMethod()
    {
        return str::splitAfter('@', $this->action);
    }

   /**
    * Get the dispatcher for the route's controller.
    *
    * @return object Xcholars\Routing\ControllerDispatcher
    */
    private function controllerDispatcher()
    {
        return $this->factory->make(ControllerDispatcher::class);
    }

   /**
    * Get the key / value list of parameters without null values.
    *
    * @return array
    */
    public function getFilledParameters()
    {
        return array_filter($this->parameters, function ($parameter)
        {
             return ! is_null($parameter);
        });
    }

   /**
    * Determine if the route matches a given request.
    *
    * @param object Xcholars\Http\Request  $request
    * @return bool
    */
    public function matches(Request $request)
    {
        $this->compiled = $this->compiler->compileWith($this->getElements());

        foreach ($this->getValidators() as $validator)
        {
            if (!$validator->matches($this, $request))
            {
                return false;
            }
        }

        return true;
    }

   /**
    * Get the route validators for the instance.
    *
    * @param object Xcholars\Http\Request $request
    * @return $this
    */
    public function bind(Request $request)
    {
        $this->parameters = (new ParameterBinder($this))->bindParameters($request);

        $request->resolver =  $this;

        return $this;
    }

  /**
   * Get the route validators for the instance.
   *
   * @return array
   */
   public function getValidators()
   {
        return [
            new UriValidator,
            new MethodValidator,
        ];
   }

   /**
    * Get the compiled version of the route.
    *
    * @return object Symfony\Modified\Routing\CompiledRoute
    */
    public function getCompiled()
    {
        return $this->compiled;
    }

   /**
    * get Route elements for compiling
    *
    * @return array
    */
    public function getElements()
    {
        return [
            preg_replace('/\{(\w+?)\?\}/', '{$1}', $this->uri),
            $this->extractOptionalParameterNames(),
            $this->patterns,
            ['utf8' => true, 'action' => $this->action],
            $this->methods,
        ];
    }

   /**
    * Get all of the parameter names for the route.
    *
    * @return array
    */
    public function getParameterNames()
    {
        if (count($this->parameterNames) > 0)
        {
            return $this->parameterNames;
        }

        return $this->parameterNames = $this->compileParameterNames();
    }

   /**
    * Get the parameter names for the route.
    *
    * @return array
    */
    public function compileParameterNames()
    {
        preg_match_all('/\{(.*?)\}/', $this->uri, $matches);

        return array_map(function ($match)
        {
            return trim($match, '?');

        }, $matches[1]);
    }

   /**
    * parse the route uri to extract optional Parameters
    *
    * @return array
    */
    public function extractOptionalParameterNames()
    {
        preg_match_all('/\{(\w+?)\?\}/', $this->uri, $matches);

        return isset($matches[1]) ? array_fill_keys($matches[1], null) : [];
    }
}
