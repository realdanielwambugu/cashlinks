<?php

Namespace Xcholars\Database\Query\Grammars;

use Xcholars\Database\Grammar as BaseGrammar;

use Xcholars\Database\Query\Builder;

abstract class Grammar extends BaseGrammar
{
    /**
    * The components that make up a select clause.
    *
    * @var array
    */
    protected $selectComponents = [
        'columns',
        'from',
        'wheres',
        'orders',
        'limit',
        'offset',
    ];

   /**
    * Compile a select query into SQL.
    *
    * @param object Xcholars\Database\Query\Builder  $query
    * @return string
    */
    public function compileSelect(Builder $query)
    {
        $original = $query->columns;

       if (is_null($query->columns))
       {
           $query->columns = ['*'];
       }

        $sql = trim($this->concatenate($this->compileComponents($query)));

        $query->columns = $original;

        return $sql;
    }

   /**
    * Compile an insert statement into SQL.
    *
    * @param object Xcholars\Database\Query\Builder  $query
    * @param  array  $values
    * @return string
    */
    public function compileInsert(Builder $query, array $values)
    {
        $table = $query->from;

        if (empty($values))
        {
            return "insert into {$table} default values";
        }

        if (!is_array(reset($values)))
        {
            $values = [$values];
        }

        $columns = implode(', ', array_keys(reset($values)));

        $parameters = implode(', ', array_map(function ($record)
        {
            return '(' . $this->parameterize($record) . ')';

        }, $values));

        return "insert into {$table} ({$columns}) values {$parameters}";
    }

   /**
    * Compile an update statement into SQL.
    *
    * @param object Xcholars\Database\Query\Builder  $query
    * @param  array  $values
    * @return string
    */
    public function compileUpdate(Builder $query, array $values)
    {
        $table = $query->from;

        $where = $this->compileWheres($query);

        $columns = $this->compileUpdateColumns($query, $values);

        return "update {$table} set {$columns} {$where}";
    }

   /**
    * Compile the columns for an update statement.
    *
    * @param object Xcholars\Database\Query\Builder  $query
    * @param  array  $values
    * @return string
    */
    protected function compileUpdateColumns(Builder $query, array $values)
    {
        $columns = array_keys($values);

        return implode(', ', array_map(function($column, $value)
        {
            return $column . ' = ' . $this->parameter($value);

        }, $columns, $values));
    }

   /**
    * Compile a delete statement into SQL.
    *
    * @param  object Xcholars\Database\Query\Builder $query
    * @return string
    */
    public function compileDelete(Builder $query)
    {
        $table = $query->from;

        $where = $this->compileWheres($query);

        return "delete from {$table} {$where}";
    }

    /**
    * Prepare the bindings for an update statement.
    *
    * @param  array  $bindings
    * @param  array  $values
    * @return array
    */
    public function prepareBindingsForUpdate(array $bindings, array $values)
    {
        unset($bindings['select']);

        return array_values(array_merge($values, array_flatten($bindings)));
    }

    /**
    * Prepare the bindings for a delete statement.
    *
    * @param  array  $bindings
    * @return array
    */
    public function prepareBindingsForDelete(array $bindings)
    {
        unset($bindings['select']);

        return array_flatten($bindings);
    }

   /**
    * Compile the components necessary for a select clause.
    *
    * @param object Xcholars\Database\Query\Builder  $query
    * @return array
    */
    protected function compileComponents(Builder $query)
    {
        $sql = [];

        foreach ($this->selectComponents as $component)
        {
            if (isset($query->$component))
            {
                $method = 'compile' . ucfirst($component);

                $sql[$component] = call_user_func(
                                [$this, $method], $query, $query->$component
                             );

            }
        }

        return $sql;
    }

   /**
    * Compile the "where" portions of the query.
    *
    * @param  object Xcholars\Database\Query\Builder  $query
    * @return string
    */
    protected function compileWheres(Builder $query)
    {
        if (is_null($query->wheres))
        {
            return '';
        }

        if (count($sql = $this->compileWheresToArray($query)) > 0)
        {
            return $this->concatenateWhereClauses($query, $sql);
        }

        return '';
    }

   /**
    * Get an array of all the where clauses for the query.
    *
    * @param object Xcholars\Database\Query\Builder  $query
    * @return array
    */
    protected function compileWheresToArray($query)
    {
        return array_map(function ($where) use($query)
        {
            $method = "where" . $where['type'];

            $condition = call_user_func([$this, $method], $query, $where);

            return $where['boolean'] . ' ' . $condition;

        }, $query->wheres);

    }

   /**
    * Compile the "order by" portions of the query.
    *
    * @param object Xcholars\Database\Query\Builder  $query
    * @param  array  $orders
    * @return string
    */
    protected function compileOrders(Builder $query, $orders)
    {
        if (! empty($orders))
        {
            $columnAndDirection = $this->compileOrdersToArray($query, $orders);

            return 'order by '.implode(', ', $columnAndDirection);
        }

        return '';
    }

