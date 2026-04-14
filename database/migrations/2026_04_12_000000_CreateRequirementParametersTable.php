<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_12_000000_CreateRequirementParametersTable
{
    public function up()
    {
        Schema::create('requirement_parameters', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('parameter_key', 100)->unique(); // e.g. 'gender', 'birth_year'
            $table->string('display_name', 255); // e.g. 'Jenis Kelamin', 'Tahun Lahir'
            $table->enum('input_type', ['text', 'number', 'select', 'date'])->default('text');
            $table->text('input_options')->nullable(); // JSON: ["Putra", "Putri"]
            $table->text('allowed_operators')->nullable(); // JSON: ["=", "!=", ">", "<"]
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('requirement_parameters');
    }
}
