<?php

Namespace Xcholars\Http;

use Xcholars\Pipeline\PipelineContract;

use Xcholars\Routing\Router;

use Xcholars\Http\Request;

class Kernel implements KernelContract
{
   /**
    * pipeline instance
    *
    * @var array
    */
    private $pipeline;

   /**
    * The router instance
    *
    * @var array
    */
    private $router;

   /**
    * HTTP middlewares
    *
    * @var array
    */
    private $middleware = [];

   /**
    * Create new Kernel instance.
    *
    * @param object Xcholars\PipelineContract
    * @param object Xcholars\Routing\Router
    * @return void
    */
    public function __construct(PipelineContract $pipeline, Router $router)
    {
        $this->pipeline = $pipeline;

        $this->router = $router;
    }

   /**
    * set the given grobal Middleware
    * They are run during every request
    *
    * @param array $middlware
    * @return void
    */
    public function setMiddleware(array $middlware)
    {
        $this->middleware = $middlware;
    }

   /**
    * Handle the user request
    *
    * @param array object Xcholars\Http\Request
    * @return object Xcholars\Http\Response
    */
    public function handle(Request $request)
    { 
        return $this->pipeline->send($request)
                    ->through($this->middleware)
                    ->then($this->dispatchRequest($this->pipeline));
    }

   /**
    * Send the given request through the middlewares.
    *
    * @return object Closure
    */
    public function dispatchRequest(PipelineContract $pipeline)
    {
        return function ($request) use($pipeline)
        {
            return $this->router->dispatch($request, $pipeline);
        };
    }

   /**
    * get the router instance
    *
    * @return object Xcholars\Routing\Router
    */
    public function getRouter()
    {
        return $this->router;
    }

}
