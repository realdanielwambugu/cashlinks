<?php

Namespace Xcholars\Pipeline;

use Xcholars\ApplicationContract;

class PipeFactory
{
   /**
    * Application instance
    *
    * @var array
    */
    private $app;

   /**
    * Create new PipeFactory instance.
    *
    * @param object Xcholars\ApplicationContract
    * @return void
    */
    public function __construct(ApplicationContract $app)
    {
        $this->app = $app;
    }

   /**
    * Resolve the pipe with the application
    *
    * @return object
    */
    public function make($pipe)
    {
        return $this->app->make($pipe);
    }
}
