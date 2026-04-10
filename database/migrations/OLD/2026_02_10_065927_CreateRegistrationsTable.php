<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_02_10_065927_CreateRegistrationsTable {
    public function up()
    {
        Schema::create('registrations', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('nomor_pendaftaran', 20)->unique();
            
            $table->string('uid_user', 36);
            $table->string('uid_event', 36);
            
            $table->enum('status', ['menunggu', 'diterima', 'ditolak'])->default('menunggu');
            $table->dateTime('tanggal_registrasi');
            $table->text('catatan_admin')->nullable();
            
            $table->timestamps();
        
            $table->foreign('uid_user')->references('uid')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('uid_event')->references('uid')->on('events')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('registrations');
    }
}