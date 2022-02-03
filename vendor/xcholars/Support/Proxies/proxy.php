<?php

Namespace Xcholars\Support\Proxies;

class proxy
{
  /**
   * invoke the requested method using the accessor instance and pass the args.
   *
   * @param string $accessor
   * @param array $args
   * @return mixed
   */
   public static function __callStatic($method, $args)
   {
       $accessor = static::getAccessor();

       if (!is_object($accessor))
       {
           $accessor = static::resolveAccessor($accessor);
       }

       return static::callAccessorMethod($accessor, $method, $args);
   }


   /**
   * throw an Exception if the child class does not implement getAccessor method.
   *
   * @return mixed
   */
   public static function getAccessor()
   {
       $className = get_called_class();

       throw new \Exception("'Proxy {$className} must implement getAccessor method");
   }


   /**
   * Create an instance of the accessor class.
   *
   * @param string $accessor
   * @return object
   */
   public static function resolveAccessor($accessor)
   {
       return app()->make($accessor);
   }


  /**
   * invoke the requested accessor class method.
   *
   * @param string $instance
   * @param string $method
   * @param array $args
   * @return mixed
   */
   public static function callAccessorMethod($instance, $method, $args)
   {
       return call_user_func_array([$instance, $method], $args);
   }


}
