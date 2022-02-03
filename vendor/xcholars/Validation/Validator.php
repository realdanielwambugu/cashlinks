<?php

Namespace Xcholars\Validation;

use Xcholars\Support\Proxies\Str;

class Validator
{
   /**
    * The Validation rules mappings for the application.
    *
    * @var object Xcholars\Validation\Mappings
    */
    private $mappings;

   /**
    * The rules for the application.
    *
    * @var object Xcholars\Validation\Rules
    */
    private $rules;

   /**
    * Instance of Validation ErrorHandler
    *
    * @var object Xcholars\Validation\Rules
    */
    private $errorHandler;

   /**
    * data to be validated
    *
    * @var array
    */
    private $data = [];

   /**
    * Create new instance of the validator.
    *
    * @param object Xcholars\Validation\Rules
    * @param object Xcholars\Validation\Mappings
    * @param object Xcholars\Validation\ErrorHandler
    * @return void
    */
    public function __construct(Mappings $mappings, Rules $rules, ErrorHandler $errorHandler)
    {
        $this->mappings = $mappings;

        $this->rules = $rules;

        $this->errorHandler = $errorHandler;

        $rules->setValidator($this);
    }

   /**
    * set the data to be validated
    *
    * @param array $data
    * @return void
    */
    public function check($data)
    {
        $this->data = $data;
    }

   /**
    * get the data to be validated
    *
    * @param string $key
    * @return array
    */
    public function getData($key = null)
    {
        return $this->data[$key] ?? $this->data;
    }

   /**
    * set activity that need Validation
    *
    * @param string $activity
    * @return $this
    */
    public function for($activity)
    {
        $rules = $this->mappings->getRulesFor($activity);

        $this->valiadate($activity, $rules);

        return $this;
    }

   /**
    * check if the Validation has failed
    *
    * @return bool
    */
    public function fails()
    {
        return $this->errorHandler->hasErrors();
    }

   /**
    * get the Validation errors
    *
    * @param string $activity
    * @return object Xcholars\Validation\ErrorHandler
    */
    public function errors()
    {
        return $this->errorHandler;
    }

   /**
    * Validate the given activity data
    *
    * @param string $activity
    * @param array $rules
    * @return void
    */
    public function valiadate($activity, $rules)
    {
        $fields = $this->prepareFields($activity, $rules);

        foreach ($fields as $field)
        {
            $this->matchToRules($field);
        }

    }

   /**
    * matach field data to the defined rules
    *
    * @param string $field
    * @param array $rules
    * @return void
    */
    public function matchToRules($field)
    {
        foreach ($field['rules'] as $rule => $satisfier)
        {
            if(empty($rule)) continue;

            $result = call_user_func([$this->rules, $rule],
                $field['name'], $field['value'], $satisfier
            );

            if (!$result)
            {
                $this->addErrors($field, $rule, $satisfier);
            }
        }
    }

   /**
    * adda Validation errors to the ErrorHandler
    *
    * @param string $activity
    * @param string $rule
    * @param string $satisfier
    * @return void
    */
    public function addErrors($field, $rule, $satisfier)
    {
        $this->errorHandler->setError(
            str_replace(
            [':field', ':satisfier'],

            [ucfirst($field['name']), $satisfier],

            $this->mappings->getMessageFor($rule)),

            $field['name']
        );
    }

   /**
    * Preparea fields foe validation by adding the provided rules
    *
    * @param string $activity
    * @param array $rules
    * @return array
    */
    public function prepareFields($activity, $rules)
    {
        $matches = [];

        foreach ($this->data as $field => $value)
        {
           $matches[$field] = [
               'name' => $field,
               'rules' => $this->parseRules($rules[$field] ?? ''),
               'value' => $value,
           ];
        }

        return $matches;
    }

   /**
    * prase rule strings to an array
    *
    * @param array $rules
    * @return array
    */
    public function parseRules($rules)
    {
        $rules =  explode('|', $rules);

        $result = [];

        foreach ($rules as $rule)
        {
            if (Str::contains($rule, ':'))
            {
                [$rule, $satisfier] = Str::split($rule, ':');

                $result[$rule] = $satisfier;
            }
            else
            {
                $result[$rule] = true;
            }
        }

        return $result;
    }


}
