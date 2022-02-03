<?php

Namespace Xcholars\Database\Orm;

use Xcholars\Database\Query\Builder as QueryBuilder;

use Closure;

use Xcholars\Support\Traits\ForwardsCalls;

use Xcholars\Database\Traits\BuildsQueries;

use Xcholars\Database\Orm\Relations\Relation;

use Xcholars\Database\Orm\Relations\RelationNotFoundException;

Use Xcholars\Support\Proxies\Str;

use BadMethodCallException;

class Builder
{
    use ForwardsCalls;

    use BuildsQueries;

   /**
    * The base query builder instance.
    *
    * @var object Xcholars\Database\Query\Builder
    */
    protected $query;

   /**
    * The model being queried.
    *
    * @var object Xcholars\Database\Orm\Model
    */
    protected $model;

   /**
    * Indicates if the model exists.
    *
    * @var bool
    */
    public $exists = false;

   /**
    * The relationships that should be eager loaded.
    *
    * @var array
    */
    protected $eagerLoad = [];

   /**
    * The methods that should be returned from query builder.
    *
    * @var array
    */
    protected $passthru = [
        'insert','insertGetId',  'getBindings', 'toSql', 'dump', 'dd',
        'count',  'getConnection',  'getGrammar',
    ];

   /**
    * Create a new Orm query builder instance.
    *
    * @param object Xcholars\Database\Query\Builder $query
    * @return void
    */
    public function __construct(QueryBuilder $query)
    {
        $this->query = $query;
    }

   /**
    * Set a model instance for the model being queried.
    *
    * @param object Xcholars\Database\Prm\Model $model
    * @return $this
    */
    public function setModel(Model $model)
    {
        $this->model = $model;

        $this->query->from($model->getTable());

        return $this;
    }

   /**
    * Execute the query as a "select" statement.
    *
    * @param  array|string  $columns
    * @return array
    */
    public function get($columns = ['*'])
    {
        if (count($models = $this->getModels($columns)) > 0)
        {
            $models = $this->eagerLoadRelations($models);
        }

        return $models;
    }

   /**
    * Eager load the relationships for the models.
    *
    * @param  array  $models
    * @return array
    */
    public function eagerLoadRelations(array $models)
    {
        foreach ($this->eagerLoad as $name => $constraints)
        {
            if (!Str::contains($name, '.'))
            {
                $models = $this->eagerLoadRelation($models, $name, $constraints);
            }
      }

       return $models;
    }

   /**
    * Eagerly load the relationship on a set of models.
    *
    * @param  array  $models
    * @param  string  $name
    * @param object \Closure  $constraints
    * @return array
    */
    protected function eagerLoadRelation(array $models, $name, Closure $constraints)
    {
        $relation = $this->getRelation($name);

        $relation->addEagerConstraints($models);

        $constraints($relation);

        $relation->initRelation($models, $name);

        return $relation->match(
            $relation->initRelation($models, $name),
            $relation->get(), $name
        );
    }

   /**
    * Get the relation instance for the given relation name.
    *
    * @param string  $name
    * @return object Xcholars\Database\Eloquent\Relations\Relation
    */
    public function getRelation($name)
    {
        $relation = Relation::noConstraints(function () use ($name)
        {
            try
            {
                return $this->getModel()->newInstance()->$name();
            }
            catch (BadMethodCallException $e)
            {
                throw RelationNotFoundException::make($this->getModel(), $name);
            }
       });

       $nested = $this->relationsNestedUnder($name);

       if (count($nested) > 0)
       {
           $relation->getQuery()->with($nested);
       }

       return $relation;
    }

   /**
    * Get the deeply nested relations for a given top-level relation.
    *
    * @param  string  $relation
    * @return array
    */
    protected function relationsNestedUnder($relation)
    {
        $nested = [];

        foreach ($this->eagerLoad as $name => $constraints)
        {
            if ($this->isNestedUnder($relation, $name))
            {
                $nested[substr($name, strlen($relation . '.'))] = $constraints;
            }
        }

        return $nested;

    }

    /**
    * Determine if the relationship is nested.
    *
    * @param  string  $relation
    * @param  string  $name
    * @return bool
    */
    protected function isNestedUnder($relation, $name)
    {
        return Str::contains($name, '.') && Str::startsWith($name, $relation.'.');
    }

   /**
    * Find a model by its primary key.
    *
    * @param  mixed  $id
    * @param  array  $columns
    * @return object|null Xcholars\Database\Eloquent\Model|[]
    */
    public function find($id, $columns = ['*'])
    {
        if (is_array($id))
        {
            return $this->findMany($id, $columns);
        }

        return $this->whereKey($id)->first($columns);
    }

   /**
    * Set the relationships that should be eager loaded.
    *
    * @param  mixed  $relations
    * @return $this
    */
    public function with($relations)
    {
        $eagerLoad = $this->parseWithRelations(
            is_string($relations) ? func_get_args() : $relations
        );

        $this->eagerLoad = array_merge($this->eagerLoad, $eagerLoad);

        return $this;
    }

    /**
    * Parse a list of relations into individuals.
    *
    * @param  array  $relations
    * @return array
    */
    protected function parseWithRelations(array $relations)
    {
        $results = [];

        foreach ($relations as $name => $constraints)
        {
            if (is_numeric($name))
            {
                $name = $constraints;

                [$name, $constraints] = Str::contains($name, ':')
                            ? $this->createSelectWithConstraint($name)
                            : [$name, static function ()
                            {
                                //code
                            }];
            }

            $results = $this->addNestedWiths($name, $results);

            $results[$name] = $constraints;
        }

        return $results;
    }

