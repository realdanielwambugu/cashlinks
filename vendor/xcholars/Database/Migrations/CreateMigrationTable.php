<?php

Namespace Xcholars\Database\Migrations;

use Xcholars\Support\Proxies\Schema;

use Xcholars\Database\Schema\Blueprint;

class CreateMigrationTable
{
   /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('migrations', function (Blueprint $table)
        {
            $table->id();

            $table->string('migration');

            $table->timestamps();
        });
    }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::drop('migrations');
    }
}
