<?php

Namespace Xcholars\Database\Connections;

use Xcholars\Database\Query\Grammars\Grammar as QueryGrammar;

use Xcholars\Database\Query\Processors\Processor;

use Xcholars\Database\Query\Builder as QueryBuilder;

use Xcholars\Database\Schema\Builder as SchemaBuilder;

use Closure;

use PDOStatement;

use Exception;

use PDO;

use Xcholars\Database\Exceptions\QueryException;

abstract class Connection implements ConnectionContract
{
   /**
    * The active PDO connection.
    *
    * @var object \PDO|\Closure
    */
    protected $pdo;

   /**
    * The name of the connected database.
    *
    * @var string
    */
    protected $database;

   /**
    * The database connection configuration options.
    *
    * @var array
    */
    protected $config = [];

    /**
    * The schema grammar implementation.
    *
    * @var object XcholarsDatabase\Schema\Grammars\Grammar
    */
    protected $schemaGrammar;

   /**
    * The query grammar implementation.
    *
    * @var object Xcholars\Database\Query\Grammars\Grammar
    */
    protected $queryGrammar;

   /**
    * The query post processor implementation.
    *
    * @var object Xcholars\Database\Query\Grammars\Grammar
    */
    protected $postProcessor;

   /**
    * The default fetch mode of the connection.
    *
    * @var int
    */
    protected $fetchMode = PDO::FETCH_OBJ;

   /**
    * Create a new database connection instance.
    *
    * @param object \PDO|\Closure  $pdo
    * @param  string  $database
    * @param  string  $tablePrefix
    * @param  array  $config
    * @return void
    */
    public function __construct($pdo, $database = '', array $config = [])
    {
        $this->pdo = $pdo;

        $this->database = $database;

        $this->database = $database;

        $this->config = $config;

        $this->setQueryGrammar();

        $this->setPostProcessor();
    }

   /**
    * Get a schema builder instance for the connection.
    *
    * @return object Xcholars\Database\Schema\Builder
    */
    public function getSchemaBuilder()
    {
        if (!$this->schemaGrammar)
        {
            $this->schemaGrammar = $this->setSchemaGrammar();
        }

        return new SchemaBuilder($this);
    }

   /**
    * Get the post processor instance.
    *
    * @return object Xcholars\Database\Query\Processors\MySqlProcessor
    */
    public function getPostProcessor()
    {
        return $this->processor;
    }

   /**
    * Get a new query builder instance.
    *
    * @return object Xcholars\Database\Query\Builder
    */
    public function query()
    {
        return new QueryBuilder($this);
    }

   /**
    * Get a schema Grammar instance for the connection.
    *
    * @return object Xcholars\Database\Schema\Grammars\Grammar
    */
    public function getSchemaGrammar()
    {
        if (!$this->schemaGrammar)
        {
            $this->schemaGrammar = $this->setSchemaGrammar();
        }

        return $this->schemaGrammar;
    }

   /**
    * Get the current PDO connection.
    *
    * @return object \PDO
    */
    public function getPdo()
    {
        if ($this->pdo instanceof Closure)
        {
            return $this->pdo = call_user_func($this->pdo);
        }

        return $this->pdo;
    }

   /**
    * Get the database connection name.
    *
    * @return string|null
    */
    public function getName()
    {
        return $this->config['name'];
    }

   /**
    * Configure the PDO prepared statement.
    *
    * @param object \PDOStatement  $statement
    * @return object \PDOStatement
    */
    protected function prepared(PDOStatement $statement)
    {
        $statement->setFetchMode($this->fetchMode);

        return $statement;
    }

   /**
    * Execute an SQL statement and return the boolean result.
    *
    * @param  string  $query
    * @param  array  $bindings
    * @return bool
    */
    public function statement($query, $bindings = [])
    {
        return $this->run($query, $bindings, function ($query, $bindings)
        {
            $statement = $this->getPdo()->prepare($query);

            $this->bindValues($statement, $this->prepareBindings($bindings));

            return $statement->execute();
        });

    }

    /**
    * Run an SQL statement and get the number of rows affected.
    *
    * @param  string  $query
    * @param  array  $bindings
    * @return int
    */
    public function affectingStatement($query, $bindings = [])
    {
        return $this->run($query, $bindings, function ($query, $bindings)
        {
            $statement = $this->getPdo()->prepare($query);

            $this->bindValues($statement, $this->prepareBindings($bindings));

            $statement->execute();

            return $statement->rowCount() > 0;
        });
    }


   /**
    * Run a select statement against the database.
    *
    * @param  string  $query
    * @param  array  $bindings
    * @return array
    */
    public function select($query, $bindings = [])
    {
        return $this->run($query, $bindings, function ($query, $bindings)
        {
            $statement = $this->prepared($this->getPdo()->prepare($query));

            $this->bindValues($statement, $this->prepareBindings($bindings));

            $statement->execute();
           
            return $statement->fetchAll();
        });
    }

   /**
    * Run an insert statement against the database.
    *
    * @param  string  $query
    * @param  array  $bindings
    * @return bool
    */
    public function insert($query, $bindings = [])
    {
        return $this->statement($query, $bindings);
    }

   /**
    * Run an update statement against the database.
    *
    * @param  string  $query
    * @param  array  $bindings
    * @return int
    */
    public function update($query, $bindings = [])
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
    * Run a delete statement against the database.
    *
    * @param  string  $query
    * @param  array  $bindings
    * @return int
    */
    public function delete($query, $bindings = [])
    {
        return $this->affectingStatement($query, $bindings);
    }

   /**
    * Run a SQL statement and log its execution context.
    *
    * @param  string  $query
    * @param  array  $bindings
    * @param  object \Closure  $callback
    * @return mixed
    *
    * @throws object Xcholars\Database\Exceptions\QueryException
    */
    protected function run($query, $bindings, Closure $callback)
    {
        try
        {
            return call_user_func($callback, $query, $bindings);
        }
        catch (Exception $error)
        {
            throw new QueryException("Query Failed: [{$error}]");
        }

    }

   /**
    * Get the query grammar used by the connection.
    *
    * @return object Xcholars\Database\Query\Grammars\Grammar
    */
    public function getQueryGrammar()
    {
        return $this->queryGrammar;
    }

   /**
    * Bind values to their parameters in the given statement.
    *
    * @param object \PDOStatement  $statement
    * @param  array  $bindings
    * @return void
    */
    public function bindValues(PDOStatement $statement, array $bindings)
    {
        foreach ($bindings as $key => $value)
        {
            $key =  is_string($key) ? $key : $key + 1;

            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;

            $statement->bindValue($key, $value, $type);
        }
    }

   /**
    * Prepare the query bindings for execution.
    *
    * @param  array  $bindings
    * @return array
    */
    public function prepareBindings(array $bindings)
    {
        $grammar = $this->getQueryGrammar();

        foreach ($bindings as $key => $value)
        {
            if ($value instanceof DateTimeInterface)
            {
                $bindings[$key] = $value->format($grammar->getDateFormat());
            }
            elseif (is_bool($value))
            {
                $bindings[$key] = (int) $value;
            }
        }

        return $bindings;
    }

   /**
    * Begin a fluent query against a database table.
    *
    * @param object|string Closure|Xcholars\Database\Query\Builder|$table
    * @return object Xcholars\Database\Query\Builder
    */
    public function table($table)
    {
        return $this->query()->from($table);
    }
}
