<?php

Namespace Xcholars\Database\Orm;

use Xcholars\Database\ConnectionResolverContract as Resolver;

use Xcholars\Database\Query\Builder as QueryBuilder;

use Xcholars\Support\Proxies\Str;

use Xcholars\Support\Traits\ForwardsCalls;

use Exception;

abstract class Model
{
    use Traits\FillableAttributes;

    use Traits\HasAttributes;

    use Traits\HasTimestamps;

    use Traits\HasRelationships;

    use ForwardsCalls;

   /**
    * The connection name for the model.
    *
    * @var string|null
    */
    protected $connection;

   /**
    * The connection resolver instance.
    *
    * @param object Xcholars\Database\ConnectionResolverContract  $resolver
    */
    protected static $resolver;

   /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table;

   /**
    * The primary key for the model.
    *
    * @var string
    */
    protected $primaryKey = 'id';

   /**
    * The "type" of the primary key ID.
    *
    * @var string
    */
    protected $keyType = 'int';

   /**
    * Indicates if the IDs are auto-incrementing.
    *
    * @var bool
    */
    public $incrementing = true;

   /**
    * The name of the "created at" column.
    *
    * @var string
    */
    const CREATED_AT = 'created_at';

   /**
    * The name of the "updated at" column.
    *
    * @var string
    */
    const UPDATED_AT = 'updated_at';

   /**
    * Indicates if the model exists.
    *
    * @var bool
    */
    public $exists = false;

   /**
    * Indicates if the model was inserted during the current request lifecycle.
    *
    * @var bool
    */
    public $wasRecentlyCreated = false;

   /**
    * The relations to eager load on every query.
    *
    * @var array
    */
    protected $with = [];

   /**
    * Create a new Orm model instance.
    *
    * @param  array  $attributes
    * @return void
    */
    public function __construct(array $attributes = [])
    {
        $this->syncOriginal();

        $this->fill($attributes);
    }

   /**
    * Set the connection resolver instance.
    *
    * @param object Xcholars\Database\ConnectionResolverContract  $resolver
    * @return void
    */
    public static function setConnectionResolver(Resolver $resolver)
    {
        static::$resolver = $resolver;
    }

   /**
    * Get the table qualified key name.
    *
    * @return string
    */
    public function getQualifiedKeyName()
    {
        return $this->qualifyColumn($this->getKeyName());
    }

   /**
    * Get the primary key for the model.
    *
    * @return string
    */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

   /**
    * Get the auto-incrementing key type.
    *
    * @return string
    */
    public function getKeyType()
    {
        return $this->keyType;
    }

   /**
    * Get the default foreign key name for the model.
    *
    * @return string
    */
    public function getForeignKey()
    {
        return Str::singular($this->getTable()) . '_' . $this->getKeyName();
    }

   /**
    * Qualify the given column name by the model's table.
    *
    * @param  string  $column
    * @return string
    */
    public function qualifyColumn($column)
    {
        if (Str::contains($column, '.'))
        {
            return $column;
        }

        return $this->getTable() . ' . ' . $column;
    }

   /**
    * Fill the model with an array of attributes.
    *
    * @param  array  $attributes
    * @return $this
    *
    * @throws object Xcholars\Database\Orm\MassAssignmentException
    */
    public function fill(array $attributes)
    {
        foreach ($this->fillableFromArray($attributes) as $key => $value)
        {
            if ($this->isFillable($key))
            {
                $this->setAttribute($key, $value);
            }
            elseif(!$this->isTotallyFillable())
            {
                throw new MassAssignmentException(sprintf(
                    'Add [%s] to fillable property to allow mass assignment on [%s].',
                    $key, get_class($this)
                ));
            }

        }

        return $this;
    }

   /**
    * Get all of the models from the database.
    *
    * @param  array|mixed  $columns
    * @return array
    */
    public static function all($columns = ['*'])
    {
        return static::query()->get(
          is_array($columns) ? $columns : func_get_args()
        );
    }

   /**
    * Begin querying a model with eager loading.
    *
    * @param  array|string  $relations
    * @return object Xcholars\Database\Eloquent\Builder
    */
    public static function with($relations)
    {
        return static::query()->with(
            is_string($relations) ? func_get_args() : $relations
        );
    }

