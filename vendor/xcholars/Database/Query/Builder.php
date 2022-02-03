<?php

Namespace Xcholars\Database\Query;

use Xcholars\Database\Connections\ConnectionContract;

use Closure;

use Xcholars\Support\Contracts\ArrayAble;

use Xcholars\Database\Traits\BuildsQueries;

use Xcholars\Database\Orm\Builder as OrmBuilder;

use Xcholars\Database\Orm\Relation;

use InvalidArgumentException;

class Builder
{
    use BuildsQueries;

   /**
    * The database connection instance.
    *
    * @var object Xcholars\Database\ConnectionInterface
    */
    public $connection;

    /**
    * The database query grammar instance.
    *
    * @var object Xcholars\Database\Query\Grammars\Grammar
    */
    public $grammar;

   /**
    * The database query post processor instance.
    *
    * @var object Xcholars\Database\Query\Processors\Processor
    */
    public $processor;

   /**
    * The table which the query is targeting.
    *
    * @var string
    */
    public $from;

   /**
    * The columns that should be returned.
    *
    * @var array
    */
    public $columns;

   /**
    * The where constraints for the query.
    *
    * @var array
    */
    public $wheres = [];

   /**
    * The maximum number of records to return.
    *
    * @var int
    */
    public $limit;

   /**
    * The orderings for the query.
    *
    * @var array
    */
    public $orders;

   /**
    * The number of records to skip.
    *
    * @var int
    */
    public $offset;

   /**
    * The current query value bindings.
    *
    * @var array
    */
    public $bindings = [
        'select' => [],
        'from' => [],
        'where' => [],
        'order' => [],
    ];

