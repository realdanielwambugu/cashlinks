<?php

Namespace Xcholars\Http;

use Xcholars\Http\Collections\HeaderCollection;

use Xcholars\Routing\UriGenerator;

use Xcholars\Http\Session\Manager as Session;

use Xcholars\Http\Traits\HasViews;

class Response implements ResponseContract
{

    use HasViews;

    /**
    * The [$_COOKIE] parameters
    *
    * @var object Xcholars/Http/Session/Manager
    */
    private $session;

    /**
    * The HeaderCollection instance
    *
    * @var object Xcholars/Http/Collections/HeaderCollection
    */
    private $headers;

    /**
    * The HeaderCollection instance
    *
    * @var object Xcholars/Routing/UriGenerator
    */
    private $generator;

    /**
    * The response status code
    *
    * @var int
    */
    private $statusCode;

    /**
    * The Response body/content
    *
    * @var mixed
    */
    private $content;

   /**
    * create new instance of Response
    *
    * @param object Xcholars\Routing\UriGenerator
    * @return void
    */
    public function __construct(UriGenerator $generator)
    {
        $this->generator = $generator;

        $this->headers = new HeaderCollection();
    }

   /**
    * set response headers
    *
    * @param string $content
    * @param int $statusCode
    * @param array $headers
    * @return $this
    */
    public function with($content ='', $statusCode = 200, array $headers = [])
    {
        $this->setContent($content);

        $this->headers->setHeaders($headers);

        $this->setStatusCode($statusCode);

        return $this;
    }

   /**
    * Redirect to route uri.
    *
    * @param string $route
    * @param array $parameters
    * @return $this
    */
    public function withRedirect($routeNameOrUri, $parameters = [])
    {
        $path = $this->generator->preparePath($routeNameOrUri, $parameters);

        $base = basePath() ? basePath() . DIRECTORY_SEPARATOR : DIRECTORY_SEPARATOR;

        $path =  $base . trim($path, '/\\');

        $this->with($this->getRedirectContent($path), 302, ['Location' => $path]);

        return $this;
    }

   /**
    * ajax Redirect to route uri.
    *
    * @param string $route
    * @return $this
    */
    public function withAjaxRedirect($routeNameOrUri)
    {
        $this->setContent("location:{$routeNameOrUri}");

        return $this;
    }

   /**
    * Get redirect content
    *
    * @param string $route
    * @param array $parameters
    * @return $this
    */
    private function getRedirectContent($path)
    {
        return sprintf('
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8" />
                    <meta http-equiv="refresh" content="0;url=\'%1$s\'" />
                    <title>Redirecting to %1$s</title>
                </head>
                    <body>
                        Redirecting to <a href="%1$s">%1$s</a>.
                    </body>
                </html>', htmlspecialchars($path, ENT_QUOTES, 'UTF-8'));

    }

   /**
    * set response headers
    *
    * @param string $name
    * @param string $value
    * @param string $expire
    * @return $this
    */
    public function withCookie($name, $value = '', $expire = '')
    {
        $cookies = is_array($name) ? $name : [$name => [$value, $expire]];

        $this->headers->setCookies($cookies);

        return $this;
    }

   /**
    * set response headers
    *
    * @param string $text
    * @return $this
    */
    public function withHeader($name, $value = '')
    {
        $headers = is_array($name) ? $name : [$name => $value];

        $this->headers->setHeaders($headers);

        return $this;
    }

   /**
    * set the response content/body
    *
    * @param string $content
    * @return $this
    */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

   /**
    * set session instance
    *
    * @param object Xcholars\Http\Session\Manager $session;
    * @return void
    */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

   /**
    * response with an error message
    *
    * @param string $message
    * @return $this
    */
    public function withError($message)
    {
        $this->setContent("flush:
                <span style='color:red; font-weight:400;'>
                    {$message}
                <span>
            ");

        return $this;
    }

   /**
    * response with an succes message
    *
    * @param string $message
    * @return $this
    */
    public function withSuccess($message)
    {
        $this->setContent("flush:
                <span style='color:lightgreen; font-weight:400;'>
                    {$message}
                <span>
            ");

        return $this;
    }

   /**
    * response with a flush message
    *
    * @param string $message
    * @return $this
    */
    public function withFlush($message)
    {
        $this->setContent('flush:' . $message);

        return $this;
    }

   /**
    * add response status code
    *
    * @param int $code
    * @return $this
    */
    public function setStatusCode($code)
    {
        $this->statusCode = $code;

        if ($this->isInvalid())
        {
            throw new \InvalidArgumentException(
                sprintf('The HTTP status code "%s" is not valid.', $code)
            );
        }

        return $this;
    }

   /**
    * check if response code is invalid?
    *
    * @return bool
    */
    private function isInvalid()
    {
        return $this->statusCode < 100 || $this->statusCode >= 600;
    }

   /**
    * Prepares the Response before it is sent to the client.
    *
    * @return $this
    */
    public function prepare()
    {
        if (!$this->headers->has('Content-Type'))
        {
            $this->headers->set('Content-Type', 'text/html');
        }

        if (!$this->headers->has('charset'))
        {
            $this->headers->set('charset', 'UTF-8');
        }

        $this->headers->set(
           'Content-Type',
          $this->headers->get('Content-Type') . '; charset=' .
          $this->headers->get('charset')
        );

        $this->headers->remove('charset');

        return $this;

    }

   /**
    * Sends HTTP headers and content.
    *
    * @return $this
    */
    public function send()
    {
        $this->session->getStore()->setSessions();

        $this->sendHeaders();

        $this->sendContent();

        return $this;
    }

   /**
    * Sends response content to the client.
    *
    * @return $this
    */
    public function sendContent()
    {
        echo $this->content;

        return $this;
    }

   /**
    * Sends HTTP Response headers.
    *
    * @return $this
    */
    public function sendHeaders()
    {
        if (headers_sent())
        {
            return $this;
        }

        http_response_code($this->statusCode);

        $this->setCookieHeaders();

        foreach ($this->headers->all() as $name => $value)
        {
            header("{$name}:{$value}", true, $this->statusCode);
        }

       return $this;
    }

   /**
    * Sends HTTP cookie Response headers.
    *
    * @return void
    */
    private function setCookieHeaders()
    {
        foreach ($this->headers->getCookies() as $name => $values)
        {
            [$value, $expire] = $values;

            setcookie($name, $value, $expire);
        }

    }

}
