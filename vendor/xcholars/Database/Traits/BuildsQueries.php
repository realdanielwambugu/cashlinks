<?php

namespace Xcholars\Database\Traits;

trait BuildsQueries
{
   /**
    * Execute the query and get the first result.
    *
    * @param  array|string  $columns
    * @return object|null |static Xcholars\Database\Eloquent\Model
    */
    public function first($columns = ['*'])
    {
        return count($data = $this->take(1)->get($columns)) ? $data[0] : false;
    }

}
