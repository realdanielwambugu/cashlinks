<?php

Namespace Xcholars\Auth;

use Xcholars\Database\Orm\Model;

class OrmUserProvider implements UserProviderContract
{
   /**
    * The Orm user model.
    *
    * @var string
    */
    private $model;

   /**
    * Hash instance
    *
    * @var object Xcholars\Auth\Hash;
    * @var string
    */
    private $hasher;

   /**
    * Create a new database user provider.
    *
    * @param  string  $model
    * @return void
    */
    public function __construct($model, Hash $hasher)
    {
        $this->model = $model;

        $this->hasher = $hasher;

    }

    /**
    * Retrieve a user by the given credentials.
    *
    * @param array  $credentials
    * @return
    */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials))
        {
            return;
        }

        if(count($credentials) === 1 && $this->hasPasswordOnly($credentials))
        {
            return;
        }

        $query = $this->newModelQuery();

        foreach ($credentials as $key => $value)
        {
            if ($key === 'password')
            {
                continue;
            }

            if (is_array($value))
            {
                $query->whereIn($key, $value);
            }
            else
            {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

   /**
    * Retrieve a user by their unique identifier.
    *
    * @param  mixed  $identifier
    * @return object Xcholars\Database\Orm\Model
    */
    public function retrieveById($identifier)
    {
        $model = $this->createModel();

        return $this->newModelQuery($model)
                    ->where($model->getKeyName(), $identifier)
                    ->first();
    }

   /**
    * Get a new query builder for the model instance.
    *
    * @param object Xcholars\Database\Orm\Model|null  $model
    * @return object Xcholars\Database\Orm\Builder
    */
    protected function newModelQuery($model = null)
    {
        return is_null($model)
                ? $this->createModel()->newQuery()
                : $model->newQuery();
    }

   /**
    * Create a new instance of the model.
    *
    * @param object Xcholars\Database\Model  $model
    */
    public function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class;
    }

    public function hasPasswordOnly($credentials)
    {
        foreach ($credentials as $key => $value)
        {
            $firstKey =  $key;
        }

        return $firstKey === 'password';
    }

    /**
    * Validate a user against the given credentials.
    *
    * @param object Xcholars\Database\Orm\Model $user
    * @param  array  $credentials
    * @return bool
    */
    public function validateCredentials(Model $user, array $credentials)
    {
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->password);
    }

}
