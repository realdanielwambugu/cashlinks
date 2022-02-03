<?php

Namespace Xcholars\Routing\Group;

use Xcholars\Exceptions\NotFoundException;

use Closure;

class GroupLoader
{
   /**
    * load the routes defined in the given groups.
    *
    * @param string|Closure $routes
    * @return void
    */
    public function load($routes)
    {
        if ($routes instanceof Closure)
        {
            $this->resolveClosureRoutes($routes);
        }
        else
        {
            $this->loadRouteFile($routes);
        }
    }

   /**
    * register routes wrapped with a Closure .
    *
    * @param Closure $closure
    * @return void
    */
    private function resolveClosureRoutes(Closure $closure)
    {
        call_user_func($closure);
    }

   /**
    * Require the given routes file.
    *
    * @param string $file
    * @return void
    */
    private function loadRouteFile($file)
    {
        if (!file_exists($file))
        {
            throw new NotFoundException("Routes map File [{$file}] not found");
        }

        require_once $file;
    }

}
