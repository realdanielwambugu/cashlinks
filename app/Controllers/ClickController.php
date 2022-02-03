<?php

Namespace App\Controllers;

use Xcholars\Http\Controller;

use Xcholars\Http\Request;

use Xcholars\Http\Response;

use Xcholars\Support\Proxies\Auth;

use App\Models\Thread;

class ClickController extends Controller
{
    use \App\Traits\HasValidation;

    public function create(Request $request, Response $response)
    {
        $thread = Thread::find($request->thread_id);

        $thread->clicks = $thread->clicks + 1;

        $thread->save();

        return $response->withSuccess('ok');
    }

}
