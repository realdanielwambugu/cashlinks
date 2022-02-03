<?php

Namespace App\Controllers;

use Xcholars\Http\Controller;

use Xcholars\Http\Request;

use Xcholars\Http\Response;

use Xcholars\Support\Proxies\Auth;

use App\Models\User;

class ProfileController extends Controller
{
    use \App\Traits\HasValidation;

    public function show(Request $request, Response $response)
    {
         return $response->withView(
           'profilw',
           [
               'route' => $request->getShortRequestUri(),
               'user' => Auth::user(),
           ]
         );
    }

    public function delete(Request $request, Response $response)
    {

    }

}
