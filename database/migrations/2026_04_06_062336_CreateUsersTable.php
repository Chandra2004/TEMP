<?php

namespace Database\Migrations;

use TheFramework\App\Schema;


class Migration_2026_04_06_062336_CreateUsersTable
{
    public function up()
    {
        Schema::create('users', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_active')->default(1);
            $table->string('remember_token', 100)->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}