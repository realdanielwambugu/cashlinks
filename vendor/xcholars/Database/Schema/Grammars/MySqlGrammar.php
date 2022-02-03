<?php

Namespace Xcholars\Database\Schema\Grammars;

use Xcholars\Database\Connections\Connection ;

use Xcholars\Support\Fluent;

use Xcholars\Database\Schema\Blueprint;

class MySqlGrammar extends Grammar
{
   /**
    * The possible column modifiers.
    *
    * @var array
    */
    protected $modifiers = [
        'Unsigned', 'Nullable','Default', 'Increment',  'After', 'First',
    ];

   /**
    * The possible column serials.
    *
    * @var array
    */
    protected $serials = [
        'bigInteger', 'integer', 'mediumInteger', 'smallInteger', 'tinyInteger'
    ];

   /**
    * Compile a create table command.
    *
    * @param object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param object Xcholars\Support\Fluent  $command
    * @param object Xcholars\Database\Connections\Connection  $connection
    * @return string
    */
    public function compileCreate(Blueprint $blueprint, Fluent $command, Connection $connection)
    {
        return $this->compileCreateTable(
            $blueprint, $command, $connection
        );
    }

   /**
    * Create the main create table clause.
    *
    * @param object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param object Xcholars\Support\Fluent  $command
    * @param object Xcholars\Database\Connections\Connection  $connection
    * @return string
    */
    private function compileCreateTable(Blueprint $blueprint, Fluent $command, Connection $connection)
    { 
        return sprintf('%s table %s %s (%s)',
                'create',
                'if not exists',
                $blueprint->getTable(),
                implode(', ', $this->getColumns($blueprint))
                 );
    }

   /**
    * Compile an add column command.
    *
    * @param object Xcholars\Support\Fluent  $command
    * @param object Xcholars\Database\Connections\Connection  $connection
    * @return string
    */
    public function compileAdd(Blueprint $blueprint, Fluent $command)
    {
        $columns = $this->prefixArray('add', $this->getColumns($blueprint));

        return 'alter table '.$blueprint->getTable().' '.implode(', ', $columns);
    }

   /**
    * Compile a primary key command.
    *
    * @param  object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param  object Xcholars\Support\Fluent  $command
    * @return string
    */
    public function compilePrimary(Blueprint $blueprint, Fluent $command)
    {
        $command->name(null);

        return $this->compileKey($blueprint, $command, 'primary key');
    }

    /**
    * Compile a unique key command.
    *
    * @param  object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param  object Xcholars\Support\Fluent  $command
    * @return string
    */
    public function compileUnique(Blueprint $blueprint, Fluent $command)
    {
        return $this->compileKey($blueprint, $command, 'unique');
    }

   /**
    * Compile an index creation command.
    *
    * @param  object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param  object Xcholars\Support\Fluent  $command
    * @param  string  $type
    * @return string
    */
    protected function compileKey(Blueprint $blueprint, Fluent $command, $type)
    {
        return sprintf('alter table %s add %s %s%s(%s)',
            $blueprint->getTable(),
            $type,
            $command->index,
            $command->algorithm ? ' using '.$command->algorithm : '',
            implode(', ', $command->columns)
        );
    }

   /**
    * Compile a drop table command.
    *
    * @param  object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param  object Xcholars\Support\Fluent  $command
    * @return string
    */
    public function compileDrop(Blueprint $blueprint, Fluent $command)
    {
        return 'drop table if exists ' . $blueprint->getTable();
    }

   /**
    * Compile a drop column command.
    *
    * @param  object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param  object Xcholars\Support\Fluent  $command
    * @return string
    */
    public function compileDropColumn(Blueprint $blueprint, Fluent $command)
    {
        $columns = $this->prefixArray('drop', $command->columns);

        return 'alter table '.$blueprint->getTable().' '.implode(', ', $columns);
    }

   /**
    * Compile a drop primary key command.
    *
    * @param  object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param  object Xcholars\Support\Fluent  $command
    * @return string
    */
    public function compileDropPrimary(Blueprint $blueprint, Fluent $command)
    {
        return 'alter table '.$blueprint->getTable().' drop primary key';
    }

