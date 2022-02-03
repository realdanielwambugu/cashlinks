<?php

Namespace Xcholars\Routing;

use Xcholars\ApplicationContract;

use Xcholars\Http\ResponseFactory;

class Factory
{
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
    * resolve factory class with the application
    *
    * @param string $abstract
    * @return object
    */
    public function make($abstract)
    {
        return  $this->app->make($abstract) ;
    }

   /**
    * Create a new instance of RouteFactory.
    *
    * @return object Xcholars\Routing\RouteFactory
    */
    public function makeRouteFactory()
    {
        return $this->make(RouteFactory::class);
    }

   /**
    * Create a new instance of ResponseFactory.
    *
    * @return object Xcholars\Routing\RouteFactory
    */
    public function makeResponseFactory()
    {
        return $this->make(ResponseFactory::class);
    }

   /**
    * Create a new instance of ResponseFactory.
    *
    * @return object Xcholars\Routing\RouteFactory
    */
    public function makeControllerFactory()
    {
        return $this->make(ControllerFactory::class);
    }


}
