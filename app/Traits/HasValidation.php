<?php

Namespace App\Traits;

use Xcholars\Http\Request;

use Xcholars\Support\Proxies\Response;


trait HasValidation
{
    function isInvalid($requestName, Request $request)
    {
        $validation = $this->validate($request)->for($requestName);

        if ($validation->fails())
        {
            return Response::withError($validation->errors()->first());
        }

        return false;
    }

}
