<?php

Namespace Xcholars\Http;

use Xcholars\Http\Collections\InputCollection;

use Xcholars\Http\Collections\FileCollection;

use Xcholars\Http\Collections\HeaderCollection;

use Xcholars\Http\Collections\ServerCollection;

class RequestFactory extends AbstractFactory
{
    /**
    * Create request by populating it with superglobals
    *
    * @return void
    */
    public function createFromGlobals()
    {
        $request = $this->createRequest();

        $request->query =  new InputCollection($_GET);

        $request->post = new InputCollection($_POST);

        $request->cookie = new InputCollection($_COOKIE);

        $request->files = new FileCollection($_FILES);

        $request->server = new ServerCollection(
            array_merge($_SERVER,
            ['requestedWith' => $request->post->get('requestedWith')]
        ));

        $request->post->remove('requestedWith');

        $request->header = new HeaderCollection($request->server->getHeaders());

        return $request;
    }

   /**
    * Create new instance of request
    *
    * @return object Xcholars\Http\Request
    */
    public function createRequest()
    {
        return $this->app->make(Request::class);
    }

}
