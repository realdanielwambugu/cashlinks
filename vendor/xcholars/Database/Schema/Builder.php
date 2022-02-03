<?php

Namespace Xcholars\Database\Schema;

use Xcholars\Database\Connections\Connection;

use Closure;

class Builder
{
   /**
    * The database connection instance.
    *
    * @var object Xcholars\Database\Connections\Connection
    */
    private $connection;

   /**
    * The schema grammar instance.
    *
    * @var object Xcholars\Database\Schema\Grammars\Grammar
    */
    private $grammar;

   /**
    * Create a new database Schema manager.
    *
    * @param  object Xcholars\Database\Connections\Connection $connection
    * @return void
    */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->grammar = $connection->getSchemaGrammar();

    }

   /**
    * Execute the blueprint to build / modify the table.
    *
    * @param object Xcholars\Database\Schema\Blueprint $blueprint
    * @return void
    */
    private function build(Blueprint $blueprint)
    {
        $blueprint->build($this->connection, $this->grammar);
    }

   /**
    * Create a new table on the schema.
    *
    * @param string $table
    * @param object \Closure $callback
    * @return void
    */
    public function create($table, Closure $callback)
    {
        $blueprint = $this->createBlueprint($table);
        
        $blueprint->create();

        $callback($blueprint);

        $this->build($blueprint);
    }

   /**
    * Modify a table on the schema.
    *
    * @param  string  $table
    * @param  object \Closure  $callback
    * @return void
    */
    public function table($table, Closure $callback)
    {
        $this->build($this->createBlueprint($table, $callback));
    }

   /**
    * Rename a table on the schema.
    *
    * @param  string  $from
    * @param  string  $to
    * @return void
    */
    public function rename($from, $to)
    {
        $blueprint = $this->createBlueprint($from);

        $blueprint->rename($to);

        $this->build($blueprint);
    }
   /**
    * Drop a table from the schema.
    *
    * @param  string  $table
    * @return void
    */
    public function drop($table)
    {
        $blueprint = $this->createBlueprint($table);

        $blueprint->drop();

        $this->build($blueprint);
    }

   /**
    * Create a new command set with a Closure.
    *
    * @param  string  $table
    * @param  object \Closure|null  $callback
    * @return object Xcholars\Database\Schema\Blueprint
    */
    private function createBlueprint($table, Closure $callback = null)
    {
        return new Blueprint($table, $callback);
    }


}
