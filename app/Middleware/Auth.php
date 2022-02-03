<?php

Namespace App\Middleware;

use Xcholars\Http\Request;

use Xcholars\Support\Proxies\Response;

use Closure;

use Xcholars\Support\Proxies\Auth as Login;

class Auth
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
        if (Login::check())
        {
            if ($request->isAjax())
            {
                return Response::withAjaxRedirect('/');
            }

            return Response::withRedirect('/');
        }

        return $next($request);
    }
}
