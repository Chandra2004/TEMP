<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_062432_CreateEventsTable
{
    public function up()
    {
        Schema::create('events', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('slug')->nullable();
            $table->string('nama_event');
            $table->string('banner_event')->nullable();
            $table->string('logo_kiri')->nullable();
            $table->string('logo_kanan')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('lokasi_event')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->time('waktu_mulai')->nullable();
            $table->integer('jumlah_lintasan')->default(8);
            $table->string('status_event')->default('berjalan');
            $table->string('tipe_event', 50)->default('gratis');
            $table->decimal('biaya_event', 12, 2)->default(0.00);
            $table->integer('kuota_peserta')->default(0);
            $table->string('uid_author', 36)->nullable();
            $table->string('uid_payment_method', 36)->nullable();
            $table->timestamps();

            $table->foreign('uid_author')->references('uid')->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('uid_payment_method')->references('uid')->on('payment_method')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}