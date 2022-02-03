<?php

Namespace Xcholars\Http;

class ResponseFactory extends AbstractFactory
{

    public function makeResponseWith($content, $statusCode, $headers)
    {
        return $this->app->make(Response::class)
                    ->with($content, $statusCode, $headers);
    }



}
