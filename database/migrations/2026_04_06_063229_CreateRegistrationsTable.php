<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_063229_CreateRegistrationsTable
{
    public function up()
    {
        Schema::create('registrations', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('uid_user', 36);
            $table->string('uid_event_category', 36);
            $table->string('seed_time', 50)->nullable();
            $table->timestamp('entry_time')->nullable();
            $table->string('status_pendaftaran', 100)->default('pending');
            $table->string('nomor_pendaftaran', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('uid_user')->references('uid')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('uid_event_category')->references('uid')->on('event_categories')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::dropIfExists('registrations');
    }
}