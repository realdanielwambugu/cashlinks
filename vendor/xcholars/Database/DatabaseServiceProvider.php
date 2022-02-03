<?php

Namespace Xcholars\Database;

use Xcholars\Provider\ServiceProvider;

use Xcholars\Database\Connectors\MySqlConnector;

use Xcholars\Database\Connections\MySqlConnection;

use Xcholars\Database\Orm\Model;

use Xcholars\Settings\SettingsContract;

class DatabaseServiceProvider extends ServiceProvider
{
  /**
   * register bindings with the service container.
   *
   * @return object
   */
   public function register()
   {
       $this->app->singleton(ConnectionFactory::class);

       $this->app->singleton(DatabaseManager::class);
   }

  /**
   * Activities to be performed after bindings are registerd.
   *
   * @return void
   */
   public function boot()
   {
        $this->app->make(ConnectionFactory::class)
                 ->setConnectors([
                    'mysql' => MySqlConnector::class,
                ])->setSupportedDriver([
                    'mysql' => MySqlConnection::class,
                ]);

        Model::setConnectionResolver($this->app->make(DatabaseManager::class));

        $settings = $this->app->make(SettingsContract::class);

        $migrator = $this->app->make(
                    \Xcholars\Database\Migrations\Migrator::class, [
                    $settings->get('database.migrations.action'),
                    $settings->get('database.migrations.status')
                    ])->migrate();
   }
}
