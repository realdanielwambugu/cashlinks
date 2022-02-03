<?php

Namespace Xcholars\Routing\Validators;

use Xcholars\Routing\Route;

use Xcholars\Http\Request;

class UriValidator implements ValidatorContract
{
    /**
    * Validate a given rule against a route and request.
    *
    * @param object Xcholars\Routing\Route $route
    * @param object Xcholars\Http\Request $request
    * @return bool
    */
    public function matches(Route $route, Request $request)
    {
        $path = rtrim($request->getPathInfo(), '/') ?: '/';
        
        return preg_match($route->getCompiled()->getRegex(), rawurldecode($path));
    }
}
