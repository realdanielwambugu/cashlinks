<?php

Namespace Xcholars\Support;

use BadMethodCallException;

class StringMethods
{
  /**
   * Dynamicallu handle method calls
   *
   * @param string $parameters
   * @return mixed
   */
   public function __call($method, array $parameters)
   {
       if (method_exists($instance = new Pluralizer, $method))
       {
           return call_user_func_array([$instance, $method], $parameters);
       }

        throw new BadMethodCallException(
           "method [{$method}] not found in " . get_class($this)
        );
    }

  /**
   * Check ii a string has a certain character
   *
   * @param string $string
   * @param string $character
   * @return bool
   */
   public function contains($string, $character)
   {
       return strpos(" " . $string, $character);
   }

  /**
   * check if string starting with given substring
   *
   * @param string $string
   * @param string $startString
   * @return bool
   */
   public function startsWith ($string, $startString)
   {
        $len = strlen($startString);

        return (substr($string, 0, $len) === $startString);
   }

  /**
   * check if string ends with given substring or not
   *
   * @param string $string
   * @param string $startString
   * @return bool
   */
   public function endsWith($string, $endString)
   {
       $len = strlen($endString);

       if ($len == 0)
       {
           return true;
       }

       return (substr($string, -$len) === $endString);
   }

  /**
   * Split a string into two at certain character
   *
   * @param string $string
   * @param string $char
   * @return array
   */
   public function split($string, $char)
   {
       if ($this->contains($string, $char))
       {
           return [$this->splitBefore($char, $string), $this->splitAfter($char, $string)];
       }

       return $string;
   }

   /**
    * Split a string into two at certain character and asignKey
    *
    * @param string $string
    * @param string $char
    * @param array $keys
    * @return array
    */
    public function splitWithKeys($string, $char, array $keys = [])
    {
        [$first, $second] = $keys;

        if ($this->contains($string, $char))
        {
            return [
              $first  => $this->splitBefore($char, $string),
              $second => $this->splitAfter($char, $string),
            ];
        }

        return [$first  => $string, $second => null,];
    }

   /**
    * Split a string into two| give the first part of the split string
    *
    * @param string $char
    * @param string $string
    * @return string
    */
    public function splitBefore($char, $string)
    {
        if ($this->contains($string, $char))
        {
            return substr($string, 0, strpos($string, $char));
        }

        return $string;
    }

   /**
    * Split a string into two| give the last part of the split string
    *
    * @param string $char
    * @param string $string
    * @return string
    */
    public function splitAfter($char, $string)
    {
        return substr($string, strrpos($string, $char)+1);
    }
}
