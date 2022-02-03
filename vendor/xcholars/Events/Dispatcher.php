<?php

Namespace Xcholars\Events;

use Xcholars\ApplicationContrct;

use Xcholars\Support\Proxies\Str;

class Dispatcher implements DispatcherContract
{

   /**
    * The IoC container instance.
    *
    * @var object Xcholars\Events\listenerFactory
    */
    protected $factory;


   /**
    * The registered event listeners.
    *
    * @var array
    */
    private $listeners = [];

   /**
    * Create a new event dispatcher instance.
    *
    * @param object Xcholars\Events\listenerFactory
    * @return void
    */
    public function __construct(ListenerFactory $factory)
    {
        $this->factory = $factory;
    }

   /**
    * Register an event listener with the dispatcher.
    *
    * @param  string|array  $events
    * @param object|string| Closure|$listener
    * @return void
    */
    public function listen($events, $listener)
    {
        foreach ((array) $events as $event)
        {
            $this->listeners[$event][] = $this->makeListener($listener);
        }
    }

   /**
    * Register an event listener with the dispatcher.
    *
    * @param object \Closure|string  $listener
    * @return object \Closure
    */
    public function makeListener($listener)
    {
        if (is_string($listener) || is_array($listener))
        {
            return $this->createClassListener($listener);
        }

        return function ($event, $payload) use ($listener)
        {
            return $listener(...array_values($payload));
        };

    }

   /**
    * Create a class based listener using the IoC container.
    *
    * @param  string  $listener
    * @param  bool  $wildcard
    * @return object \Closure
    */
    public function createClassListener($listener)
    {
        return function ($event, $payload) use ($listener)
        {
            return call_user_func_array(
                $this->createClassCallable($listener), $payload
            );
        };
    }

   /**
    * Create the class based event callable.
    *
    * @param array|string  $listener
    * @return callable
    */
    protected function createClassCallable($listener)
    {
        [$class, $method] = is_array($listener)
                            ? $listener
                            : $this->parseListener($listener);

        return [$this->factory->make($class), $method];
    }

   /**
    * Parse the class listener into class and method.
    *
    * @param  string  $listener
    * @return array
    */
    protected function parseListener($listener)
    {
        if (Str::contains($listener, '@'))
        {
            return Str::split($listener, '@');
        }

        return [$listener, 'handle'];
    }

   /**
    * Fire an event and call the listeners.
    *
    * @param  string|object  $event
    * @param  mixed  $payload
    * @return array|null
    */
    public function dispatch($event, $payload = [])
    {
        [$event, $payload] = $this->parseEventAndPayload(
            $event, $payload
        );

        $responses = [];

        foreach ($this->getListeners($event) as $listener)
        {
            $response = $listener($event, $payload);

            if ($response === false)
            {
                break;
            }

            $responses[] = $response;
        }

        return $responses;
    }

   /**
    * Parse the given event and payload and prepare them for dispatching.
    *
    * @param  mixed  $event
    * @param  mixed  $payload
    * @return array
    */
    protected function parseEventAndPayload($event, $payload)
    {
        if (is_object($event))
        {
            [$payload, $event] = [$event, get_class($event)];
        }

        return [$event, [$payload]];

    }


   /**
    * Get all of the listeners for a given event name.
    *
    * @param  string  $eventName
    * @return array
    */
    public function getListeners($eventName)
    {
        return $this->listeners[$eventName] ?? [];
    }
}
