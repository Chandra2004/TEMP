<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_02_10_053241_CreateRolesTable {
    public function up()
    {
        Schema::create('roles', function ($table) {
            $table->increments('id_role');
            $table->string('uid', 36)->unique();
            $table->string('nama_role', 50);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
}