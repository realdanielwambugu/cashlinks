<?php

Namespace Xcholars\Database\Query\Processors;

use Xcholars\Database\Query\Builder;

class Processor
{
   /**
    * Process an  "insert get ID" query.
    *
    * @param object Xcholars\Database\Query\Builder  $query
    * @param  string  $sql
    * @param  array  $values
    * @param  string|null  $sequence
    * @return int
    */
    public function processInsertGetId(Builder $query, $sql, $values, $sequence = null)
    {
        $query->getConnection()->insert($sql, $values);

        $id = $query->getConnection()->getPdo()->lastInsertId($sequence);

        return is_numeric($id) ? (int) $id : $id;
    }

}
