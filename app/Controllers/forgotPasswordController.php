<?php

Namespace App\Controllers;

use Xcholars\Http\Controller;

use Xcholars\Http\Request;

use Xcholars\Http\Response;

use App\Models\User;

use Xcholars\Support\Proxies\Event;

use App\Events\EmailExist;

class forgotPasswordController extends Controller
{
    public function verify(Request $request, Response $response)
    {
        if (!$user = User::where('email', $request->email)->first())
        {
            return $response->withError('Email does not exist');
        }

        $user->code = rand(1000, 9000);

        $user->save();

        Event::dispatch(new EmailExist($user));

        return $response->withAjaxRedirect('/confirmResetCode');
    }

}
