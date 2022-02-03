<?php

Namespace Xcholars\Routing\Validators;

use Xcholars\Routing\Route;

use Xcholars\Http\Request;

interface ValidatorContract
{
    /**
    * Validate a given rule against a route and request.
    *
    * @param object Xcholars\Routing\Route $route
    * @param object Xcholars\Http\Request $request
    * @return bool
    */
    public function matches(Route $route, Request $request);
}
