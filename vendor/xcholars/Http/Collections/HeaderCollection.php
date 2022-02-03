<?php

Namespace Xcholars\Http\Collections;

class HeaderCollection extends InputCollection
{
    /**
    * The cookie headers
    *
    * @var array
    */
    private $cookies = [];

   /**
    * create new instance of HeaderCollection
    *
    * @param array $headers
    * @return void
    */
    public function __construct(array $headers = [])
    {
        $this->setHeaders($headers);
    }

   /**
    * Add a list of cookies to the response.
    *
    * @param array $cookie
    * @return $this
    */
    public function setCookies(array $cookies)
    {
        foreach ($cookies as $name => $values)
        {
            $this->cookies[$name] = $values;
        }
     }

   /**
    * get the list of cookies .
    *
    * @return array
    */
    public function getCookies()
    {
        return $this->cookies;
    }

   /**
    * Add an array of headers to the response.
    *
    * @param array $headers
    * @return void
    */
    public function setHeaders(array $headers)
    {
       foreach ($headers as $name => $value)
       { 
           $this->set($name, $value);
       }
    }



}
