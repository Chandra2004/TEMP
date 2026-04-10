<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_062944_CreateEventCategoriesTable
{
    public function up()
    {
        Schema::create('event_categories', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('uid_event', 36);
            $table->string('uid_category', 36);
            $table->unsignedInteger('nomor_acara')->nullable();
            $table->string('nama_acara')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->time('waktu_mulai')->nullable();
            $table->string('tipe_biaya')->nullable();
            $table->decimal('biaya_pendaftaran', 10, 2)->default(0.00);
            $table->integer('jumlah_seri')->default(1);
            $table->string('lokasi')->nullable();

            $table->timestamps();

            $table->foreign('uid_event')->references('uid')->on('events')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('uid_category')->references('uid')->on('categories')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_categories');
    }
}