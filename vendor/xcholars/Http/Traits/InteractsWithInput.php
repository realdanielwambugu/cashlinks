<?php

Namespace Xcholars\Http\Traits;

trait InteractsWithInput
{
   /**
    * Get all of the input and files for the request.
    *
    * @param  array|mixed|null  $keys
    * @return array
    */
    public function all()
    {
        return array_replace_recursive($this->input() ?? [], $this->allFiles());
    }

   /**
    * Get an array of all of the files on the request.
    *
    * @return array
    */
    public function allFiles()
    {
        return $this->files->all();
    }

   /**
    * Retrieve an input item from the request.
    *
    * @param  string|null  $key
    * @param  mixed  $default
    * @return mixed
    */
    public function input($key = null)
    {
        return $this->getInputSource()->all() + $this->query->all();
    }

   /**
    * Get a subset containing the provided keys with values from the input data.
    *
    * @param  array|mixed  $keys
    * @return array
    */
    public function only($keys)
    {
        $results = [];

        foreach (is_array($keys) ? $keys : func_get_args() as $key)
        {
            $results[$key] = $this->get($this->all(), $key);
        }

        return $results;
    }

   /**
    * Get a subset not containing the provided keys with values from the input data.
    *
    * @param  array|mixed  $keys
    * @return array
    */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        return array_diff_key($this->all(), array_flip($keys));
    }

   /**
    * Get the input source for the request.
    *
    * @return object Xcholars\Http\Collections\InputCollection
    */
    protected function getInputSource()
    {
        return in_array(
            $this->getMethod(), ['GET', 'HEAD'])
            ? $this->query
            : $this->post;
    }
}