  /**
   * Parse the nested relationships in a relation.
   *
   * @param  string  $name
   * @return array
   */
    protected function addNestedWiths($name, $results)
    {
        $progress = [];

        foreach (explode('.', $name) as $segment)
        {
            $progress[] = $segment;

            $results[implode('.', $progress)] = static function ()
            {
                //code
            };
        }

        return $results;
    }

   /**
    * Create a constraint to select the given columns for the relation.
    *
    * @param  string  $name
    * @return array
    */
    protected function createSelectWithConstraint($name)
    {
        return [Str::SplitBefore(':', $name), static function ($query) use ($name)
        {
            $query->select(explode(',', Str::SplitAfter(':', $name)));
        }];
    }

   /**
    * Save a new model and return the instance.
    *
    * @param  array  $attributes
    * @return object Xcholars\Database\Orm\Model|$this
    */
    public function create(array $attributes = [])
    {
        $instance = $this->newModelInstance($attributes);

        $instance->save();

        return $instance;
    }

   /**
    * Update a record in the database.
    *
    * @param  array  $values
    * @return int
    */
    public function update(array $values)
    {
        return $this->query->update($this->addUpdatedAtColumn($values));
    }

   /**
    * Add the "updated at" column to an array of values.
    *
    * @param  array  $values
    * @return array
    */
    protected function addUpdatedAtColumn(array $values)
    {
        $column = $this->model->getUpdatedAtColumn();

        if (! $this->model->usesTimestamps() || is_null($column))
        {
            return $values;
        }

        $values = array_merge(
            [$column => $this->model->freshTimestampString()], $values
        );

        $qualifiedColumn = $this->query->from . '.' . $column;

        $values[$qualifiedColumn] = $values[$column];

        unset($values[$column]);

        return $values;
    }

   /**
    * Retrieve the "count" result of the query.
    *
    * @param  string  $columns
    * @return int
    */
    public function count($columns = ['*'])
    {
        return count($this->get($columns));
    }

   /**
    * Find multiple models by their primary keys.
    *
    * @param array  $ids
    * @param  array  $columns
    * @return array
    */
    public function findMany($ids, $columns = ['*'])
    {
        if (empty($ids))
        {
            return [];
        }

        return $this->whereKey($ids)->get($columns);
    }

    /**
    * Add a basic where clause to the query.
    *
    * @param object \Closure|string|array  $column
    * @param  mixed  $operator
    * @param  mixed  $value
    * @param  string  $boolean
    * @return $this
    */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if ($column instanceof Closure && is_null($operator))
        {
            $column($query = $this->model->newModelQuery());

            $this->query->addNestedWhereQuery($query->getQuery(), $boolean);
        }
        else
        {
            $this->query->where(...func_get_args());
        }

        return $this;
    }

   /**
    * Add a basic where clause to the query, and return the first result.
    *
    * @param object|string|array $column
    * @param  mixed  $operator
    * @param  mixed  $value
    * @param  string  $boolean
    * @return object Xcholars\Database\Eloquent\Model|static
    */
    public function firstWhere($column, $operator = null, $value = null, $boolean = 'and')
    {
        return $this->where($column, $operator, $value, $boolean)->first();
    }

   /**
    * Add a where clause on the primary key to the query.
    *
    * @param  mixed  $id
    * @return $this
    */
    public function whereKey($id)
    {
        if (is_array($id))
        {
            $this->query->whereIn($this->model->getQualifiedKeyName(), $id);

            return $this;
        }

        if ($this->model->getKeyType() === 'string')
        {
            $id = (string) $id;
        }

        return $this->where($this->model->getQualifiedKeyName(), '=', $id);
    }

   /**
    * Get the model instance being queried.
    *
    * @return object Xcholars\Database\Orm\Model[]|static[]
    */
    public function getModel()
    {
        return $this->model;
    }

   /**
    * Get the underlying query builder instance.
    *
    * @return object Xcholars\Database\Query\Builder
    */
    public function getQuery()
    {
        return $this->query;
    }

   /**
    * Get the hydrated models without eager loading.
    *
    * @param array|string  $columns
    * @return object Xcholars\Database\Orm\Model[]|static[]
    */
    public function getModels($columns = ['*'])
    {
        return $this->hydrate(
            $this->query->get($columns)
        );
    }

   /**
    * Create a collection of models from plain arrays.
    *
    * @param array $items
    * @return array
    */
    public function hydrate(array $items)
    {
        $instance = $this->newModelInstance();

        return array_map(function ($item) use ($instance)
        {
            return $instance->newFromBuilder($item);

        }, $items);
    }

   /**
    * Create a new instance of the model being queried.
    *
    * @param array $attributes
    * @return object Xcholars\Database\Orm\Model|static
    */
    public function newModelInstance($attributes = [])
    {
        return $this->model->newInstance($attributes)->setConnection(
            $this->query->getConnection()->getName()
        );
    }

   /**
    * Dynamically handle calls into the query instance.
    *
    * @param  string  $method
    * @param  array  $parameters
    * @return mixed
    */
    public function __call($method, $parameters)
    {
        if (in_array($method, $this->passthru))
        {
            return $this->query->{$method}(...$parameters);
        }

        $this->forwardCallTo($this->query, $method, $parameters);

        return $this;
    }


}