   /**
    * Compile the "offset" portions of the query.
    *
    * @param object Xcholars\Database\Query\Builder  $query
    * @param  int  $offset
    * @return string
    */
    protected function compileOffset(Builder $query, $offset)
    {
        return 'offset ' . (int) $offset;
    }

   /**
    * Compile the "limit" portions of the query.
    *
    * @param object Xcholars\Database\Query\Builder $query
    * @param  int  $limit
    * @return string
    */
    protected function compileLimit(Builder $query, $limit)
    {
        return 'limit '.(int) $limit;
    }

   /**
    * Compile the query orders to an array.
    *
    * @param object Xcholars\Database\Query\Builder  $query
    * @param  array  $orders
    * @return array
    */
    protected function compileOrdersToArray(Builder $query, $orders)
    {
        return array_map(function ($order)
        {
            return $order['sql'] ?? $order['column'] . ' ' . $order['direction'];

        }, $orders);
    }

   /**
    * Compile a "between" where clause.
    *
    * @param object xcholars\Database\Query\Builder  $query
    * @param  array  $where
    * @return string
    */
    protected function whereBetween(Builder $query, $where)
    {
        $between = $where['not'] ? 'not between' : 'between';

        $min = $this->parameter(reset($where['values']));

        $max = $this->parameter(end($where['values']));

        return $where['column'] . ' ' . $between . ' ' . $min . ' and ' . $max;
    }

   /**
    * Compile a basic where clause.
    *
    * @param object Xcholars\Database\Query\Builder  $query
    * @param  array  $where
    * @return string
    */
    protected function whereBasic(Builder $query, $where)
    {
        $value = $this->parameter($where['value']);

        return $where['column'] . ' ' . $where['operator'] . ' ' . $value;
    }

    /**
    * Compile a "where in" clause.
    *
    * @param object xcholarsDatabase\Query\Builder  $query
    * @param  array  $where
    * @return string
    */
    protected function whereIn(Builder $query, $where)
    {
        if (! empty($where['values']))
        {
            $parameters = $this->parameterize($where['values']);

            return $where['column'] . ' in (' . $parameters . ')';
        }

        return '0 = 1';
    }

    /**
    * Compile a "where not in" clause.
    *
    * @param object xcholars\Database\Query\Builder  $query
    * @param  array  $where
    * @return string
    */
    protected function whereNotIn(Builder $query, $where)
    {
        if (! empty($where['values']))
        {
            $parameters = $this->parameterize($where['values']);

            return $where['column'] . ' not in (' . $parameters . ')';
        }

        return '1 = 1';
    }

   /**
    * Compile a where clause comparing two columns..
    *
    * @param object Xcholars\Database\Query\Builder  $query
    * @param  array  $where
    * @return string
    */
    protected function whereColumn(Builder $query, $where)
    {
        return $where['first'] . ' ' . $where['operator'] . ' ' . $where['second'];
    }

   /**
    * Compile a nested where clause.
    *
    * @param object Xcholars\Database\Query\Builder  $query
    * @param  array  $where
    * @return string
    */
    protected function whereNested(Builder $query, $where)
    {
        return '('.substr($this->compileWheres($where['query']), 6).')';
    }

   /**
    * Compile a "where null" clause.
    *
    * @param object xcholars\Database\Query\Builder  $query
    * @param  array  $where
    * @return string
    */
    protected function whereNull(Builder $query, $where)
    {
        return $where['column'] . ' is null';
    }

   /**
    * Compile a "where not null" clause.
    *
    * @param object xcholars\Database\Query\Builder  $query
    * @param  array  $where
    * @return string
    */
    protected function whereNotNull(Builder $query, $where)
    {
        return $where['column'] . ' is not null';
    }

   /**
    * Concatenate an array of segments, removing empties.
    *
    * @param array $segments
    * @return string
    */
    private function concatenate($segments)
    {
        return implode(' ', array_filter($segments, function ($value)
        {
            return (string) $value !== '';
        }));
    }

   /**
    * Format the where clause statements into one string.
    *
    * @param object Xcholars\Database\Query\Builder $query
    * @param  array  $sql
    * @return string
    */
    protected function concatenateWhereClauses(Builder $query, $sql)
    {
        $conjunction = 'where';

        return $conjunction . ' ' . $this->removeLeadingBoolean(implode(' ', $sql));
    }

   /**
    * Remove the leading boolean from a statement.
    *
    * @param  string  $value
    * @return string
    */
    protected function removeLeadingBoolean($value)
    {
        return preg_replace('/and |or /i', '', $value, 1);
    }

   /**
    * Compile the "from" portion of the query.
    *
    * @param object Xcholars\Database\Query\Builder  $query
    * @param  string  $table
    * @return string
    */
    protected function compileFrom(Builder $query, $table)
    {
        return 'from ' . $table;
    }

   /**
    * Compile the "select *" portion of the query.
    *
    * @param object Xcholars\Database\Query\Builder  $query
    * @param  array  $columns
    * @return string|null
    */
    protected function compileColumns(Builder $query, $columns)
    {
        $select = 'select ';

        return $select . implode(',', $columns);
    }
}
