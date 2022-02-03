<?php

Namespace Xcholars\Routing;

use Xcholars\Http\Response;

use Xcholars\Http\Request;

class ViewContoller
{
   /**
    * Request instance
    *
    * @var object Xcholars\Http\Request
    */
    private $request;

   /**
    * Response instance
    *
    * @var object Xcholars\Http\Response
    */
    private $response;

   /**
    * Create new Route instance.
    *
    * @param object Xcholars\Http\Request
    * @param object Xcholars\Http\Response
    * @return void
    */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;

        $this->response = $response;
    }

   /**
    * Run the controller
    *
    * @return object Xcholars\Http\Response
    */
    public function __invoke()
    {
        return $this->response->withView(
               $this->request->resolver->getDefault('view'),
               $this->request->resolver->getDefault('data')
        );
    }
}
