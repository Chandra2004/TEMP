<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_02_10_065428_CreateEventsTable {
    public function up()
    {
        Schema::create('events', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            
            $table->string('banner_event', 255);
            $table->string('nama_event', 150);
            $table->text('deskripsi');
            $table->string('lokasi_event');
            $table->time('waktu_event');
            $table->date('tanggal_event');

            $table->string('biaya_event')->nullable();
            $table->enum('status_event', ['berjalan', 'ditunda', 'ditutup'])->default('berjalan');
            $table->integer('kuota_peserta')->nullable();
            
            $table->enum('tipe_event', ['berbayar', 'gratis'])->default('gratis');

            $table->string('slug')->unique();

            $table->string('uid_kategori', 36);
            $table->string('uid_author', 36);
            $table->string('uid_payment_method', 36);

            
            $table->timestamps();

            $table->foreign('uid_kategori')->references('uid')->on('categories')->onDelete('cascade');
            $table->foreign('uid_author')->references('uid')->on('users')->onDelete('cascade');
            $table->foreign('uid_payment_method')->references('uid')->on('payment_method')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}