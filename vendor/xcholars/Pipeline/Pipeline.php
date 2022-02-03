<?php

Namespace Xcholars\Pipeline;

use Closure;

class Pipeline implements PipelineContract
{
   /**
    * Application instance
    *
    * @var object Xcholars\Pipeline\PipeFactory
    */
    private $factory;

   /**
    * The object being passed through the pipeline.
    *
    * @var mixed
    */
    private $passables;

   /**
    * The array of class pipes.
    *
    * @var array
    */
    private $pipes = [];

   /**
    * The method to call on each pipe.
    *
    * @var string
    */
    private $method = 'handle';

   /**
    * Create new pipeline instance.
    *
    * @param object Xcholars\Pipeline\PipeFactory
    * @return void
    */
    public function __construct(PipeFactory $factory)
    {
        $this->factory = $factory;
    }

   /**
    * Set the object being sent through the pipeline.
    *
    * @param  mixed  $passables
    * @return $this
    */
    public function send($passables)
    {
        $this->passables = $passables;

        return $this;
    }

   /**
    * Set the array of pipes.
    *
    * @param array
    * @return $this
    */
    public function through($pipes)
    {
        $this->pipes = $pipes;

        return $this;
    }

   /**
    * Set the method to call on the pipes.
    *
    * @param string $method
    * @return $this
    */
    public function via($method)
    {
        $this->method = $method;

        return $this;
    }

   /**
    * Run the pipeline with a final destination callback.
    *
    * @param object Closure $destination
    * @return mixed
    */
    public function then(Closure $destination)
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            $this->carry(),
            $this->prepareDestination($destination)
        );

        return call_user_func($pipeline, $this->passables);
    }

   /**
    * Get the final piece of the Closure onion.
    *
    * @param object Closure $destination
    * @return object Closure
    */
    private function prepareDestination($destination)
    {
        return function ($passables) use($destination)
        {
            return call_user_func($destination, $passables);
        };
    }

   /**
    * Get the final piece of the Closure onion.
    *
    * @return object Closure
    */
    private function carry()
    {
        return function ($destination, $pipe)
        {
            return function ($passables) use ($destination, $pipe)
            {
                if (is_callable($pipe))
                {
                    return call_user_func($pipe, $destination);
                }

                [$pipe, $parameters] = $this->resolvePipe($pipe);

                $passables = array_merge([$passables, $destination], $parameters);

                return method_exists($pipe, $this->method)
                       ? $pipe->{$this->method}(...$passables)
                       : $pipe(...$passables);
            };
        };
    }

   /**
    * Resolve string pipe name with service container/Application
    *
    * @param  string  $pipe
    * @return array
    */
    private function resolvePipe($pipe)
    {
        if (is_string($pipe))
        {
            [$name, $parameters] = $this->parsePipeString($pipe);
        }

        $pipe = $this->factory->make($name);

        return [$pipe, $parameters];
    }

   /**
    * Parse full pipe string to get name and parameters.
    *
    * @param  string $pipe
    * @return array
    */
    private function parsePipeString($pipe)
    {
        [$name, $parameters] = array_pad(explode(':', $pipe, 2), 2, []);

        if (is_string($parameters))
        {
            $parameters = explode(',', $parameters);
        }

        return [$name, $parameters];
    }

}
