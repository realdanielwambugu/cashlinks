<?php

Namespace Xcholars\Pipeline;

interface PipelineContract
{
   /**
    * Create new PipeFactory instance.
    *
    * @param object Xcholars\ApplicationContract
    * @return void
    */
    public function __construct(ApplicationContract $app);

   /**
    * Resolve the pipe with the application
    *
    * @return object
    */
    public function make($pipe);
    
}
