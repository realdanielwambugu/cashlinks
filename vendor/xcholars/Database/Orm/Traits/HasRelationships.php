<?php

namespace Xcholars\Database\Orm\Traits;

use Xcholars\Database\Orm\Builder;

use Xcholars\Database\Orm\Model;

use Xcholars\Database\Orm\Relations\HasOne;

use Xcholars\Database\Orm\Relations\BelongsTo;

use Xcholars\Database\Orm\Relations\HasMany;

use Xcholars\Support\Proxies\Str;

trait HasRelationships
{
   /**
    * The loaded relationships for the model.
    *
    * @var array
    */
    protected $relations = [];

   /**
    * Define a one-to-one relationship.
    *
    * @param  string  $related
    * @param  string|null  $foreignKey
    * @param  string|null  $localKey
    * @return object Xcholars\Database\Orm\Relations\HasOne
    */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $foreignKey = $instance->getTable() . '.' . mb_strtolower($foreignKey);

        $localKey = $localKey ?: $this->getKeyName();

        return $this->newHasOne(
            $instance->newQuery(), $this, $foreignKey, $localKey
        );
    }

   /**
    * Instantiate a new HasOne relationship.
    *
    * @param object Xcholars\Database\Orm\Builder  $query
    * @param object Xcholars\Database\Orm\Model  $parent
    * @param string  $foreignKey
    * @param string  $localKey
    * @return object Xcholars\Database\Orm\Relations\HasOne
    */
    protected function newHasOne(Builder $query, Model $parent, $foreignKey, $localKey)
    {
        return new HasOne($query, $parent, $foreignKey, $localKey);
    }

   /**
    * Define an inverse one-to-one or many relationship.
    *
    * @param  string  $related
    * @param  string|null  $foreignKey
    * @param  string|null  $ownerKey
    * @param  string|null  $relation
    * @return object Xcholars\Database\Orm\Relations\BelongsTo
    */
    public function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey =  mb_strtolower($foreignKey ?: $instance->getForeignKey());

        $ownerKey = $ownerKey ?: $instance->getKeyName();

        $relation =  mb_strtolower(Str::singular($instance->getTable()));

        return $this->newBelongsTo(
            $instance->newQuery(), $this, $foreignKey, $ownerKey, $relation
        );

    }

    /**
    * Instantiate a new BelongsTo relationship.
    *
    * @param object Xcholars\Database\Orm\Builder  $query
    * @param object Xcholars\Database\Orm\Model  $child
    * @param  string  $foreignKey
    * @param  string  $ownerKey
    * @param  string  $relation
    * @returnobject Xcholars\Database\Orm\Relations\BelongsTo
    */
    protected function newBelongsTo(Builder $query, Model $child, $foreignKey, $ownerKey, $relation)
    {
        return new BelongsTo($query, $child, $foreignKey, $ownerKey, $relation);
    }

   /**
    * Define a one-to-many relationship.
    *
    * @param string|null $foreignKey
    * @param string|null $localKey
    * @param string $related
    * @return object Xcholars\Database\Eloquent\Relations\HasMany
    */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $foreignKey = mb_strtolower($instance->getTable() . '.' . $foreignKey);

        $localKey = $localKey ?: $this->getKeyName();

        return $this->newHasMany(
            $instance->newQuery(), $this, $foreignKey, $localKey
        );
    }

   /**
    * Instantiate a new HasMany relationship.
    *
    * @param object Xcholars\Database\Orm\Builder  $query
    * @param object Xcholars\Database\Orm\Model  $parent
    * @param string  $foreignKey
    * @param string  $localKey
    * @returnobject Xcholars\Database\Orm\Relations\HasMany
    */
    protected function newHasMany(Builder $query, Model $parent, $foreignKey, $localKey)
    {
        return new HasMany($query, $parent, $foreignKey, $localKey);
    }

   /**
    * Create a new model instance for a related model.
    *
    * @param  string  $class
    * @return mixed
    */
    protected function newRelatedInstance($class)
    {
        $instance = new $class;

        if (!$instance->getConnectionName())
        {
            $instance->setConnection($this->connection);
        }

        return $instance;
    }

   /**
    * Determine if the given relation is loaded.
    *
    * @param  string  $key
    * @return bool
    */
    public function relationLoaded($key)
    {
        return array_key_exists($key, $this->relations);
    }

   /**
    * Set the given relationship on the model.
    *
    * @param  string  $relation
    * @param  mixed  $value
    * @return $this
    */
    public function setRelation($relation, $value)
    {
        $this->relations[$relation] = $value;

        return $this;
    }
}
