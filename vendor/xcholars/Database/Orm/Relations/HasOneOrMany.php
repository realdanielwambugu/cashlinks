<?php

Namespace Xcholars\Database\Orm\Relations;

use Xcholars\Database\Orm\Builder;

use Xcholars\Database\Orm\Model;

abstract class HasOneOrMany extends Relation
{
   /**
    * The foreign key of the parent model.
    *
    * @var string
    */
    protected $foreignKey;

   /**
    * The local key of the parent model.
    *
    * @var string
    */
    protected $localKey;

   /**
    * The count of self joins.
    *
    * @var int
    */
    protected static $selfJoinCount = 0;

   /**
    * Create a new has one or many relationship instance.
    *
    * @param  object Xcholars\Database\Orm\Builder  $query
    * @param  object Xcholars\Database\Orm\Model  $parent
    * @param  string  $foreignKey
    * @param  string  $localKey
    * @return void
    */
    public function __construct(Builder $query, Model $parent, $foreignKey, $localKey)
    {
        $this->localKey = $localKey;

        $this->foreignKey = $foreignKey;

        parent::__construct($query, $parent);
    }

   /**
    * Get the foreign key for the relationship.
    *
    * @return string
    */
    public function getQualifiedForeignKeyName()
    {
        return $this->foreignKey;
    }

   /**
    * Get the plain foreign key.
    *
    * @return string
    */
    public function getForeignKeyName()
    {
        $segments = explode('.', $this->getQualifiedForeignKeyName());

        return end($segments);
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
            $this->query->where($this->foreignKey, '=', $this->getParentKey());

            $this->query->whereNotNull($this->foreignKey);
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
        $this->query->whereIn(
            $this->foreignKey, $this->getKeys($models, $this->localKey)
        );
    }

   /**
    * Get the key value of the parent's local key.
    *
    * @return mixed
    */
    public function getParentKey()
    {
        return $this->parent->getAttribute($this->localKey);
    }

   /**
    * Match the eagerly loaded results to their many parents.
    *
    * @param array  $models
    * @param array $results
    * @param string  $relation
    * @return array
    */
    public function matchMany(array $models, array $results, $relation)
    {
        return $this->matchOneOrMany($models, $results, $relation, 'many');
    }


   /**
    * Match the eagerly loaded results to their single parents.
    *
    * @param  array  $models
    * @param array $results
    * @param  string  $relation
    * @return array
    */
    public function matchOne(array $models, array $results, $relation)
    {
        return $this->matchOneOrMany($models, $results, $relation, 'one');
    }

   /**
    * Match the eagerly loaded results to their many parents.
    *
    * @param  array  $models
    * @param array $results
    * @param  string  $relation
    * @param  string  $type
    * @return array
    */
    protected function matchOneOrMany(array $models, array $results, $relation, $type)
    {
        $dictionary = $this->buildDictionary($results);

        foreach ($models as $model)
        {
            $key = $model->getAttribute($this->localKey);

            if (isset($dictionary[$key]))
            {
                $model->setRelation(
                    $relation, $this->getRelationValue($dictionary, $key, $type)
                );
            }
        }

        return $models;
    }

    /**
    * Build model dictionary keyed by the relation's foreign key.
    *
    * @param array $results
    * @return array
    */
    protected function buildDictionary(array $results)
    {
        $foreign = $this->getForeignKeyName();

        $dictionary = [];

        foreach ($results as $result)
        {
            $dictionary[$result->{$foreign}] = $result;
        }

        return $dictionary;
    }

   /**
    * Get the value of a relationship by one or many type.
    *
    * @param  array  $dictionary
    * @param  string  $key
    * @param  string  $type
    * @return mixed
    */
    protected function getRelationValue(array $dictionary, $key, $type)
    {
        $value = $dictionary[$key];
         
        return $type === 'one' ? $value : [$value];
    }

}
