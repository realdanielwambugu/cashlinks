<?php

Namespace Xcholars\Validation;

use InvalidArgumentException;

class Mappings
{
   /**
    * mappings classes namespace.
    *
    * @var string
    */
    private $namespace;

   /**
    * The Validation rules mappings for the application.
    *
    * @var array
    */
    private $mappings = [];

   /**
    * Loaded Mappings instances
    *
    * @var array
    */
    private $loadedMappings = [];

   /**
    * default error messages for rules
    *
    * @var array
    */
    private $defaultMessages = [
        'required' => ':field is required',
        'min' => ':field must be minimumn of :satisfier characters',
        'max' => ':field must be maximum of :satisfier characters',
        'email' => ':field address is invalid',
        'alpha' => ':field must contain only letters and numbers',
        'match' => ':satisfier should match',
        'unique' => ':field is already taken',
    ];

   /**
    * set mappings to location (path & namespace)
    *
    * @return void
    */
    public function setMappings($mappings)
    {
        $this->mappings = $mappings;
    }

   /**
    * set mappings classes namespace
    *
    * @return void
    */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

   /**
    * get mappings for the given activity
    *
    * @return object
    */
    public function getMappingsFor($activity)
    {
        if ($mappings = $this->loadedMappings[$activity] ?? null)
        {
            return $mappings;
        }

        if (!$mappings = $this->mappings[$activity] ?? null)
        {
            $mappings = $this->getMappingsClassName($activity);
        }

        if(!$mappings)
        {
            throw new InvalidArgumentException(
                "Validation Mappings for [{$activity}] not found"
            );
        }

        return $this->loadedMappings[$activity] = new $mappings;
    }

   /**
    * get the activity mappings classname.
    *
    * @return string
    */
    public function getMappingsClassName($activity)
    {
        return $this->namespace . '\For' . ucfirst($activity);
    }

   /**
    * get rules for the given activity
    *
    * @return array
    */
    public function getRulesFor($activity)
    {
        return $this->getMappingsFor($activity)->rules();
    }

   /**
    * get error messages for the given rule and activity
    *
    * @return string
    */
    public function getMessageFor($rule, $activity = null)
    {
        $message = null;

        if (!is_null($activity))
        {
            $mappings = $this->getMappingsFor($activity);

            $message = $mappings->messages($rule);
        }

        return $message ?? $this->defaultMessages[$rule];
    }


}
