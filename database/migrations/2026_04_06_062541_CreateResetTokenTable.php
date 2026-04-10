<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_062541_CreateResetTokenTable
{
    public function up()
    {
        Schema::create('reset_token', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('uid_user', 36)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('token');
            $table->timestamp('valid_until')->nullable();

            $table->timestamps();

            $table->foreign('uid_user')->references('uid')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reset_token');
    }
}