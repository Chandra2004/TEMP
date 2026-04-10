<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_063320_CreatePaymentsTable
{
    public function up()
    {
        Schema::create('payments', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('uid_registration', 36);
            $table->string('nomor_invoice', 100)->nullable();
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->string('status_pembayaran', 50)->default('pending');
            $table->string('payment_method', 50)->nullable();
            $table->timestamp('tanggal_pembayaran')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();

            $table->foreign('uid_registration')->references('uid')->on('registrations')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}