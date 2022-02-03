<?php

return [

  'database' => [

   /*
   |--------------------------------------------------------------------------
   | Default Database Connection Name
   |--------------------------------------------------------------------------
   |
   | Specify which of the database connections below should be used as
   | default connection for all database work.
   |
   */

    'default' => $_ENV['DB_CONNECTION'] ?? 'mysql',

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Database connections setup for the application.
    |
    */

    'connections' => [

        'mysql' => [
            'driver' => 'mysql',
            'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'database' => $_ENV['DB_DATABASE'] ?? 'cashlink_db',
            'username' => $_ENV['DB_USERNAME'] ?? 'cashlink_admin',
            'password' => $_ENV['DB_PASSWORD'] ?? 'cashlink_db_pass',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'strict' => true,
        ],

        'options' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
            PDO::ATTR_STRINGIFY_FETCHES => false,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Database Migrations
    |--------------------------------------------------------------------------
    |
    | Specify database migrations action here
    |
    */

   'migrations' => [
        'status' => 'ON',
        'action' => 'up',
    ],

  ],
];
