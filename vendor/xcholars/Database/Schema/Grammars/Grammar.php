<?php

Namespace Xcholars\Database\Schema\Grammars;

use Xcholars\Support\Fluent;

use Xcholars\Database\Schema\Blueprint;

use Xcholars\Database\Grammar as BaseGrammar;

abstract class Grammar extends BaseGrammar
{
   /**
    * Compile the blueprint's column definitions.
    *
    * @param object Xcholars\Database\Schema\Blueprint $blueprint
    * @return array
    */
    protected function getColumns(Blueprint $blueprint)
    {
        $columns = [];
        
        foreach ($blueprint->getAddedColumns() as $column)
        {
            $sql = $column->name . ' ' . $this->getType($column);

            $columns[] = $this->addModifiers($sql, $blueprint, $column);
        }

        return $columns;
    }

   /**
    * Add the column modifiers to the definition.
    *
    * @param string  $sql
    * @param object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param object Xcholars\Support\Fluent  $column
    * @return string
    */
    protected function addModifiers($sql, Blueprint $blueprint, Fluent $column)
    {
        foreach ($this->modifiers as $modifier)
        {
            if (method_exists($this, $method = "modify{$modifier}"))
            {
                $sql .= call_user_func([$this, $method], $blueprint, $column);
            }
        }

        return $sql;
    }

   /**
    * Get the SQL for the column data type.
    *
    * @param object Xcholars\Support\Fluent  $column
    * @return string
    */
    protected function getType(Fluent $column)
    {
        $method = 'type' . ucfirst($column->type);

        return  call_user_func([$this, $method], $column);
    }

   /**
    * Format a value so that it can be used in "default" clauses.
    *
    * @param  mixed  $value
    * @return string
    */
    protected function getDefaultValue($value)
    {
        return is_bool($value)
                    ? "'" . (int) $value . "'"
                    : "'" . (string) $value . "'";
    }

}
