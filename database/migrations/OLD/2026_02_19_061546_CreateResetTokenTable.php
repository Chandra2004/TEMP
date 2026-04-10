<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_02_19_061546_CreateResetTokenTable {
    public function up()
    {
        Schema::create('reset_token', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            
            $table->string('email', 255)->unique();
            $table->string('token', 36)->unique();
            $table->dateTime('valid_until');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reset_token');
    }
}