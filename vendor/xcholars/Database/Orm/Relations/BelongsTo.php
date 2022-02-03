<?php

Namespace Xcholars\Database\Orm\Relations;

use Xcholars\Database\Orm\Builder;

use Xcholars\Database\Orm\Model;

class BelongsTo extends Relation 
{
   /**
    * The child model instance of the relation.
    *
    * @var object Xcholars\Database\Orm\Model
    */
    protected $child;

   /**
    * The foreign key of the parent model.
    *
    * @var string
    */
    protected $foreignKey;

   /**
    * The associated key on the parent model.
    *
    * @var string
    */
    protected $ownerKey;

   /**
    * The name of the relationship.
    *
    * @var string
    */
    protected $relationName;

   /**
    * Create a new belongs to relationship instance.
    *
    * @param  object Xcholars\Database\Orm\Builder  $query
    * @param  object Xcholars\Database\Orm\Model  $child
    * @param  string  $foreignKey
    * @param  string  $ownerKey
    * @param  string  $relationName
    *
    * @return void
    */
    public function __construct(Builder $query, Model $child, $foreignKey, $ownerKey, $relationName)
    {
        $this->ownerKey = $ownerKey;

        $this->relationName = $relationName;

        $this->foreignKey = $foreignKey;

        $this->child = $child;

        parent::__construct($query, $child);
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        if (static::$constraints)
        {
            $ownerKey = $this->related->getTable() . '.' .  $this->ownerKey;

            $this->query->where(
                $ownerKey, '=', $this->child->{$this->foreignKey}
            );
        }
    }

   /**
    * Set the constraints for an eager load of the relation.
    *
    * @param  array  $models
    * @return void
    */
    public function addEagerConstraints(array $models)
    {
        $key = $this->related->getTable() . '.' . $this->ownerKey;

        $this->query->whereIn($key, $this->getEagerModelKeys($models));
    }

   /**
    * Gather the keys from an array of related models.
    *
    * @param  array  $models
    * @return array
    */
    protected function getEagerModelKeys(array $models)
    {
        $keys = [];

        foreach ($models as $model)
        {
            if (!is_null($value = $model->{$this->foreignKey}))
            {
                $keys[] = $value;
            }
        }

        sort($keys);

        return array_values(array_unique($keys));
    }

   /**
    * Initialize the relation on a set of models.
    *
    * @param  array  $models
    * @param  string  $relation
    * @return array
    */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model)
        {
            $model->setRelation($relation, $model);
        }

        return $models;
    }


   /**
    * Match the eagerly loaded results to their parents.
    *
    * @param  array  $models
    * @param  array $results
    * @param  string  $relation
    * @return array
    */
    public function match(array $models, $results, $relation)
    {
        $foreign = $this->foreignKey;

        $owner = $this->ownerKey;

        $dictionary = [];

        foreach ($results as $result)
        {
            $dictionary[$result->getAttribute($owner)] = $result;
        }

        foreach ($models as $model)
        {
            if (isset($dictionary[$model->{$foreign}]))
            {
                $model->setRelation($relation, $dictionary[$model->{$foreign}]);
            }
        }

       return $models;
    }

   /**
    * Get the results of the relationship.
    *
    * @return mixed
    */
    public function getResults()
    {
        return $this->query->first();
    }
}
