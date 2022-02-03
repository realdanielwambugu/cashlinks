<?php

Namespace Xcholars\Container;


interface ContainerContract
{
  /**
   * register bindings in the container.
   *
   * @param string $key
   * @param string $value
   * @param bool $singleton
   * @return void
   */
   public function bind($abstract, $concrete = null, $singleton = false);

  /**
   * register singleton bindings in the container.
   *
   * @param string $abstract
   * @param string $concrete
   * @return void
   */
   public function singleton($abstract, $concrete = null);

  /**
   * Resolve the given abstract type from the container.
   *
   * @param  string  $abstract
   * @param  array  $parameters
   * @return mixed
   */
   public function make($abstract, array $parameters  = []);

}
