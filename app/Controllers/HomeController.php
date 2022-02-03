<?php

Namespace App\Controllers;

use Xcholars\Http\Request;

use Xcholars\Http\Response;

class HomeController
{
   public function show(Request $request, Response $response)
   {
        return $response->withView(
          'welcome/home',
          ['route' => $request->getShortRequestUri()]
        );
   }

}
