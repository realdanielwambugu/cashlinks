<?php

Namespace Xcholars\Database\Schema;

class ForeignIdColumnDefinition extends ColumnDefinition
{
   /**
    * The schema builder blueprint instance.
    *
    * @var object Xcholars\Database\Schema\Blueprint
    */
    private $blueprint;

   /**
    * Create a new foreign ID column definition.
    *
    * @param object Xcholars\Database\Schema\Blueprint $blueprint
    * @param array $attributes
    * @return void
    */
    public function __construct(Blueprint $blueprint, array $attributes = [])
    {
        parent::__construct($attributes);

        $this->blueprint = $blueprint;
    }
}
