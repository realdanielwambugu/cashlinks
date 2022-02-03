<?php

Namespace Xcholars\Routing\Group;


class GroupRegistrar
{
   /**
    * The Group Stack instance
    *
    * @var object Xcholars\Routing\Group\GroupStack
    */
    private $stack;

   /**
    * The Group Loader instance
    *
    * @var object Xcholars\Routing\Group\GroupLoader
    */
    private $loader;

   /**
    * Group attributes to be registered.
    *
    * @var array
    */
    private $attributes = [];

   /**
    * Create a new route Group registrar instance.
    *
    * @param object Xcholars\Routing\Group\GroupStack $stack
    * @param object Xcholars\Routing\Group\GroupLoader $loader
    * @return void
    */
    public function __construct(GroupStack $stack, GroupLoader $loader)
    {
        $this->stack = $stack;

        $this->loader = $loader;
    }

   /**
    * Create a route group with shared attributes.
    *
    * @param Closure|string $callback
    * @return object $this
    */
    public function members($routes)
    {
        $this->stack->update($this->attributes);

        $this->loader->load($routes);

        $this->stack->removeLastGroup();
    }

   /**
    * set the namespace attribute.
    *
    * @param string $namespace
    * @return object $this
    */
    public function namespace($namespace)
    {
        $this->attributes['namespace'] = $namespace;

        return $this;
    }

   /**
    * set the prefix attribute.
    *
    * @param string $uriPrefix
    * @return object $this
    */
    public function prefix($uriPrefix)
    {
        $this->attributes['prefix'] = $uriPrefix;

        return $this;
    }

   /**
    * set the middlware attribute.
    *
    * @param string $middleware
    * @return object $this
    */
    public function middleware($middleware)
    {
        $middleware = is_string($middleware) ? (array) $middleware : $middleware;

        $this->attributes['middlware'] = $middleware;

        return $this;
    }

   /**
    * set the name attribute.
    *
    * @param string $namePrefix
    * @return object $this
    */
    public function name($namePrefix)
    {
        $this->attributes['name'] = $namePrefix;

        return $this;
    }

}
