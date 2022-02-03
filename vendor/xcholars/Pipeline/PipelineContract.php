<?php

Namespace Xcholars\Pipeline;

use Closure;

interface PipelineContract
{
   /**
    * Create new pipeline instance.
    *
    * @param object Xcholars\Pipeline\PipeFactory
    * @return void
    */
    public function __construct(PipeFactory $factory);

   /**
    * Set the object being sent through the pipeline.
    *
    * @param  mixed  $passables
    * @return $this
    */
    public function send($passables);

   /**
    * Set the array of pipes.
    *
    * @param array
    * @return $this
    */
    public function through($pipes);

   /**
    * Set the method to call on the pipes.
    *
    * @param string $method
    * @return $this
    */
    public function via($method);

    /**
     * Run the pipeline with a final destination callback.
     *
     * @param object Closure $destination
     * @return mixed
     */
     public function then(Closure $destination);

}
