<?php

Namespace Xcholars\Database\Schema;

use Xcholars\Database\Connections\Connection;

use Xcholars\Database\Schema\Grammars\Grammar;

use Xcholars\Database\Schema\ColumnDefinition;

use Xcholars\Support\Fluent;

use Closure;

class Blueprint
{

   /**
    * The table the blueprint describes.
    *
    * @var string
    */
    private $table;

   /**
    * The commands that should be run for the table.
    *
    * @var array
    */
    private $commands = [];

  /**
    * The columns that should be added to the table.
    *
    * @var array
    */
    private $columns = [];

   /**
    * Create a new schema blueprint instance.
    * @param object \Closure|null  $callback
    * @param string $table
    * @return void
    */
    public function __construct($table, Closure $callback = null)
    {
        $this->table = $table;

        if (! is_null($callback))
        {
            $callback($this);
        }
    }

   /**
    * Get the table the blueprint describes.
    *
    * @return string
    */
    public function getTable()
    {
        return $this->table;
    }

   /**
    * Execute the blueprint against the database.
    *
    * @param object Xcholars\Database\Connection $connection
    * @param object Xcholars\Database\Schema\Grammars\Grammar $grammar
    * @return void
    */
    public function build(Connection $connection, Grammar $grammar)
    {
        foreach ($this->toSql($connection, $grammar) as $statement)
        {
            $connection->statement($statement);
        }
    }

   /**
    * Get the raw SQL statements for the blueprint.
    *
    * @param object Xcholars\Database\Connection $connection
    * @param object Xcholars\Database\Schema\Grammars\Grammar $grammar
    * @return array
    */
    private function toSql(Connection $connection, Grammar $grammar)
    {
        $this->addImpliedCommands($grammar);

        $statements = [];

        foreach ($this->commands as $command)
        {
            $method = 'compile' . ucfirst($command->name);

            if (method_exists($grammar, $method))
            {
                $sql = call_user_func(
                    [$grammar, $method], $this, $command, $connection
                );

                if (!is_null($sql))
                {
                    $statements = array_merge($statements, (array) $sql);
                }
            }

        }

        return $statements;
    }

   /**
    * Add the commands that are implied by the blueprint's state.
    *
    * @param object Xcholars\Database\Schema\Grammars\Grammar $grammar
    * @return void
    */
    private function addImpliedCommands()
    {
        if (count($this->getAddedColumns()) > 0 && ! $this->isCreating())
        {
            array_unshift($this->commands, $this->createCommand('add'));
        }

        if (count($this->getChangedColumns()) > 0 && ! $this->isCreating())
        {
            array_unshift($this->commands, $this->createCommand('change'));
        }

        $this->addFluentIndexes();
    }

   /**
    * Get the columns on the blueprint that should be added.
    *
    * @return array Xcholars\Database\Schema\ColumnDefinition[]
    */
    public function getAddedColumns()
    {
        return array_filter($this->columns, function ($column)
        {
            return ! $column->change;
        });
    }

   /**
    * Get the columns on the blueprint that should be changed.
    *
    * @return array Xcholars\Database\Schema\ColumnDefinition[]
    */
    public function getChangedColumns()
    {
        return array_filter($this->columns, function ($column)
        {
            return (bool) $column->change;
        });
    }

   /**
    * Determine if the blueprint has a create command.
    *
    * @return bool
    */
    private function isCreating()
    {
        return count(array_filter($this->commands, function ($command)
        {
            return $command->name === 'create';

        }));
    }

   /**
    * Add the index commands fluently specified on columns.
    *
    * @return void
    */
    private function addFluentIndexes()
    {
        foreach ($this->columns as $column)
        {
            foreach (['primary', 'unique'] as $index)
            {
                if ($column->{$index} === true)
                {
                    call_user_func([$this, $index], $column->name);

                    $column->{$index} = false;

                }
                elseif (isset($column->{$index}))
                {
                    call_user_func(
                        [$this, $index], $column->name, $column->{$index}
                    );

                    $column->{$index} = false;
                }
            }
        }
    }

   /**
    * Indicate that the table needs to be created.
    *
    * @return array
    */
    public function create()
    {
        return $this->addCommand('create');
    }

