<?php

Namespace Xcholars\Http\Collections;

use Countable;

class InputCollection implements Countable
{

    /**
    * parameter storage
    *
    * @var array
    */
    protected $parameters = [];

    /**
    * Create new ParameterCollection instance
    *
    * @return void
    */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
    * get all parameters
    *
    * @return array
    */
    public function all()
    {
        return $this->parameters;
    }

    /**
    * get all the parameter keys.
    *
    * @return array
    */
    public function keys()
    {
        return array_keys($this->parameters);
    }

   /**
    * Returns true if the parameter is defined.
    *
    * @param $key
    * @return bool
    */
    public function has($key)
    {
        return array_key_exists($key, $this->parameters);
    }

    /**
    * get a parameter by name.
    *
    * @param string $key
    * @param mixed $value
    * @return void
    */
    public function set($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    /**
    * get a parameter by name.
    *
    * @param string $key
    * @param mixed $default
    * @return mixed
    */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->parameters[$key] : $default;
    }

   /**
    * Removes a parameter from the storage array
    *
    * @param $key
    * @return void
    */
    public function remove($key)
    {
        unset($this->parameters[$key]);
    }

   /**
    * get the parameters number.
    *
    * @return int
    */
    public function count()
    {
        return \count($this->parameters);
    }

}
