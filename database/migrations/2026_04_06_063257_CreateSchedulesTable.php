<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_063257_CreateSchedulesTable
{
    public function up()
    {
        Schema::create('schedules', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('uid_registration', 36);
            $table->unsignedInteger('nomor_seri')->nullable();
            $table->unsignedInteger('nomor_lintasan')->nullable();
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->timestamps();

            $table->foreign('uid_registration')->references('uid')->on('registrations')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}