   /**
    * Rename the table to a given name.
    *
    * @param string  $to
    * @return object Xcholars\Support\Fluent
    */
    public function rename($to)
    {
        return $this->addCommand('rename', compact('to'));
    }

   /**
    * Indicate that the table should be dropped.
    *
    * @return object Xcholars\Support\Fluent
    */
    public function drop()
    {
        return $this->addCommand('drop');
    }

   /**
    * Add a new command to the blueprint.
    *
    * @param  string  $name
    * @param  array  $parameters
    * @return array
    */
    private function addCommand($name, array $parameters = [])
    {
        $this->commands[] = $command = $this->createCommand($name, $parameters);

        return $command;
    }

   /**
    * Create a new Fluent command.
    *
    * @param  string  $name
    * @param  array  $parameters
    * @return object Xcholars\Support/Fluent
    */
    private function createCommand($name, array $parameters = [])
    {
        return new Fluent(array_merge(compact('name'), $parameters));
    }

   /**
    * Create a new auto-incrementing big integer (8-byte) column on the table.
    *
    * @param string  $column
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function id($column = 'id')
    {
        return $this->bigIncrements($column);
    }

    /**
    * Create a new unsigned big integer (8-byte) column on the table.
    *
    * @param string $column
    * @return object Xcholars\Database\Schema\ForeignIdColumnDefinition
    */
    public function foreignId($column)
    {
        $column = new ForeignIdColumnDefinition($this, [
            'type' => 'bigInteger',
            'name' => $column,
            'autoIncrement' => false,
            'unsigned' => true,
        ]);

        $this->columns[] = $column;

        return $column;
    }

   /**
    * Create a new auto-incrementing big integer (8-byte) column on the table.
    *
    * @param  string  $column
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function bigIncrements($column)
    {
        return $this->unsignedBigInteger($column, true);
    }

   /**
    * Create a new auto-incrementing integer (4-byte) column on the table.
    *
    * @param  string  $column
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function increments($column)
    {
        return $this->unsignedInteger($column, true);
    }

   /**
    * Create a new unsigned integer (4-byte) column on the table.
    *
    * @param  string  $column
    * @param  bool  $autoIncrement
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function unsignedInteger($column, $autoIncrement = false)
    {
        return $this->integer($column, $autoIncrement, true);
    }

   /**
    * Create a new integer (4-byte) column on the table.
    *
    * @param  string  $column
    * @param  bool  $autoIncrement
    * @param  bool  $unsigned
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function integer($column, $autoIncrement = false, $unsigned = false)
    {
        return $this->addColumn(
            'integer', $column, compact('autoIncrement', 'unsigned')
        );
    }

   /**
    * Create a new IP address column on the table.
    *
    * @param  string  $column
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function ipAddress($column)
    {
        return $this->addColumn('ipAddress', $column);
    }

   /**
    * Create a new long text column on the table.
    *
    * @param  string  $column
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function longText($column)
    {
        return $this->addColumn('longText', $column);
    }

   /**
    * Create a new medium text column on the table.
    *
    * @param  string  $column
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function mediumText($column)
    {
        return $this->addColumn('mediumText', $column);
    }

   /**
    * Create a new text column on the table.
    *
    * @param  string  $column
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function text($column)
    {
        return $this->addColumn('text', $column);
    }

   /**
    * Create a new string column on the table.
    *
    * @param  string  $column
    * @param  int  $length
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function string($column, $length = 255)
    {
        return $this->addColumn('string', $column, compact('length'));
    }

   /**
    * Add nullable creation and update timestamps to the table.
    *
    * Alias for self::timestamps().
    *
    * @param  int  $precision
    * @return void
    */
    public function nullableTimestamps($precision = 0)
    {
        $this->timestamps($precision);
    }

   /**
    * Add nullable creation and update timestamps to the table.
    *
    * @param  int  $precision
    * @return void
    */
    public function timestamps($precision = 0)
    {
        $this->timestamp('updated_at', $precision)->nullable();

        $this->timestamp('created_at', $precision)->useCurrent()->nullable();
    }

   /**
    * Indicate that the timestamp columns should be dropped.
    *
    * @return void
    */
    public function dropTimestamps()
    {
        $this->dropColumn('created_at', 'updated_at');
    }

