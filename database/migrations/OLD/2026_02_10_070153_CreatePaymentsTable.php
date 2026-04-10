<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_02_10_070153_CreatePaymentsTable {
    public function up()
    {
        Schema::create('payments', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('uid_registration', 36);
            
            $table->decimal('total_bayar', 10, 2);
            $table->string('metode_pembayaran', 100);
            $table->enum('status_pembayaran', ['menunggu', 'diterima', 'ditolak'])->default('menunggu');
            
            $table->timestamp('tanggal_pembayaran');
            $table->string('bukti_pembayaran', 255);
            
            $table->timestamps();
        
            $table->foreign('uid_registration')->references('uid')->on('registrations')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}