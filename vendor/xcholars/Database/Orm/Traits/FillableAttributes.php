<?php

namespace Xcholars\Database\Orm\Traits;

trait FillableAttributes
{
   /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [];

   /**
    * Indicates if all mass assignment is enabled.
    *
    * @var bool
    */
    protected static $totallyFillable = false;

   /**
    * Get the fillable attributes for the model.
    *
    * @return array
    */
    public function getFillable()
    {
        return $this->fillable;
    }

   /**
   * Determine if the given attribute may be mass assigned.
   *
   * @param  string  $key
   * @return bool
   */
   public function isFillable($key)
   {
        if ($this->isTotallyFillable())
        {
            return true;
        }

        if (in_array($key, $this->getFillable()))
        {
            return true;
        }

        return false;
   }

   /**
    * Determine if the model is totally guarded.
    *
    * @return bool
    */
    public function isTotallyFillable()
    {
        return static::$totallyFillable;
    }

   /**
    * Get the fillable attributes of a given array.
    *
    * @param  array  $attributes
    * @return array
    */
    protected function fillableFromArray(array $attributes)
    {
        if (count($this->getFillable()) > 0 && ! static::$totallyFillable)
        {
            return array_intersect_key(
                $attributes, array_flip($this->getFillable())
            );
        }

        return $attributes;
    }
}
