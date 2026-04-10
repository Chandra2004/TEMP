<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_062402_CreateClubsTable
{
    public function up()
    {
        Schema::create('clubs', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('nama_klub');
            $table->string('logo_klub')->nullable();
            $table->string('nama_pelatih')->nullable();
            $table->string('kontak_klub')->nullable();
            $table->string('alamat_klub')->nullable();
            $table->string('website_klub')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clubs');
    }
}