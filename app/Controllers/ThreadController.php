<?php

Namespace App\Controllers;

use Xcholars\Http\Controller;

use Xcholars\Http\Request;

use Xcholars\Http\Response;

use Xcholars\Support\Proxies\Auth;

use App\Models\Thread;

use App\Models\Comment;

class ThreadController extends Controller
{
    use \App\Traits\HasValidation;

    public function create(Request $request, Response $response)
    {
        if ($error = $this->isInvalid('thread', $request))
        {
            return $error;
        }

        $request->post->set('user_id', Auth::id());

        Thread::create($request->all());

        return $response->withSuccess('Link created successfully');
    }

    public function show(Request $request, Response $response)
    {
        $threads = [];

        if ($request->last_id)
        {
            $threads = Thread::where('id', '<', $request->last_id)
                             ->take(5)
                             ->orderBy('id', 'desc')
                             ->get();
        }

        if (empty($threads))
        {
            $threads = Thread::take(5)->orderBy('id', 'desc')->get();
        }

        return $response->withView(
          'home',
          [
          'route' => $request->getShortRequestUri(),
          'threads' => $threads,
          'prev_last_id' => $request->last_id
          ]
        );
    }

    public function delete(Request $request, Response $response)
    {
        $thread = Thread::find($request->thread_id)->drop();

        return $response->withRedirect('/');
    }



}