   /**
    * Create a new timestamp column on the table.
    *
    * @param  string  $column
    * @param  int  $precision
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function timestamp($column, $precision = 0)
    {
        return $this->addColumn('timestamp', $column, compact('precision'));
    }

   /**
    * Create a new time column on the table.
    *
    * @param  string  $column
    * @param  int  $precision
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function time($column, $precision = 0)
    {
        return $this->addColumn('time', $column, compact('precision'));
    }

    /**
    * Create a new unsigned big integer (8-byte) column on the table.
    *
    * @param  string  $column
    * @param  bool  $autoIncrement
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function unsignedBigInteger($column, $autoIncrement = false)
    {
        return $this->bigInteger($column, $autoIncrement, true);
    }

   /**
    * Create a new big integer (8-byte) column on the table.
    *
    * @param  string  $column
    * @param  bool  $autoIncrement
    * @param  bool  $unsigned
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function bigInteger($column, $autoIncrement = false, $unsigned = false)
    {
        return $this->addColumn(
            'bigInteger', $column, compact('autoIncrement', 'unsigned')
        );
    }

   /**
    * Create a new auto-incrementing medium integer (3-byte) column on the table.
    *
    * @param string $column
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function mediumIncrements($column)
    {
        return $this->unsignedMediumInteger($column, true);
    }

   /**
    * Create a new unsigned medium integer (3-byte) column on the table.
    *
    * @param string $column
    * @param bool $autoIncrement
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function unsignedMediumInteger($column, $autoIncrement = false)
    {
        return $this->mediumInteger($column, $autoIncrement, true);
    }

   /**
    * Create a new medium integer (3-byte) column on the table.
    *
    * @param  string  $column
    * @param  bool  $autoIncrement
    * @param  bool  $unsigned
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function mediumInteger($column, $autoIncrement = false, $unsigned = false)
    {
        return $this->addColumn(
            'mediumInteger', $column, compact('autoIncrement', 'unsigned')
        );
    }

   /**
    * Create a new auto-incrementing small integer (2-byte) column on the table.
    *
    * @param  string  $column
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function smallIncrements($column)
    {
        return $this->unsignedSmallInteger($column, true);
    }

   /**
    * Create a new unsigned small integer (2-byte) column on the table.
    *
    * @param  string  $column
    * @param  bool  $autoIncrement
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function unsignedSmallInteger($column, $autoIncrement = false)
    {
        return $this->smallInteger($column, $autoIncrement, true);
    }

   /**
    * Create a new small integer (2-byte) column on the table.
    *
    * @param  string  $column
    * @param  bool  $autoIncrement
    * @param  bool  $unsigned
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function smallInteger($column, $autoIncrement = false, $unsigned = false)
    {
        return $this->addColumn(
            'smallInteger', $column, compact('autoIncrement', 'unsigned')
        );
    }

   /**
    * Create a new auto-incrementing tiny integer (1-byte) column on the table.
    *
    * @param  string  $column
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function tinyIncrements($column)
    {
        return $this->unsignedTinyInteger($column, true);
    }

   /**
    * Create a new unsigned tiny integer (1-byte) column on the table.
    *
    * @param  string  $column
    * @param  bool  $autoIncrement
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function unsignedTinyInteger($column, $autoIncrement = false)
    {
        return $this->tinyInteger($column, $autoIncrement, true);
    }

   /**
    * Create a new tiny integer (1-byte) column on the table.
    *
    * @param  string  $column
    * @param  bool  $autoIncrement
    * @param  bool  $unsigned
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function tinyInteger($column, $autoIncrement = false, $unsigned = false)
    {
        return $this->addColumn('tinyInteger', $column, compact('autoIncrement', 'unsigned'));
    }

   /**
    * Create a new boolean column on the table.
    *
    * @param string  $column
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function boolean($column)
    {
        return $this->addColumn('boolean', $column);
    }

   /**
    * Create a new char column on the table.
    *
    * @param string  $column
    * @param int  $length
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function char($column, $length = 255)
    {
        return $this->addColumn('char', $column, compact('length'));
    }

   /**
    * Create a new date-time column on the table.
    *
    * @param  string  $column
    * @param  int  $precision
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function dateTime($column, $precision = 0)
    {
        return $this->addColumn('dateTime', $column, compact('precision'));
    }

   /**
    * Create a new date column on the table.
    *
    * @param  string  $column
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function date($column)
    {
        return $this->addColumn('date', $column);
    }

   /**
    * Create a new year column on the table.
    *
    * @param  string  $column
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function year($column)
    {
       return $this->addColumn('year', $column);
    }

   /**
    * Specify the primary key(s) for the table.
    *
    * @param  string|array  $columns
    * @param  string|null  $name
    * @param  string|null  $algorithm
    * @return object Xcholars\Support\Fluent
    */
    public function primary($columns, $name = null, $algorithm = null)
    {
        return $this->indexCommand('primary', $columns, $name, $algorithm);
    }

