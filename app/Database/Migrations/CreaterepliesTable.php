<?php

Namespace App\Database\Migrations;

use Xcholars\Support\Proxies\Schema;

use Xcholars\Database\Schema\Blueprint;

class CreaterepliesTable
{
   /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('replies', function (Blueprint $table)
        {
            $table->id();
            
            $table->integer('user_id');

            $table->integer('comment_id');

            $table->integer('sub_reply_id');

            $table->text('body');

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
        Schema::drop('replies');
    }
}
