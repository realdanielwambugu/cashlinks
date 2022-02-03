<?php

Namespace Xcholars\Routing;

use Symfony\Modified\Routing\RouteCompiler as SymfonyRouteCompiler;

use LogicException;

class RouteCompiler
{
   /**
    * The host pattern to match
    *
    * @var string
    */
    private $host;

   /**
    * The path pattern to match
    *
    * @var string
    */
    private $path = '/';

   /**
 	* An array of default/optional parameter values
 	*
 	* @var array
 	*/
 	private $optionalParameters = [];

   /**
 	* An array of patterns for parameters (regexes)
 	*
 	* @var array
 	*/
 	private $patterns = [];

    /**
  	*  An array of options (eg: ecncoding utf8)
  	*
  	* @var array
  	*/
  	private $options = [];

   /**
    * A required HTTP method or an array of restricted methods
    *
    * @var array
    */
    private $methods = [];

   /**
    * compile routes with the given elements
    *
    * @return object Symfony\Modified\Routing\CompiledRoute
    */
    public function compileWith(array $elements)
    {
        $this->setElements($elements);

        return $this->compile();
    }

   /**
    * set the elements for the route being compiled
    *
    * @return void
    */
    public function setElements(array $elements)
    {
        [$path, $optionalParameters, $patterns, $options, $methods] = $elements;

        $this->path = $path;

        $this->optionalParameters = $optionalParameters;

        $this->patterns = $patterns;

        $this->options = $options;

        $this->methods = $methods;
    }

   /**
    * gets the pattern for the path.
    *
    * @return string
    */
    public function getPath()
    {
        return $this->path;
    }

   /**
    * Sets the pattern for the path.
    *
    * @param string $path
    * @return $this
    */
    public function setPath($path)
    {
        return $this->path = $path;
    }

   /**
    * Checks if a default value is set for the given variable.
    *
    * @param string $name
    * @return bool
    */
    public function hasDefault($name)
    {
        return array_key_exists($name, $this->optionalParameters);
    }

   /**
    * Gets a default value.
    *
    * @param string $key
    * @return string
    */
    public function getDefault($key)
    { 
        return $this->optionalParameters[$key] ?? '';
    }

   /**
    * Returns the pattern for the host.
    *
    * @return string
    */
    public function getHost()
    {
        return $this->host ?? '';
    }

   /**
    * Get an option value.
    *
    * @param string $key
    * @return mixed
    */
    public function getOption($key)
    {
        return $this->options[$key] ?? '';
    }

   /**
    * Returns the requirement for the given key.
    *
    * @param string $key
    * @return string|null
    */
    public function getRequirement($key)
    {
        return $this->patterns[$key] ?? null;
    }

   /**
    * get all the requirements/patterns
    *
    * @return array
    */
    public function getRequirements()
    {
        return $this->patterns;
    }

   /**
    * Sets the requirements/patterns
    *
    * @param array $requirements The requirements
    * @return $this
    */
    public function setRequirements(array $requirements)
    {
        $this->patterns = $requirements;

        return $this;
    }

   /**
    * Compiles the route.
    *
    * @return object Symfony\Modified\Routing\CompiledRoute
    */
    public function compile()
    {
        return (new SymfonyRouteCompiler)->compile($this);
    }

}