   /**
    * Specify a unique index for the table.
    *
    * @param  string|array  $columns
    * @param  string|null  $name
    * @param  string|null  $algorithm
    * @return object Xcholars\Support\Fluent
    */
    public function unique($columns, $name = null, $algorithm = null)
    {
        return $this->indexCommand('unique', $columns, $name, $algorithm);
    }

   /**
    * Indicate that the given primary key should be dropped.
    *
    * @param  string|array|null  $index
    * @return object Xcholars\Support\Fluent
    */
    public function dropPrimary($index = null)
    {
        return $this->dropIndexCommand('dropPrimary', 'primary', $index);
    }

   /**
    * Indicate that the given unique key should be dropped.
    *
    * @param  string|array  $index
    * @return object Xcholars\Support\Fluent
    */
    public function dropUnique($index)
    {
        return $this->dropIndexCommand('dropUnique', 'unique', $index);
    }

   /**
    * Indicate that the given indexes should be renamed.
    *
    * @param  string  $from
    * @param  string  $to
    * @return object Xcholars\Support\Fluent
    */
    public function renameIndex($from, $to)
    {
        return $this->addCommand('renameIndex', compact('from', 'to'));
    }

   /**
    * Create a new drop index command on the blueprint.
    *
    * @param  string  $command
    * @param  string  $type
    * @param  string|array  $index
    * @return object Xcholars\Support\Fluent
    */
    protected function dropIndexCommand($command, $type, $index)
    {
        $columns = [];

        if (is_array($index))
        {
            $index = $this->createIndexName($type, $columns = $index);
        }

        return $this->indexCommand($command, $columns, $index);
    }

   /**
    * Add a new index command to the blueprint.
    *
    * @param  string  $type
    * @param  string|array  $columns
    * @param  string  $index
    * @param  string|null  $algorithm
    * @return object Xcholars\Support\Fluent
    */
    private function indexCommand($type, $columns, $index, $algorithm = null)
    {
        $columns = (array) $columns;

        $index = $index ?: $this->createIndexName($type, $columns);

        return $this->addCommand(
            $type, compact('index', 'columns', 'algorithm')
        );
    }

   /**
    * Create a default index name for the table.
    *
    * @param  string  $type
    * @param  array  $columns
    * @return string
    */
    private function createIndexName($type, array $columns)
    {
        $index = strtolower($this->table.'_'.implode('_', $columns).'_'.$type);

        return str_replace(['-', '.'], '_', $index);
    }

   /**
    * Add a new column to the blueprint.
    *
    * @param  string  $type
    * @param  string  $name
    * @param  array  $parameters
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    private function addColumn($type, $name, array $parameters = [])
    {
        $column = new ColumnDefinition(array_merge(
                    compact('type', 'name'), $parameters)
                );

        $this->columns[] = $column;

        return $column;
    }

   /**
    * Indicate that the given columns should be renamed.
    *
    * @param  string  $from
    * @param  string  $to
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function renameColumn($from, $to, $type)
    {
        return $this->addCommand('renameColumn', compact('from', 'to', 'type'));
    }

   /**
    * Indicate that the given columns should be dropped.
    *
    * @param  array|mixed  $columns
    * @return object Xcholars\Database\Schema\ColumnDefinition
    */
    public function dropColumn($columns)
    {
        $columns = is_array($columns) ? $columns : func_get_args();

        return $this->addCommand('dropColumn', compact('columns'));
    }


}
