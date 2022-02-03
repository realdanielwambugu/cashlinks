<?php

Namespace Xcholars\Routing\Validators;

use Xcholars\Routing\Route;

use Xcholars\Http\Request;

class MethodValidator implements ValidatorContract
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
        return in_array($request->getMethod(), $route->getMethods());
    }
}