   /**
    * Compile a drop unique key command.
    *
    * @param  object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param  object Xcholars\Support\Fluent  $command
    * @return string
    */
    public function compileDropUnique(Blueprint $blueprint, Fluent $command)
    {
        return "alter table {$blueprint->getTable()} drop index {$command->index}";
    }

   /**
    * Compile a drop foreign key command.
    *
    * @param  object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param  object Xcholars\Support\Fluent  $command
    * @return string
    */
    public function compileDropForeign(Blueprint $blueprint, Fluent $command)
    {
        return "alter table {$blueprint->getTable()} drop foreign key {$index}";
    }

   /**
    * Compile a rename table command.
    *
    * @param  object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param  object Xcholars\Support\Fluent  $command
    * @return string
    */
    public function compileRename(Blueprint $blueprint, Fluent $command)
    {
        $from = $blueprint->getTable();

        return "rename table {$from} to " . $command->to;
    }

   /**
    * Compile a rename table command.
    *
    * @param  object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param  object Xcholars\Support\Fluent  $command
    * @return string
    */
    public function compileRenameColumn(Blueprint $blueprint, Fluent $command)
    {
        $name = $blueprint->getTable();

        return "alter table {$name} change column {$command->from} {$command->to} {$command->type}";
    }


   /**
    * Compile a rename index command.
    *
    * @param  object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param  object Xcholars\Support\Fluent  $command
    * @return string
    */
   public function compileRenameIndex(Blueprint $blueprint, Fluent $command)
   {
       return sprintf('alter table %s rename index %s to %s',
           $blueprint->getTable(),
           $command->from,
           $command->to
       );
   }

   /**
    * Compile the SQL needed to drop all tables.
    *
    * @param  array  $tables
    * @return string
    */
    public function compileDropAllTables($tables)
    {
        return 'drop table '.implode(',', $tables);
    }

   /**
    * Compile the SQL needed to retrieve all table names.
    *
    * @return string
    */
    public function compileGetAllTables()
    {
        return 'SHOW FULL TABLES WHERE table_type = \'BASE TABLE\'';
    }

   /**
    * Add a prefix to an array of values.
    *
    * @param  string  $prefix
    * @param  array  $values
    * @return array
    */
    public function prefixArray($prefix, array $values)
    {
        return array_map(function ($value) use ($prefix)
        {
            return $prefix.' '.$value;

        }, $values);
    }

   /**
    * Create the column definition for a char type.
    *
    * @param  object Xcholars\Support\Fluent $column
    * @return string
    */
    protected function typeChar(Fluent $column)
    {
        return "char({$column->length})";
    }

    /**
     * Create the column definition for a string type.
     *
     * @param  object Xcholars\Support\Fluent $column
     * @return string
     */
    protected function typeString(Fluent $column)
    {
        return "varchar({$column->length})";
    }

   /**
    * Create the column definition for a text type.
    *
    * @param  object Xcholars\Support\Fluent $column
    * @return string
    */
    protected function typeText(Fluent $column)
    {
        return 'text';
    }

   /**
    * Create the column definition for a medium text type.
    *
    * @param  object Xcholars\Support\Fluent $column
    * @return string
    */
    protected function typeMediumText(Fluent $column)
    {
        return 'mediumtext';
    }

   /**
    * Create the column definition for a long text type.
    *
    * @param  object Xcholars\Support\Fluent $column
    * @return string
    */
    protected function typeLongText(Fluent $column)
    {
        return 'longtext';
    }

   /**
    * Create the column definition for a big integer type.
    *
    * @param  object Xcholars\Support\Fluent $column
    * @return string
    */
    protected function typeBigInteger(Fluent $column)
    {
        return 'bigint';
    }

   /**
    * Create the column definition for an integer type.
    *
    * @param  object Xcholars\Support\Fluent $column
    * @return string
    */
    protected function typeInteger(Fluent $column)
    {
        return 'int';
    }

   /**
    * Create the column definition for a medium integer type.
    *
    * @param  object Xcholars\Support\Fluent $column
    * @return string
    */
    protected function typeMediumInteger(Fluent $column)
    {
        return 'mediumint';
    }

   /**
    * Create the column definition for a tiny integer type.
    *
    * @param  object Xcholars\Support\Fluent $column
    * @return string
    */
    protected function typeTinyInteger(Fluent $column)
    {
        return 'tinyint';
    }

