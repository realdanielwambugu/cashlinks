<?php

Namespace Xcholars\Routing;

use Xcholars\Provider\ServiceProvider;


class RouteServiceProvider extends ServiceProvider
{
   /**
	* register bindings with the service container.
	*
	* @return object
	*/
	public function register()
	{
		$this->app->singleton(\Xcholars\Routing\Group\GroupStack::class);

        $this->app->singleton(\Xcholars\Routing\RouteCollection::class);
	}

   /**
	* Activities to be performed after bindings are registerd.
	*
	* @return void
	*/
	public function boot()
	{

	}

}
