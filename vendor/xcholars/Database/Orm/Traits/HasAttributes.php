<?php

namespace Xcholars\Database\Orm\Traits;

use Xcholars\Support\Proxies\Date;

use Xcholars\Support\DateContract;

use DateTimeInterface;

use Xcholars\Database\Orm\Relations\Relation;

use LogicException;

trait HasAttributes
{
   /**
    * The model's attributes.
    *
    * @var array
    */
    protected $attributes = [];

   /**
    * The model attribute's original state.
    *
    * @var array
    */
    protected $original = [];

   /**
    * The changed model attributes.
    *
    * @var array
    */
    protected $changes = [];

   /**
    * Set a given attribute on the model.
    *
    * @param  string  $key
    * @param  mixed  $value
    * @return mixed
    */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

   /**
    * Get all of the current attributes on the model.
    *
    * @return array
    */
    public function getAttributes()
    {
        return $this->attributes;
    }

   /**
    * Set the array of model attributes. No checking is done.
    *
    * @param  array  $attributes
    * @param  bool  $sync
    * @return $this
    */
    public function setRawAttributes(array $attributes, $sync = false)
    {
        $this->attributes = $attributes;

        if ($sync)
        {
            $this->syncOriginal();
        }

        return $this;
    }

   /**
    * Sync the original attributes with the current.
    *
    * @return $this
    */
    public function syncOriginal()
    {
        $this->original = $this->getAttributes();

        return $this;
    }

   /**
    * Sync the changed attributes.
    *
    * @return $this
    */
    public function syncChanges()
    {
        $this->changes = $this->getDirty();

        return $this;
    }

   /**
    * Get an attribute from the model.
    *
    * @param  string  $key
    * @return mixed
    */
    public function getAttribute($key)
    {
        if (!$key)
        {
            return;
        }

        if (array_key_exists($key, $this->attributes))
        {
            return $this->getAttributeValue($key);
        }

        if (method_exists(self::class, $key))
        {
            return;
        }

        return $this->getRelationValue($key);
    }

   /**
    * Get a relationship.
    *
    * @param  string  $key
    * @return mixed
    */
    public function getRelationValue($key)
    {
        if ($this->relationLoaded($key))
        {
            return $this->relations[$key];
        }

        if (method_exists($this, $key))
        {
            return $this->getRelationshipFromMethod($key);
        }
    }

  /**
    * Get a relationship value from a method.
    *
    * @param  string  $method
    * @return mixed
    *
    * @throws \LogicException
    */
    protected function getRelationshipFromMethod($method)
    {
        $relation = call_user_func([$this, $method]);

        if (! $relation instanceof Relation)
        {
            if (is_null($relation))
            {
                throw new LogicException(sprintf(
                   '%s::%s must return a relationship instance,
                    but "null" was returned. Was the "return"
                    keyword used?', static::class, $method
                ));
            }

            throw new LogicException(sprintf(
                '%s::%s must return a relationship
                instance.', static::class, $method
            ));
        }

        $results = $relation->getResults();

        $this->setRelation($method, $results);

        return $results;
    }

   /**
    * Get an attribute from the $attributes array.
    *
    * @param  string  $key
    * @return mixed
    */
    protected function getAttributeValue($key)
    {
        return $this->getAttributes()[$key] ?? null;
    }

   /**
    * Determine if the model or any of the given attribute(s) have been modified.
    *
    * @param  array|string|null  $attributes
    * @return bool
    */
    public function isDirty($attributes = null)
    {
        return $this->hasChanges(
            $this->getDirty(), is_array($attributes) ? $attributes : func_get_args()
        );
    }


   /**
    * Get the attributes that have been changed since last sync.
    *
    * @return array
    */
    public function getDirty()
    {
        $dirty = [];

        foreach ($this->getAttributes() as $key => $value)
        {
            if (!$this->originalIsEquivalent($key))
            {
                $dirty[$key] = $value;
            }
        }

        return $dirty;
    }

   /**
    * Determine if the new and old values for a given key are equivalent.
    *
    * @param  string  $key
    * @return bool
    */
    public function originalIsEquivalent($key)
    {
        if (!array_key_exists($key, $this->original))
        {
            return false;
        }

        $attribute = $this->attributes[$key];

        $original = $this->original[$key];

        return $attribute === $original;
    }

   /**
    * Determine if any of the given attributes were changed.
    *
    * @param  array  $changes
    * @param  array|string|null  $attributes
    * @return bool
    */
    protected function hasChanges($changes, $attributes = null)
    {
        if (empty($attributes))
        {
            return count($changes) > 0;
        }

        $attributes = is_array($attributes) ? $attributes : [$attributes];

        foreach ($attributes as $attribute)
        {
            if (array_key_exists($attribute, $changes))
            {
                return true;
            }
        }

        return false;
    }

   /**
    * Get the format for database stored dates.
    *
    * @return string
    */
    public function getDateFormat()
    {
        return $this->dateFormat ?: $this->getConnection()->getQueryGrammar()->getDateFormat();
    }

   /**
    * Convert a DateTime to a storable string.
    *
    * @param  mixed  $value
    * @return string|null
    */
    public function fromDateTime($value)
    {
        return empty($value) ? $value : $this->asDateTime($value)->format(
            $this->getDateFormat()
        );
    }

   /**
    * Return a timestamp as unix timestamp.
    *
    * @param  mixed  $value
    * @return int
    */
    protected function asTimestamp($value)
    {
        return $this->asDateTime($value)->getTimestamp();
    }

   /**
    * Return a timestamp as DateTime object with time set to 00:00:00.
    *
    * @param  mixed  $value
    * @return object Xcholars\Support\CustomDate
    */
    protected function asDate($value)
    {
        return $this->asDateTime($value)->startOfDay();
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param  mixed  $value
     * @return object Xcholars\Support\CustomDate
     */
    protected function asDateTime($value)
    {
        if ($value instanceof DateContract)
        {
            return Date::instance($value);
        }

        if ($value instanceof DateTimeInterface)
        {
            return Date::parse(
                $value->format('Y-m-d H:i:s.u'), $value->getTimezone()
            );
        }

        if (is_numeric($value))
        {
            return Date::createFromTimestamp($value);
        }

        if ($this->isStandardDateFormat($value))
        {
            return Date::instance(Date::createFromFormat('Y-m-d', $value)->startOfDay());
        }

        $format = $this->getDateFormat();

        if (Date::hasFormat($value, $format))
        {
            return Date::createFromFormat($format, $value);
        }

        return Date::parse($value);
    }

   /**
    * Determine if the given value is a standard date format.
    *
    * @param  string  $value
    * @return bool
    */
    protected function isStandardDateFormat($value)
    {
        return preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value);
    }

}
