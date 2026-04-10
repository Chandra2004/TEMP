<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_063345_CreateResultsTable
{
    public function up()
    {
        Schema::create('results', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('uid_registration', 36);
            $table->time('waktu_akhir')->nullable();
            $table->unsignedInteger('peringkat')->nullable();
            $table->string('nama_penandatangan')->nullable();
            $table->decimal('score', 8, 2)->nullable();
            $table->timestamps();

            $table->foreign('uid_registration')->references('uid')->on('registrations')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::dropIfExists('results');
    }
}