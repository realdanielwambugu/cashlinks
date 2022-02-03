<?php

Namespace App\Controllers;

use Xcholars\Http\Controller;

use Xcholars\Http\Request;

use Xcholars\Http\Response;

use Xcholars\Support\Proxies\Auth;

use App\Models\User;

class LoginController extends Controller
{
    use \App\Traits\HasValidation;

    public function authenticate(Request $request, Response $response)
    {
        if ($error = $this->isInvalid('login', $request))
        {
            return $error;
        }

        $credentials = $request->only('username', 'password');

        if (Auth::attemptWith($credentials))
        {
            return $response->withAjaxRedirect('/');
        }

        return $response->withError('wrong username or password');
    }

}
