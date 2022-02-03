<?php

Namespace App\Controllers;

use Xcholars\Http\Controller;

use Xcholars\Http\Request;

use Xcholars\Http\Response;

use Xcholars\Support\Proxies\Hash;

use App\Models\User;

use Xcholars\Support\Proxies\Auth;

class SignupController extends Controller
{
    use \App\Traits\HasValidation;

    public function create(Request $request, Response $response)
    {
        if ($error = $this->isInvalid('signup', $request))
        {
            return $error;
        }

        $request->post->set('password', Hash::make($request->password));

        $user = User::create($request->except('confirmPassword'));

        Auth::login($user);

        return $response->withAjaxRedirect('/');
    }

}