   /**
    * Save the model to the database.
    *
    * @param  array  $options
    * @return bool
    */
    public function save(array $options = [])
    {
        $query = $this->newModelQuery();

        if ($this->exists)
        {
            $saved = $this->isDirty() ? $this->performUpdate($query) : true;
        }
        else
        {
            $saved = $this->performInsert($query);

            $connection = $query->getConnection();

            if (!$this->getConnectionName() && $connection)
            {
                $this->setConnection($connection->getName());
            }
        }

        $this->syncOriginal();

        return $saved;
    }

   /**
    * Delete the model from the database.
    *
    * @return bool|null
    *
    * @throws object \Exception
    */
    public function delete()
    {
        if (is_null($this->getKeyName()))
        {
            throw new Exception('No primary key defined on model.');
        }

        if (! $this->exists)
        {
            return;
        }

        $this->performDeleteOnModel();

        return true;
    }

   /**
    * Perform the actual delete query on this model instance.
    *
    * @return void
    */
    protected function performDeleteOnModel()
    {
        $this->setKeysForSaveQuery($this->newModelQuery())->delete();

        $this->exists = false;
    }

   /**
    * Perform a model insert operation.
    *
    * @param object Xcholars\Database\Orm\Builder  $query
    * @return bool
    */
    protected function performInsert(Builder $query)
    {
        if ($this->usesTimestamps())
        {
            $this->updateTimestamps();
        }

        $attributes = $this->getAttributes();

        if ($this->getIncrementing())
        {
            $this->insertAndSetId($query, $attributes);
        }
        else
        {
            if (empty($attributes))
            {
                return true;
            }

            $query->insert($attributes);
        }

        $this->wasRecentlyCreated = true;

        return true;
    }

   /**
    * Insert the given attributes and set the ID on the model.
    *
    * @param object Xcholars\Database\Eloquent\Builder $query
    * @param  array  $attributes
    * @return void
    */
    protected function insertAndSetId(Builder $query, $attributes)
    {
        $id = $query->insertGetId($attributes, $keyName = $this->getKeyName());

        $this->setAttribute($keyName, $id);
    }

   /**
    * Update the model in the database.
    *
    * @param  array  $attributes
    * @param  array  $options
    * @return bool
    */
    public function update(array $attributes = [], array $options = [])
    {
        if (!$this->exists)
        {
            return false;
        }

        return $this->fill($attributes)->save($options);
    }

   /**
    * Destroy the models for the given IDs.
    *
    * @param array $ids
    * @return int
    */
    public static function destroy($ids)
    {
        $count = 0;

        $ids = is_array($ids) ? $ids : func_get_args();

        $key = ($instance = new static)->getKeyName();

        foreach ($instance->whereIn($key, $ids)->get() as $model)
        {
            if ($model->delete())
            {
                $count++;
            }
        }

        return $count;
    }

