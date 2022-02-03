<?php

Namespace Xcholars\Http;

use InvalidArgumentException;

use Xcholars\Http\Session\Manager as Session;

use Xcholars\Support\Traits\HasNestedArray;

use Xcholars\Http\Traits\InteractsWithInput;

class Request
{
    use HasNestedArray;

    use InteractsWithInput;

    /**
 	* The [$_GET] parameters
 	*
 	* @var object Xcholars/Http/InputCollection
 	*/
 	private $query;

    /**
 	* The [$_POST] parameters
 	*
 	* @var object Xcholars/Http/InputCollection or ParameterCollection
 	*/
 	private $post;

    /**
 	* Custom parameters (parameters parsed from the PATH_INFO, ...)
 	*
 	* @var object Xcholars/Http/ParameterCollection
 	*/
 	private $attributes;

    /**
 	* The [$_SERVER] parameters
 	*
 	* @var object Xcholars/Http/serverCollection
 	*/
 	private $server;

    /**
    * The [$_COOKIE] parameters
    *
    * @var object Xcholars/Http/InputCollection
    */
    private $cookie;

    /**
    * The [$_COOKIE] parameters
    *
    * @var object Xcholars/Http/Session/Manager
    */
    private $session;

    /**
 	* The [$_FILES] parameters
 	*
 	* @var object Xcholars/Http/FileCollection
 	*/
 	private $files;

    /**
 	* Request headers parameters
 	*
 	* @var object Xcholars/Http/HeaderCollection
 	*/
 	private $header;

    /**
    * Request uri
    *
    * @var string
    */
    private $requestUri = '/';

    /**
    * Request PATH_INFO
    *
    * @var string
    */
    private $pathInfo = '/';

    /**
    * Route for this request
    *
    * @var object Xcholars\Routing\Route
    */
    private $resolver;

   /**
    * Properties that can be set Dynamically
    *
    * @var array
    */
    private $allowedProperties = [
        'resolver', 'session','query', 'post', 'cookie', 'server', 'files', 'header'
    ];

   /**
    * Create new Request instance.
    *
    * @param object Xcholars\Http\Session\Manager $session;
    * @return void
    */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

   /**
    * Dynamically set request properties
    *
    * @param string $property
    * @param string $value
    * @return void
    */
    public function __set($property, $value)
    {
        if ($this->propertyIsAllowed($property))
        {
            $this->$property = $value;
        }
        else
        {
            throw new InvalidArgumentException("property {$property} not allowed");
        }
    }

   /**
    * Dynamically get request properties
    *
    * @param string  $key
    * @return mixed
    */
    public function __get($key)
    {
        if ($this->propertyIsAllowed($key))
        {
            return $this->$key;
        }

        return $this->get($this->all(), $key);
    }

   /**
    * Check if request property is Dynamically accessible
    *
    * @return bool
    */
    public function propertyIsAllowed($property)
    {
        return in_array($property, $this->allowedProperties);
    }

   /**
    * get the http method for this request
    *
    * @return string
    */
    public function getMethod()
    {
        return $this->server->get('REQUEST_METHOD');
    }

   /**
    * get the PATH_INFO for this request
    *
    * @return string
    */
    public function getPathInfo()
    {
        if ($pathInfo = $this->server->get('PATH_INFO'))
        {
            return $pathInfo;
        }

        return $this->getRequestUri(true) ?? $this->pathInfo;
    }

   /**
    * get the path for this request
    *
    * @return string
    */
    public function getPath()
    {
        $pattern = trim($this->getPathInfo(), '/');

        return $pattern == '' ? '/' : $pattern;
    }

   /**
    * get the uri for this request
    *
    * @return string
    */
    public function getRequestUri($withoutBasePath = false)
    {
        $uri = $this->server->get('REQUEST_URI');

        if ($withoutBasePath)
        {
            $uri = '/' . trim(str_replace(basename(basePath()), '', $uri), '/');
        }

        return $uri ?? $this->requestUri;
    }

   /**
    * get short request uri
    *
    * @return string
    */
    public function getShortRequestUri()
    {
        return str_replace(
            '/' . mb_strtolower(app_name()), '', $this->getRequestUri()
        );
    }

    /**
    * Get the current decoded path info for the request.
    *
    * @return string
    */
    public function decodePath()
    {
        return rawurldecode($this->getPath());
    }

    /**
    * Chack if request is ajax
    *
    * @return string
    */
    public function isAjax()
    {
        return $this->server->get('requestedWith');
    }
}
