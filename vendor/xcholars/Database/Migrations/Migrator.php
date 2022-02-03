<?php

Namespace Xcholars\Database\Migrations;

use Xcholars\Database\DatabaseManager;

class Migrator
{
   /**
    * Run the migrations.
    *
    * @var string
    */
    private $action;

   /**
    * Run the migrations.
    *
    * @var string
    */
    private $status;

   /**
    * DatabaseManager instance
    *
    * @var object Xcholars\Database\Manager
    */
    private $conn;

   /**
    * Create new instance of Migrator
    *
    * @param object Xcholars\Database\Manager
    * @param string $action
    * @param string $status
    * @return void
    */
    public function __construct(DatabaseManager $conn, $action, $status)
    {
        $this->conn = $conn;

        $this->action = $action;

        $this->status = $status;
    }

   /**
    * Run migrations
    *
    * @return void
    */
    public function migrate()
    {
        $this->migrationsTable()->up();

        if ($this->action === 'fresh')
        {
            $this->fresh();
        }

        if ($this->status === 'ON')
        {
            $this->run($this->getNewMigrations());
        }
    }

   /**
    * get New Migrations
    *
    * @return array
    */
    public function getNewMigrations()
    {
        $migrations = $this->getMigrationsFromDatabase();

        $classes = $this->getMigrationsClasses();

        return array_diff($classes, $migrations);
    }

   /**
    * Run migrations a fresh
    *
    * param array $migrations
    * @return void
    */
    public function fresh()
    {
        $migrations = $this->getMigrationsFromDatabase();

        $this->action = 'down';

        $this->run($migrations);

        $this->migrationsTable()->down();

        $this->migrationsTable()->up();

        $this->action = 'up';
    }

   /**
    * Run migrations
    *
    * @return void
    */
    public function getMigrationsClasses()
    {
        return array_map(function ($filePath)
        {
            return substr(basename($filePath), 0, -4);

        }, glob('app/Database/Migrations/*.php'));
    }

   /**
    * Run migrations
    *
    * @return void
    */
    public function getMigrationsFromDatabase()
    {
        $migrations = $this->conn->table('migrations')->get();

        foreach ($migrations as $key => $migration)
        {
            $migrations[$key] = $migration->migration;
        }

        return $migrations;
    }

   /**
    * get Migrations Table instance
    *
    * @return object
    */
    public function migrationsTable()
    {
        return (new CreateMigrationTable);
    }

   /**
    * run Migrations
    *
    * @return void
    */
    public function run($newMigrations)
    {
        $migrations = [];

        foreach ($newMigrations as $migration)
        {
            $class = 'App\Database\Migrations\\' . $migration;

            (new $class)->{mb_strtolower($this->action)}();

            $migrations[] = ['migration' => $migration];
        }

        $this->conn->table('migrations')->insert($migrations);

    }
}
