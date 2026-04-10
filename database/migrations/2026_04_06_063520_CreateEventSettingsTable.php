<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_063520_CreateEventSettingsTable
{
    public function up()
    {
        Schema::create('event_settings', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('uid_event', 36)->nullable(); // Null = global, ada value = per-event
            $table->string('setting_key', 100);
            $table->string('setting_type', 50)->default('string');
            $table->text('setting_value')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_editable')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('uid_event')->references('uid')->on('events');
            $table->unique(['uid_event', 'setting_key']);
            $table->index(['setting_key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_settings');
    }
}