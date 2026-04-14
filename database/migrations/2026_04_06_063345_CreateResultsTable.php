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
            $table->string('waktu_akhir', 50)->nullable(); // Format MM:SS.ss
            $table->integer('total_milliseconds')->nullable(); // Untuk memudahkan sorting/mencari Best Time
            $table->string('status', 20)->default('FINISH'); // FINISH, NS, DSQ
            $table->unsignedInteger('peringkat')->nullable();
            $table->string('nama_penandatangan', 100)->nullable();
            $table->decimal('score', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('uid_registration')->references('uid')->on('registrations')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::dropIfExists('results');
    }
}