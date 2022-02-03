<?php

Namespace App\Database\Migrations;

use Xcholars\Support\Proxies\Schema;

use Xcholars\Database\Schema\Blueprint;

class CreatethreadsTable
{
   /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('threads', function (Blueprint $table)
        {
            $table->id();

            $table->integer('user_id');

            $table->string('country');

            $table->string('title');

            $table->string('link');

            $table->text('body');

            $table->integer('clicks')->default('0');

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
        Schema::drop('threads');
    }
}
