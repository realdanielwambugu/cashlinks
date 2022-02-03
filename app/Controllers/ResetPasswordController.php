<?php

Namespace App\Controllers;

use Xcholars\Http\Controller;

use Xcholars\Http\Request;

use Xcholars\Http\Response;

use Xcholars\Support\Proxies\Auth;

use Xcholars\Support\Proxies\Session;


use App\Models\User;

class ResetPasswordController extends Controller
{
    use \App\Traits\HasValidation;

    public function verifyResetCode(Request $request, Response $response)
    {
        if (!$user = User::where('code', $request->code)->first())
        {
            return $response->withError('Invalid or expired reset code');
        }

        $request->session->set('passwordResetCode', $request->code);

        return $response->withAjaxRedirect('/resetPassword');
    }

    public function reset(Request $request, Response $response)
    {

        if ($error = $this->isInvalid($request, 'signup'))
        {
            return $error;
        }

        $user = User::where(
               'code',  $request->session->get('passwordResetCode')
            )->first();

        if (!$user)
        {
            return $response->withError('Invalid or expired reset code');
        }

        $user->password = $request->password;

        $user->code = '';

        $user->save();

        return $response->withSuccess('password Reset succcess');
    }

}
