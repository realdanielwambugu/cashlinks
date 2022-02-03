<?php

Namespace App\Controllers;

use Xcholars\Http\Controller;

use Xcholars\Http\Request;

use Xcholars\Http\Response;

use Xcholars\Support\Proxies\Auth;

use App\Models\Comment;

use App\Models\Reply;

class ReplyController extends Controller
{
    use \App\Traits\HasValidation;

    public function create(Request $request, Response $response)
    {
        if ($error = $this->isInvalid('reply', $request))
        {
            return $error;
        }

        Reply::create($request->all());

        return $response->withSuccess('Reply added successfully');
    }

    public function show(Request $request, Response $response)
    {
         return $response->withView(
           'replies',
           [
               'route' => $request->getShortRequestUri(),
               'comment' => Comment::find($request->thread_id),
           ]
         );
    }

    public function delete(Request $request, Response $response)
    {
        $reply = Reply::find($request->reply_id);

        $comment = $reply->comment;

        $reply->drop();

        return $response->withRedirect("/reply/{$comment->id}");
    }

}
