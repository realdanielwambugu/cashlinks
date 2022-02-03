<?php

Namespace Xcholars\Routing;

use Xcholars\Http\Request;

class ParameterBinder
{
   /**
   	* The route instance
   	*
   	* @var object
   	*/
   	private $route;

   /**
    * Create new ParameterBinder instance.
    *
    * @param object Xcholars\Routing\Route
    * @return void
    */
    public function __construct(Route $route)
    {
        $this->route = $route;
    }

   /**
    * Get the parameter matches for the path portion of the URI.
    *
    * @param  object Xcholars\Http\Request $request
    * @return array
    */
    public function bindParameters(Request $request)
    {
        $path = '/' . ltrim($request->decodePath());

        preg_match($this->route->getCompiled()->getRegex(), $path, $matches);

        return $this->matchToKeys(array_slice($matches, 1));
    }

    /**
    * Combine a set of parameter matches with the route's keys.
    *
    * @param  array  $matches
    * @return array
    */
    protected function matchToKeys(array $matches)
    {
        if (empty($parameterNames = $this->route->getParameterNames()))
        {
            return [];
        }

        $parameters = array_intersect_key($matches, array_flip($parameterNames));

        return array_filter($parameters, function ($value)
        {
            return is_string($value) && strlen($value) > 0;

        });
    }

}
