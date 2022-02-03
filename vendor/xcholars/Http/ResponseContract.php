<?php

Namespace Xcholars\Http;

use Xcholars\Routing\UriGenerator;

interface ResponseContract
{
   /**
    * create new instance of Response
    *
    * @param array $headers
    * @return void
    */
    public function __construct(UriGenerator $generator);

   /**
    * set response headers
    *
    * @param string $content
    * @param int $statusCode
    * @param array $headers
    * @return $this
    */
    public function with($content, $statusCode, array $headers = []);

   /**
    * Redirect to route uri.
    *
    * @param string $route
    * @param array $parameters
    * @return $this
    */
    public function withRedirect($routeNameOrUri, $parameters = []);
    
   /**
    * set response headers
    *
    * @param string $name
    * @param string $value
    * @param string $expire
    * @return $this
    */
    public function withCookie($name, $value = '', $expire = '');

   /**
    * set response headers
    *
    * @param string $text
    * @return $this
    */
    public function withHeader($name, $value = '');

   /**
    * set the response content/body
    *
    * @param string $content
    * @return $this
    */
    public function setContent($content);

   /**
    * add response status code
    *
    * @param int $code
    * @return $this
    */
    public function setStatusCode($code);

   /**
    * Prepares the Response before it is sent to the client.
    *
    * @return $this
    */
    public function prepare();

   /**
    * Sends HTTP headers and content.
    *
    * @return $this
    */
    public function send();

   /**
    * Sends response content to the client.
    *
    * @return $this
    */
    public function sendContent();

   /**
    * Sends HTTP Response headers.
    *
    * @return $this
    */
    public function sendHeaders();

}
