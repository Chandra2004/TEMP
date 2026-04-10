<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_062444_CreateSettingsTable
{
    public function up()
    {
        Schema::create('settings', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('setting_key', 100)->unique();
            $table->string('setting_value', 255)->nullable();
            $table->string('group', 100)->nullable();
            $table->string('type', 50)->default('string');
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
}