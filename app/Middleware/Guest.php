<?php

Namespace App\Middleware;

use Xcholars\Http\Request;

use Xcholars\Support\Proxies\Auth;

use Xcholars\Support\Proxies\Response;

use Closure;

class Guest
{
   /**
    * Handle the user request.
    *
    * @param object \Xcholars\Http\Request  $request
    * @param object Closure $next
    * @return mixed
    */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check())
        {
            if ($request->isAjax())
            {
                return Response::withAjaxRedirect('/signin');
            }

            return Response::withRedirect('/signin');
        }

        return $next($request);
    }
}