   /**
    * All of the available clause operators.
    *
    * @var array
    */
    public $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=', '<=>',
        'like', 'like binary', 'not like', 'ilike',
        '&', '|', '^', '<<', '>>',
        'rlike', 'not rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to',
        'not similar to', 'not ilike', '~~*', '!~~*',
    ];

   /**
    * Create a new query builder instance.
    *
    * @param object Xcholars\Database\ConnectionInterface  $connection
    * @return void
    */
    public function __construct(ConnectionContract $connection)
    {
        $this->connection = $connection;

        $this->grammar   = $connection->getQueryGrammar();

        $this->processor =  $connection->getPostProcessor();

    }

   /**
    * Get the database connection instance.
    *
    * @return object Xcholars\Database\ConnectionInterface
    */
    public function getConnection()
    {
        return $this->connection;
    }

   /**
    * Set the columns to be selected.
    *
    * @param  array|mixed  $columns
    * @return $this
    */
    public function select($columns = ['*'])
    {
        $this->bindings['select'] = [];

        $this->columns = is_array($columns) ? $columns : func_get_args();

        return $this;
    }

   /**
    * Set the table which the query is targeting.
    *
    * @param object|string Closure|Xcholars\Database\Query\Builder|$table
    * @return $this
    */
    public function from($table)
    {
        $this->from = $table;

        return $this;
    }

   /**
    * Execute the query as a "select" statement.
    *
    * @param array|string  $columns
    */
    public function get($columns = ['*'])
    {  
        return $this->onceWithColumns($columns, function ()
        {
            return $this->runSelect();
        });
    }


   /**
    * Execute the given callback while selecting the given columns.
    *
    * After running the callback, the columns are reset to the original value.
    *
    * @param array $columns
    * @param callable $callback
    * @return mixed
    */
    protected function onceWithColumns($columns, callable $callback)
    {
        $original = $this->columns;

        if (is_null($original))
        {
            $this->columns = $columns;
        }

        $result = $callback();

        $this->columns = $original;

        return $result;
    }


   /**
    * Run the query as a "select" statement against the connection.
    *
    * @return array
    */
    private function runSelect()
    {
        return $this->connection->select($this->toSql(), $this->getBindings());
    }

   /**
    * Get the SQL representation of the query.
    *
    * @return string
    */
    public function toSql()
    {
        return $this->grammar->compileSelect($this);
    }

   /**
    * Insert a new record into the database.
    *
    * @param  array  $values
    * @return bool
    */
    public function insert(array $values)
    {
        if (empty($values))
        {
            return true;
        }

        if (!is_array(reset($values)))
        {
            $values = [$values];
        }
        else
        {
            foreach ($values as $key => $value)
            {
                ksort($value);

                $values[$key] = $value;
            }
        }

        return $this->connection->insert(
          $this->grammar->compileInsert($this, $values),
          $this->cleanBindings(array_flatten($values))
        );
    }

   /**
    * Insert a new record and get the value of the primary key.
    *
    * @param  array  $values
    * @param  string|null  $sequence
    * @return int
    */
    public function insertGetId(array $values, $sequence = null)
    {
        $sql = $this->grammar->compileInsert($this, $values);

        $values = $this->cleanBindings($values);

        return $this->processor->processInsertGetId($this, $sql, $values, $sequence);
    }

   /**
    * Update a record in the database.
    *
    * @param  array  $values
    * @return int
    */
    public function update(array $values)
    {
        $sql = $this->grammar->compileUpdate($this, $values);

        return $this->connection->update($sql, $this->cleanBindings(
            $this->grammar->prepareBindingsForUpdate($this->bindings, $values)
        ));
    }

   /**
    * Delete a record from the database.
    *
    * @param  mixed  $id
    * @return int
    */
    public function delete($id = null)
    {
        if (!is_null($id))
        {
            $this->where($this->from . '.id', '=', $id);
        }

        return $this->connection->delete(
            $this->grammar->compileDelete($this), $this->cleanBindings(
                $this->grammar->prepareBindingsForDelete($this->bindings)
            )
        );
    }

    /**
    * Get the current query value bindings in a flattened array.
    *
    * @return array
    */
    public function getBindings()
    {
        return array_flatten($this->bindings);
    }

    /*
    * Get the raw array of bindings.
    *
    * @return array
    */
    public function getRawBindings()
    {
        return $this->bindings;
    }

   /**
    * Add a basic where clause to the query.
    *
    * @param object|string|array Closure| $column
    * @param mixed $operator
    * @param mixed $value
    * @param string $boolean
    * @return $this
    */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (is_array($column))
        {
            return $this->addArrayOfWheres($column, $boolean);
        }

        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        if ($column instanceof Closure && is_null($operator))
        {
            return $this->whereNested($column, $boolean);
        }

        if ($this->isQueryable($column) && ! is_null($operator))
        {
            [$sub, $bindings] = $this->createSub($column);

            return $this->addBinding($bindings, 'where')
            ->where(
                new Expression('('.$sub.')'), $operator, $value, $boolean
             );
        }

        if ($this->invalidOperator($operator))
        {
            [$value, $operator] = [$operator, '='];
        }

        if ($value instanceof Closure)
        {
            return $this->whereSub($column, $operator, $value, $boolean);
        }

        if (is_null($value))
        {
            return $this->whereNull($column, $boolean, $operator !== '=');
        }

        $type = 'Basic';

        $this->wheres[] = compact(
                  'type', 'column', 'operator', 'value', 'boolean'
               );

        if (! $value instanceof Expression)
        {
            $this->addBinding($value, 'where');
        }

        return $this;
    }

   /**
    * Add an "or where" clause to the query.
    *
    * @param object \Closure|string|array  $column
    * @param  mixed  $operator
    * @param  mixed  $value
    * @return $this
    */
    public function orWhere($column, $operator = null, $value = null)
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->where($column, $operator, $value, 'or');
    }

   /**
    * Add a where between statement to the query.
    *
    * @param  string  $column
    * @param  array  $values
    * @param  string  $boolean
    * @param  bool  $not
    * @return $this
    */
    public function whereBetween($column, array $values, $boolean = 'and', $not = false)
    {
        $type = 'between';

        $this->wheres[] = compact('type', 'column', 'values', 'boolean', 'not');

        $this->addBinding($this->cleanBindings($values), 'where');

        return $this;
    }

   /**
    * Add an or where between statement to the query.
    *
    * @param  string  $column
    * @param  array  $values
    * @return $this
    */
    public function orWhereBetween($column, array $values)
    {
        return $this->whereBetween($column, $values, 'or');
    }

   /**
    * Add a where not between statement to the query.
    *
    * @param  string  $column
    * @param  array  $values
    * @param  string  $boolean
    * @return $this
    */
    public function whereNotBetween($column, array $values, $boolean = 'and')
    {
        return $this->whereBetween($column, $values, $boolean, true);
    }

   /**
    * Add an or where not between statement to the query.
    *
    * @param  string  $column
    * @param  array  $values
    * @return $this
    */
    public function orWhereNotBetween($column, array $values)
    {
        return $this->whereNotBetween($column, $values, 'or');
    }

   /**
    * Add a "where in" clause to the query.
    *
    * @param  string  $column
    * @param  mixed  $values
    * @param  string  $boolean
    * @param  bool  $not
    * @return $this
    */
    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotIn' : 'In';

        if ($this->isQueryable($values))
        {
            [$query, $bindings] = $this->createSub($values);

            $values = [new Expression($query)];

            $this->addBinding($bindings, 'where');
        }

       if ($values instanceof Arrayable)
       {
            $values = $values->toArray();
       }

       $this->wheres[] = compact('type', 'column', 'values', 'boolean');

       $this->addBinding($this->cleanBindings($values), 'where');

       return $this;
    }

   /**
    * Add an "or where in" clause to the query.
    *
    * @param  string  $column
    * @param  mixed  $values
    * @return $this
    */
    public function orWhereIn($column, $values)
    {
        return $this->whereIn($column, $values, 'or');
    }

    /**
    * Add an "or where" clause comparing two columns to the query.
    *
    * @param  string|array  $first
    * @param  string|null  $operator
    * @param  string|null  $second
    * @return $this
    */
    public function orWhereColumn($first, $operator = null, $second = null)
    {
        return $this->whereColumn($first, $operator, $second, 'or');
    }

   /**
    * Add a "where not in" clause to the query.
    *
    * @param  string  $column
    * @param  mixed  $values
    * @param  string  $boolean
    * @return $this
    */
    public function whereNotIn($column, $values, $boolean = 'and')
    {
        return $this->whereIn($column, $values, $boolean, true);
    }

   /**
    * Add an "or where not in" clause to the query.
    *
    * @param  string  $column
    * @param  mixed  $values
    * @return $this
    */
    public function orWhereNotIn($column, $values)
    {
        return $this->whereNotIn($column, $values, 'or');
    }

   /**
    * Add a "where" clause comparing two columns to the query.
    *
    * @param  string|array  $first
    * @param  string|null  $operator
    * @param  string|null  $second
    * @param  string|null  $boolean
    * @return $this
    */
    public function whereColumn($first, $operator = null, $second = null, $boolean = 'and')
    {
        if (is_array($first))
        {
            return $this->addArrayOfWheres($first, $boolean, 'whereColumn');
        }

        if ($this->invalidOperator($operator))
        {
            [$second, $operator] = [$operator, '='];
        }

        $type = 'Column';

        $this->wheres[] = compact(
            'type', 'first', 'operator', 'second', 'boolean'
        );

        return $this;
    }

   /**
    * Add a nested where statement to the query.
    *
    * @param object Closure  $callback
    * @param  string  $boolean
    * @return $this
    */
    public function whereNested(Closure $callback, $boolean = 'and')
    {
        call_user_func($callback, $query = $this->forNestedWhere());

        return $this->addNestedWhereQuery($query, $boolean);
    }

   /**
    * Add a full sub-select to the query.
    *
    * @param  string  $column
    * @param  string  $operator
    * @param object Closure  $callback
    * @param  string  $boolean
    * @return $this
    */
    private function whereSub($column, $operator, Closure $callback, $boolean)
    {
        $type = 'Sub';

        call_user_func($callback, $query = $this->newQuery());

        $this->wheres[] = compact(
            'type', 'column', 'operator', 'query', 'boolean'
        );

        $this->addBinding($query->getBindings(), 'where');

        return $this;
    }

   /**
    * Add a "where null" clause to the query.
    *
    * @param string|array  $columns
    * @param string  $boolean
    * @param bool  $not
    * @return $this
    */
    public function whereNull($columns, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotNull' : 'Null';

        foreach (is_array($columns) ? $columns : (array) $columns as $column)
        {
            $this->wheres[] = compact('type', 'column', 'boolean');
        }

        return $this;
    }

   /**
    * Add a "where not null" clause to the query.
    *
    * @param  string|array  $columns
    * @param  string  $boolean
    * @return $this
    */
    public function whereNotNull($columns, $boolean = 'and')
    {
        return $this->whereNull($columns, $boolean, true);
    }

   /**
    * Add an "order by" clause to the query.
    *
    * @param object|string Closure|Xcholars\Database\Query\{Builder|Expression}  $column
    * @param  string  $direction
    * @return $this
    *
    * @throws object \InvalidArgumentException
    */
    public function orderBy($column, $direction = 'asc')
    {
        if ($this->isQueryable($column))
        {
            [$query, $bindings] = $this->createSub($column);

            $column = new Expression('('.$query.')');

            $this->addBinding($bindings, 'order');
        }

        $direction = strtolower($direction);

        if (!in_array($direction, ['asc', 'desc'], true))
        {
            throw new InvalidArgumentException(
                'Order direction must be "asc" or "desc".'
            );
        }

        $this->orders[] = ['column' => $column, 'direction' => $direction];

        return $this;
    }


   /**
    * Alias to set the "offset" value of the query.
    *
    * @param  int  $value
    * @return $this
    */
    public function skip($value)
    {
        return $this->offset($value);
    }

   /**
    * Set the "offset" value of the query.
    *
    * @param  int  $value
    * @return $this
    */
    private function offset($value)
    {
        $this->offset = max(0, $value);

        return $this;
    }

   /**
    * Alias to set the "limit" value of the query.
    *
    * @param int $value
    * @return $this
    */
    public function take($value)
    {
        return $this->limit($value);
    }

   /**
    * Set the "limit" value of the query.
    *
    * @param int $value
    * @return $this
    */
    public function limit($value)
    {
        if ($value >= 0)
        {
            $this->limit = $value;
        }

        return $this;
    }

   /**
    * Add an exists clause to the query.
    *
    * @param object Xcholars\Database\Query\Builder  $query
    * @param  string  $boolean
    * @param  bool  $not
    * @return $this
    */
    public function addWhereExistsQuery(self $query, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotExists' : 'Exists';

        $this->wheres[] = compact('type', 'query', 'boolean');

        $this->addBinding($query->getBindings(), 'where');

        return $this;
    }

   /**
    * Remove all of the expressions from a list of bindings.
    *
    * @param  array  $bindings
    * @return array
    */
    private function cleanBindings(array $bindings)
    {
        return array_values(array_filter($bindings, function ($binding)
        {
            return ! $binding instanceof Expression;
        }));
    }

    /**
    * Prepare the value and operator for a where clause.
    *
    * @param  string  $value
    * @param  string  $operator
    * @param  bool  $useDefault
    * @return array
    *
    * @throws object \InvalidArgumentException
    */
    public function prepareValueAndOperator($value, $operator, $useDefault = false)
    {
        if ($useDefault)
        {
            return [$operator, '='];
        }
        elseif ($this->invalidOperatorAndValue($operator, $value))
        {
            throw new InvalidArgumentException(
                "Illegal operator [{$operator}] and value [null] combination."
            );
        }

        return [$value, $operator];
    }

   /**
    * Determine if the value is a query builder instance or a Closure.
    *
    * @param mixed $value
    * @return bool
    */
    private function isQueryable($value)
    {
        return $value instanceof self ||
               $value instanceof OrmBuilder ||
               $value instanceof Relation ||
               $value instanceof Closure;
    }

   /**
    * Creates a subquery and parse it.
    *
    * @param object|string Closure|XcholarsDatabase\Query\Builder $query
    * @return array
    */
    private function createSub($query)
    {
        if ($query instanceof Closure)
        {
            $callback = $query;

            $callback($query = $this->newQuery());
        }

        return $this->parseSub($query);
   }

   /**
    * Parse the subquery into SQL and bindings.
    *
    * @param  mixed  $query
    * @return array
    *
    * @throws object \InvalidArgumentException
    */
    private function parseSub($query)
    {
        if ($this->isQueryable($query))
        {
            return [$query->toSql(), $query->getBindings()];
        }
        // elseif (is_string($query))
        // {
        //     return [$query, []];
        // }
        // else
        // {
        //     throw new InvalidArgumentException(
        //         'A subquery must be a query builder instance, a Closure, or a string.'
        //     );
        // }

        return false;
    }


   /**
    * Determine if the given operator and value combination is legal.
    *
    * Prevents using Null values with invalid operators.
    *
    * @param  string  $operator
    * @param  mixed  $value
    * @return bool
    */
    private function invalidOperatorAndValue($operator, $value)
    {
        return is_null($value) && in_array($operator, $this->operators)
               && ! in_array($operator, ['=']);
    }

   /**
    * Determine if the given operator is supported.
    *
    * @param  string  $operator
    * @return bool
    */
    private function invalidOperator($operator)
    {
        return ! in_array(strtolower($operator), $this->operators, true);
    }

   /**
    * Add an array of where clauses to the query.
    *
    * @param  array  $column
    * @param  string  $boolean
    * @param  string  $method
    * @return $this
    */
    private function addArrayOfWheres($column, $boolean, $method = 'where')
    {
        return $this->whereNested(
            function (Builder $query) use ($column, $method, $boolean)
            {
                foreach ($column as $key => $value)
                {
                    if (is_numeric($key) && is_array($value))
                    {
                        $query->{$method}(...array_values($value));
                    }
                    else
                    {
                        $query->$method($key, '=', $value, $boolean);
                    }
                }

            }, $boolean);
    }

   /**
    * Create a new query instance for nested where condition.
    *
    * @return object Xcholars\Database\Query\Builder
    */
    public function forNestedWhere()
    {
        return $this->newQuery()->from($this->from);
    }

   /**
    * Get a new instance of the query builder.
    *
    * @return object Xcholars\Database\Query\Builder
    */
    public function newQuery()
    {
        return new static($this->connection, $this->grammar, $this->processor);
    }

   /**
    * Add another query builder as a nested where to the query builder.
    *
    * @param object Xcholars\Database\Query\Builder  $query
    * @param  string  $boolean
    * @return $this
    */
    public function addNestedWhereQuery($query, $boolean = 'and')
    {
        if (count($query->wheres))
        {
            $type = 'Nested';

            $this->wheres[] = compact('type', 'query', 'boolean');

            $this->addBinding($query->getRawBindings()['where'], 'where');
        }

        return $this;
    }

   /**
    * Add a binding to the query.
    *
    * @param  mixed  $value
    * @param  string  $type
    * @return $this
    *
    * @throws object \InvalidArgumentException
    */
    public function addBinding($value, $type = 'where')
    {
        if (! array_key_exists($type, $this->bindings))
        {
            throw new InvalidArgumentException("Invalid binding type: {$type}.");
        }

        if (is_array($value))
        {
            $this->bindings[$type] = array_values(
                array_merge($this->bindings[$type], $value)
            );
        }
        else
        {
            $this->bindings[$type][] = $value;
        }

        return $this;
    }

   /**
    * Dump the current SQL and bindings.
    *
    * @return $this
    */
    public function dump()
    {
        dump($this->toSql(), $this->getBindings());

        return $this;
    }

   /**
    * Die and dump the current SQL and bindings.
    *
    * @return void
    */
    public function dd()
    {
        dd($this->toSql(), $this->getBindings());
    }

}
