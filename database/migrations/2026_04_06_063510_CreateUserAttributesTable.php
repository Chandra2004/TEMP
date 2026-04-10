<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_063510_CreateUserAttributesTable
{
    public function up()
    {
        Schema::create('user_attributes', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('uid_user', 36);
            $table->string('attribute_name', 100); // Nama field (misal: 'ukuran_sepatu')
            $table->string('attribute_type', 50)->default('string'); // string, number, date, boolean, json
            $table->text('attribute_value')->nullable();
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('uid_user')->references('uid')->on('data_users');
            $table->unique(['uid_user', 'attribute_name']);
            $table->index(['attribute_name', 'attribute_type']); // Untuk query cepat
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_attributes');
    }
}