<?php

Namespace Xcholars\Database\Orm\Relations;

use Xcholars\Database\Orm\Builder;

use Xcholars\Database\Orm\Model;

use Xcholars\Support\Traits\ForwardsCalls;

use Closure;

abstract class Relation
{
    use ForwardsCalls;

   /**
    * The Eloquent query builder instance.
    *
    * @var object Xcholars\Database\Orm\Builder
    */
    protected $query;

   /**
    * The parent model instance.
    *
    * @var object Xcholars\Database\Orm\Model
    */
    protected $parent;

   /**
    * The related model instance.
    *
    * @var object Xcholars\Database\Orm\Model
    */
    protected $related;

   /**
    * Indicates if the relation is adding constraints.
    *
    * @var bool
    */
    protected static $constraints = true;

   /**
    * Create a new relation instance.
    *
    * @param object Xcholars\Database\Orm\Builder  $query
    * @param object Xcholars\Database\Orm\Model  $parent
    * @return void
    */
    public function __construct(Builder $query, Model $parent)
    {
        $this->query = $query;

        $this->parent = $parent;

        $this->related = $query->getModel();

        $this->addConstraints();
    }

   /**
    * Get the underlying query for the relation.
    *
    * @return object Xcholars\Database\Orm\Builder
    */
    public function getQuery()
    {
        return $this->query;
    }

   /**
    * Set the base constraints on the relation query.
    *
    * @return void
    */
    abstract public function addConstraints();

   /**
    * Initialize the relation on a set of models.
    *
    * @param  array  $models
    * @param  string  $relation
    * @return array
    */
    abstract public function initRelation(array $models, $relation);

   /**
    * Match the eagerly loaded results to their parents.
    *
    * @param  array  $models
    * @param array
    * @param  string  $relation
    * @return array
    */
    abstract public function match(array $models, array $results, $relation);

   /**
    * Run a callback with constraints disabled on the relation.
    *
    * @param object \Closure  $callback
    * @return mixed
    */
    public static function noConstraints(Closure $callback)
    {
        $previous = static::$constraints;

        static::$constraints = false;

        try
        {
            return $callback();
        }
        finally
        {
            static::$constraints = $previous;
        }
    }

   /**
    * Execute the query as a "select" statement.
    *
    * @param  array  $columns
    * @return array
    */
    public function get($columns = ['*'])
    {
        return $this->query->get($columns);
    }

   /**
    * Get all of the primary keys for an array of models.
    *
    * @param  array  $models
    * @param  string|null  $key
    * @return array
    */
    protected function getKeys(array $models, $key = null)
    {
        $keys = array_unique(array_values(array_map(function ($model) use ($key)
        {
            return $key ? $model->getAttribute($key) : $model->getKey();

        }, $models)));

        sort($keys);

        return $keys;
    }

   /**
    * Handle dynamic method calls to the relationship.
    *
    * @param  string  $method
    * @param  array  $parameters
    * @return mixed
    */
    public function __call($method, $parameters)
    {
        $result = $this->forwardCallTo($this->query, $method, $parameters);

        if ($result === $this->query)
        {
            return $this;
        }

        return $result;
    }
}