   /**
    * Create the column definition for a small integer type.
    *
    * @param  object Xcholars\Support\Fluent $column
    * @return string
    */
    protected function typeSmallInteger(Fluent $column)
    {
        return 'smallint';
    }

   /**
    * Create the column definition for a boolean type.
    *
    * @param object  Xcholars\Support\Fluent  $column
    * @return string
    */
    protected function typeBoolean(Fluent $column)
    {
        return 'tinyint(1)';
    }

   /**
    * Create the column definition for a date type.
    *
    * @param object  Xcholars\Support\Fluent  $column
    * @return string
    */
    protected function typeDate(Fluent $column)
    {
        return 'date';
    }

   /**
    * Create the column definition for a date-time type.
    *
    * @param object  Xcholars\Support\Fluent  $column
    * @return string
    */
    protected function typeDateTime(Fluent $column)
    {
        $columnType = $column->precision ? "datetime($column->precision)" : 'datetime';

        return $column->useCurrent ? "$columnType default CURRENT_TIMESTAMP" : $columnType;
    }

   /**
    * Create the column definition for a time type.
    *
    * @param object  Xcholars\Support\Fluent  $column
    * @return string
    */
    protected function typeTime(Fluent $column)
    {
        return $column->precision ? "time($column->precision)" : 'time';
    }

   /**
    * Create the column definition for a timestamp type.
    *
    * @param object  Xcholars\Support\Fluent  $column
    * @return string
    */
    protected function typeTimestamp(Fluent $column)
    {
        $columnType = $column->precision ? "timestamp($column->precision)" : 'timestamp';

        $defaultCurrent = $column->precision ? "CURRENT_TIMESTAMP($column->precision)" : 'CURRENT_TIMESTAMP';

        return $column->useCurrent ? "$columnType default $defaultCurrent" : $columnType;
    }

   /**
    * Create the column definition for a year type.
    *
    * @param object  Xcholars\Support\Fluent  $column
    * @return string
    */
    protected function typeYear(Fluent $column)
    {
        return 'year';
    }

   /**
    * Create the column definition for an IP address type.
    *
    * @param object  Xcholars\Support\Fluent  $column
    * @return string
    */
    protected function typeIpAddress(Fluent $column)
    {
        return 'varchar(45)';
    }

   /**
    * Get the SQL for an unsigned column modifier.
    *
    * @param object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param object Xcholars\Support\Fluent  $column
    * @return string|null
    */
    protected function modifyUnsigned(Blueprint $blueprint, Fluent $column)
    {
        if ($column->unsigned)
        {
            return ' unsigned';
        }
    }

   /**
    * Get the SQL for a nullable column modifier.
    *
    * @param object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param object Xcholars\Support\Fluent  $column
    * @return string|null
    */
    protected function modifyNullable(Blueprint $blueprint, Fluent $column)
    {
        if ($column->nullable === false)
        {
            return ' not null';
        }
    }

   /**
    * Get the SQL for a default column modifier.
    *
    * @param object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param object Xcholars\Support\Fluent  $column
    * @return string|null
    */
    protected function modifyDefault(Blueprint $blueprint, Fluent $column)
    {
        if (!is_null($column->default))
        {
            return ' default ' . $this->getDefaultValue($column->default);
        }
    }

   /**
    * Get the SQL for an auto-increment column modifier.
    *
    * @param object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param object Xcholars\Support\Fluent  $column
    * @return string|null
    */
    protected function modifyIncrement(Blueprint $blueprint, Fluent $column)
    {
        if (in_array($column->type, $this->serials) && $column->autoIncrement)
        {
            return ' auto_increment primary key';
        }
    }

   /**
    * Get the SQL for a "first" column modifier.
    *
    * @param object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param object Xcholars\Support\Fluent  $column
    * @return string|null
    */
    protected function modifyFirst(Blueprint $blueprint, Fluent $column)
    {
        if (!is_null($column->first))
        {
            return ' first';
        }
    }

   /**
    * Get the SQL for an "after" column modifier.
    *
    * @param object Xcholars\Database\Schema\Blueprint  $blueprint
    * @param object Xcholars\Support\Fluent  $column
    * @return string|null
    */
    protected function modifyAfter(Blueprint $blueprint, Fluent $column)
    {
        if (!is_null($column->after))
        {
            return ' after ' . $column->after;
        }
    }
}
