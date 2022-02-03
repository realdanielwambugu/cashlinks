<?php

Namespace Xcholars\Validation;

class ErrorHandler
{
   /**
    * list of  Validation errors
    *
    * @var array
    */
    private $errors = [];

   /**
    * set validation errors
    *
    * @param string $error
    * @param string|null $key
    * @return void
    */
    public function setError($error, $key = null)
    {
        if ($key)
        {
            $this->errors[$key][] = $error;
        }
        else
        {
           $this->errors[] = $error;
        }

    }

   /**
    * get all validation errors
    *
    * @param string|null $key
    * @return void
    */
    public function all($key = null)
    {
        return isset($this->errors[$key]) ? $this->errors[$key] : $this->errors;
    }

   /**
    * get the first validation errors for a field
    *
    * @param string $error
    * @param string|null $key
    * @return void
    */
    public function first($key = null)
    {
      if (!$key && isset($this->errors[$key][0]))
      {
          return $this->errors[$key][0];
      }

        return  $this->firstOfAll();
    }

   /**
    * get the first of all validation errors
    *
    * @param string $error
    * @param string|null $key
    * @return void
    */
    public function firstOfAll()
    {
       foreach ($this->errors as $error)
       {
           return $error[0];
       }
    }

   /**
    * check if the handler has any vali errors
    *
    * @param string $error
    * @param string|null $key
    * @return void
    */
    public function hasErrors()
    {
        return count($this->all()) ? true : false;
    }

}
