<?php

Namespace Xcholars\Validation;

use Xcholars\Support\Proxies\DB;

class Rules
{
   /**
    * The validator instance
    *
    * @var object Xcholars\Validation\Validator
    */
    private $validator;

   /**
    * set validator instance
    *
    * @param string $field
    * @param string $value
    * @param string $satisfier
    * @return void
    */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;
    }

   /**
    * check if email is valid
    *
    * @param string $field
    * @param string $value
    * @param string $satisfier
    * @return void
    */
    public function email($field, $value, $satisfier)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

   /**
    * Check if value is alpha-numeric
    *
    * @param string $field
    * @param string $value
    * @param string $satisfier
    * @return void
    */
    public function alpha($field, $value, $satisfier)
    {
        return ctype_alnum($value);
    }

   /**
    * Check if value is required/ not empty
    *
    * @param string $field
    * @param string $value
    * @param string $satisfier
    * @return void
    */
    public function required($field, $value, $satisfier)
    {
        return !empty(trim($value));
    }

   /**
    * ensure the value is more than or equal to a certain NO. of characters
    *
    * @param string $field
    * @param string $value
    * @param string $satisfier
    * @return void
    */
    public function min($field, $value, $satisfier)
    {
        return mb_strlen($value) >= $satisfier;
    }

   /**
    * ensure the value is less than or equal to a certain NO. of characters
    *
    * @param string $field
    * @param string $value
    * @param string $satisfier
    * @return void
    */
    public function max($field, $value, $satisfier)
    {
        return mb_strlen($value) <= $satisfier;
    }

   /**
    * Check if the satisfer exist in a database table
    *
    * @param string $field
    * @param string $value
    * @param string $satisfier
    * @return void
    */
    public function unique($field, $value, $satisfier)
    {
        return !DB::table($satisfier)->where($field, $value)->first();
    }

   /**
    * Check if the value matches satisfer
    *
    * @param string $field
    * @param string $value
    * @param string $satisfier
    * @return void
    */
    public function match($field, $value, $satisfier)
    {
       return $value === $this->validator->getData($satisfier);
    }

}