   /**
    * Save the model and all of its relationships.
    *
    * @return bool
    */
    public function push()
    {
        if (!$this->save())
        {
            return false;
        }

        foreach ($this->relations as $models)
        {
            $models = is_array($models) ? $models : [$models];

            foreach (array_filter($models) as $model)
            {
                if (!$model->push())
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
    * Perform a model update operation.
    *
    * @param object Xcholars\Database\Orm\Builder  $query
    * @return bool
    */
    protected function performUpdate(Builder $query)
    {
        if ($this->usesTimestamps())
        {
            $this->updateTimestamps();
        }

        $dirty = $this->getDirty();

        if (count($dirty) > 0)
        {
            $this->setKeysForSaveQuery($query)->update($dirty);

            $this->syncChanges();

        }

        return true;
    }

   /**
    * Set the keys for a save update query.
    *
    * @param object Xcholars\Database\Orm\Builder $query
    * @return object Xcholars\Database\Orm\Builder
    */
    protected function setKeysForSaveQuery(Builder $query)
    {
        $query->where($this->getKeyName(), '=', $this->getKeyForSaveQuery());

        return $query;
    }

   /**
    * Get the value of the model's primary key.
    *
    * @return mixed
    */
    public function getKey()
    {
        return $this->getAttribute($this->getKeyName());
    }

   /**
    * Get the primary key value for a save query.
    *
    * @return mixed
    */
    protected function getKeyForSaveQuery()
    {
        return $this->original[$this->getKeyName()]
                        ?? $this->getKey();
    }

   /**
    * Begin querying the model.
    *
    * @return object Xcholars\Database\Orm\Builder
    */
    public static function query()
    {
        return (new static)->newQuery();
    }

    /**
    * Get a new query builder for the model's table.
    *
    * @return object Xcholars\Database\Orm\Builder
    */
    public function newQuery()
    {
        return $this->newModelQuery()->with($this->with);
    }

   /**
    * Get a new query builder that doesn't have any global scopes or eager loading.
    *
    * @return object Xcholars\Database\Orm\Builder|static
    */
    public function newModelQuery()
    {
        return $this->newOrmBuilder(
            $this->newBaseQueryBuilder()
        )->setModel($this);;
    }

   /**
    * Create a new Orm query builder for the model.
    *
    * @param object Xcholars\Database\Query\Builder  $query
    * @return object Xcholars\Database\Orm\Builder|static
    */
    public function newOrmBuilder(QueryBuilder $query)
    {
        return new Builder($query);
    }

   /**
    * Get a new query builder instance for the connection.
    *
    * @return object Xcholars\Database\Orm\Builder
    */
    protected function newBaseQueryBuilder()
    {
        return $this->getConnection()->query();
    }

    /**
    * Get the database connection for the model.
    *
    * @return object Xcholars\Database\Connections\Connection
    */
    public function getConnection()
    {
        return static::resolveConnection($this->getConnectionName());
    }

   /**
    * Get the current connection name for the model.
    *
    * @return string|null
    */
    public function getConnectionName()
    {
        return $this->connection;
    }

   /**
    * Set the connection associated with the model.
    *
    * @param  string|null  $name
    * @return $this
    */
    public function setConnection($name)
    {
        $this->connection = $name;

        return $this;
    }

   /**
    * Get the value indicating whether the IDs are incrementing.
    *
    * @return bool
    */
    public function getIncrementing()
    {
        return $this->incrementing;
    }

   /**
    * Resolve a connection instance.
    *
    * @param  string|null  $connection
    * @return object Xcholars\Database\Connections\Connection
    */
    public static function resolveConnection($connection = null)
    {
        return static::$resolver->connection($connection);
    }

   /**
    * Set the table associated with the model.
    *
    * @param  string  $table
    * @return $this
    */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

   /**
    * Get the table associated with the model.
    *
    * @return string
    */
    public function getTable()
    {
        return $this->table ?? mb_strtolower(Str::plural(class_basename($this)));
    }

   /**
    * Create a new instance of the given model.
    *
    * @param array  $attributes
    * @param bool  $exists
    * @return object static
    */
    public function newInstance($attributes = [], $exists = false)
    {
        $model = new static((array) $attributes);

        $model->exists = $exists;

        $model->setConnection($this->getConnectionName());

        $model->setTable($this->getTable());

        return $model;
    }


    /**
    * Create a new model instance that is existing.
    *
    * @param  array  $attributes
    * @param  string|null  $connection
    * @return object static
    */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $model = $this->newInstance([], true);

        $model->setRawAttributes((array) $attributes, true);

        $model->setConnection($connection ?: $this->getConnectionName());

        return $model;
    }

   /**
    * Handle dynamic method calls into the model.
    *
    * @param  string  $method
    * @param  array  $parameters
    * @return mixed
    */
    public function __call($method, $parameters)
    {
        // if (in_array($method, ['increment', 'decrement']))
        // {
        //     return $this->$method(...$parameters);
        // }

        // if ($resolver = (static::$relationResolvers[get_class($this)][$method] ?? null))
        // {
        //     return $resolver($this);
        // }

        return $this->forwardCallTo($this->newQuery(), $method, $parameters);
    }

   /**
    * Handle dynamic static method calls into the model.
    *
    * @param  string  $method
    * @param  array  $parameters
    * @return mixed
    */
    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

   /**
    * Dynamically retrieve attributes on the model.
    *
    * @param  string  $key
    * @return mixed
    */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

   /**
    * Dynamically set attributes on the model.
    *
    * @param  string  $key
    * @param  mixed  $value
    * @return void
    */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }
}
