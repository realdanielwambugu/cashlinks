<?php

Namespace App\Controllers;

use Xcholars\Http\Controller;

use Xcholars\Http\Request;

use Xcholars\Http\Response;

use App\Models\Thread;

use App\Models\Comment;

class CommentController extends Controller
{
    use \App\Traits\HasValidation;

    public function create(Request $request, Response $response)
    {
        if ($error = $this->isInvalid('comment', $request))
        {
            return $error;
        }

        Comment::create($request->all());

        return $response->withSuccess('comment added successfully');
    }

    public function show(Request $request, Response $response)
    {
         return $response->withView(
           'comments',
           [
               'route' => $request->getShortRequestUri(),
               'thread' => Thread::find($request->thread_id),
           ]
         );
    }

    public function delete(Request $request, Response $response)
    {
        $comment = Comment::find($request->comment_id);

        $thread = $comment->thread;

        $comment->drop();

        return $response->withRedirect("/comment/{$thread->id}");
    }

}
