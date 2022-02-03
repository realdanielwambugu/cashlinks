<?php

Namespace Xcholars\Database;

use Xcholars\Database\Query\Expression;

abstract class Grammar
{
   /**
    * Get the format for database stored dates.
    *
    * @return string
    */
    public function getDateFormat()
    {
        return 'Y-m-d H:i:s';
    }

   /**
    * Get the appropriate query parameter place-holder for a value.
    *
    * @param  mixed  $value
    * @return string
    */
    public function parameter($value)
    {
        return $this->isExpression($value) ? $this->getValue($value) : '?';
    }

   /**
    * Get the value of a raw expression.
    *
    * @param object xcholars\Database\Query\Expression  $expression
    * @return string
    */
    public function getValue($expression)
    {
        return $expression->getValue();
    }

   /**
    * Create query parameter place-holders for an array.
    *
    * @param  array  $values
    * @return string
    */
    public function parameterize(array $values)
    {
        return implode(', ', array_map([$this, 'parameter'], $values));
    }

   /**
    * Determine if the given value is a raw expression.
    *
    * @param  mixed  $value
    * @return bool
    */
    public function isExpression($value)
    {
        return $value instanceof Expression;
    }

}
