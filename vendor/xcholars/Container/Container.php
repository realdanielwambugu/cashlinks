<?php

Namespace Xcholars\Container;

use Closure;

use ReflectionClass;

class Container implements ContainerContract
{
  /**
   * service bindings.
   *
   * @var array
   */
   private $bindings = [];

  /**
   * singleton instances.
   *
   * @var array
   */
   private $singletons = [];

  /**
   * register bindings in the container.
   *
   * @param string $key
   * @param string $value
   * @param bool $singleton
   * @return void
   */
   public function bind($abstract, $concrete = null, $singleton = false)
   {
       if (is_null($concrete))
       {
           $concrete = $abstract;
       }

       $this->bindings[$abstract] = compact('concrete', 'singleton');
   }

  /**
   * register singleton bindings in the container.
   *
   * @param string $abstract
   * @param string $concrete
   * @return void
   */
   public function singleton($abstract, $concrete = null)
   {
       $this->bind($abstract, $concrete, true);
   }

  /**
   * Check whether the given abstract type has concrete binding.
   *
   * @param string $abstract
   * @return bool
   */
   private function hasConcreteBinding($abstract)
   {
      return array_key_exists($abstract, $this->bindings);
   }

  /**
   * check weather the given binding is singleton.
   *
   * @param string $abstract
   * @return bool
   */
   private function isSingleton($abstract)
   {
       if ($this->hasConcreteBinding($abstract))
       {
           return $this->bindings[$abstract]['singleton'];
       }

       return false;
   }

  /**
   * check the given singleton binding is aready resolved.
   *
   * @param string $abstract
   * @return bool
   */
   private function singletonIsResolved($abstract)
   {
       return array_key_exists($abstract, $this->singletons);
   }

  /**
   * Resolve the given abstract type from the container.
   *
   * @param  string  $abstract
   * @param  array  $parameters
   * @return mixed
   */
   public function make($abstract, array $parameters  = [])
   {
       if ($this->isSingleton($abstract) && $this->singletonIsResolved($abstract))
       {
           return $this->singletons[$abstract];
       }

       return $this->resolveBinding($abstract, $parameters);
   }

  /**
   * get concrete binding for the given abstract type.
   *
   * @param string $abstract
   * @return string
   */
   private function getConcreteBinding($abstract)
   {
       $concrete = $abstract;

       if ($this->hasConcreteBinding($abstract))
       {
           $concrete = $this->bindings[$abstract]['concrete'];
       }

       return $concrete;
   }


  /**
   * Mark an instance as singleton.
   *
   * @param string $abstract
   * @param object $instance
   * @return void
   */
   private function markAsSingleton($abstract, $instance)
   {
       $this->singletons[$abstract] = $instance;
   }

  /**
   * Resolve the given abstract type from the container.
   *
   * @param  array $binding
   * @return mixed
   */
   private function resolveBinding($abstract, array $parameters)
   {
       $concrete = $this->getConcreteBinding($abstract);

       if ($concrete instanceof Closure)
       {
           $instance = $this->resolveClosureBinding($concrete, $parameters);
       }
       else
       {
           $instance = $this->resolveClassBinding($concrete, $parameters);
       }

       if ($this->isSingleton($abstract))
       {
           $this->markAsSingleton($abstract, $instance);
       }

       return $instance;
   }

  /**
   * Resolve class Container bindings.
   *
   * @param callback $callback
   * @return mixed
   */
   private function resolveClassBinding($concrete, $parameters)
   {
       $reflector = $this->refelectOnClass($concrete);

       $dependencies = $this->getClassDependencies($reflector);

       if (!is_null($dependencies))
       {
           $resolvedDependencies = $this->resolveDependencies($dependencies);

           $parameters = array_merge($resolvedDependencies, $parameters);
       }

       return $reflector->newInstanceArgs($parameters);
   }

  /**
   * Resolve closure Container bindings.
   *
   * @param callback $callback
   * @return mixed
   */
   private function resolveClosureBinding($concrete, $parameters)
   {
       array_unshift($parameters, $this);

       return call_user_func_array($concrete, $parameters);
   }

  /**
   * Reflect on the given bound class.
   *
   * @param string $class
   * @return mixed
   */
   private function refelectOnClass($class)
   {
        $reflector = new ReflectionClass($class);

        if($reflector->isInstantiable())
        {
             return $reflector;
        }

        throw new \Exception("Class [{$class}] is not instantiable");
   }

  /**
   * Confirm the type hinted dependency is not optional or an array
   *
   * @param object $dependency
   * @return bool
   */
   private function isValidDependency($dependency)
   {
        return !is_null($dependency) || !$dependency->isArray();
   }

  /**
   * Confirm the type hinted dependency is not optional or an array
   *
   * @param object $reflector
   * @return array|null
   */
   private function getClassDependencies($reflector)
   {
        if ($constructor = $reflector->getConstructor())
        {
             return $constructor->getParameters();
        }

        return null;
   }

  /**
   * Confirm the type hinted dependency is not optional or an array
   *
   * @param array $dependencies
   * @return array
   */
   private function resolveDependencies(array $dependencies)
   {
       $resolvedDependencies = [];

        foreach ($dependencies as $dependency)
        {
            if (!$this->isValidDependency($dependency)) continue;

            $class = $dependency->getClass();

            if ($class === null) continue;

            $resolvedDependencies[] = $this->make($class->name);

        }

        return $resolvedDependencies;
   }

}
