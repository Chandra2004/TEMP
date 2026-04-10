<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_02_10_055654_CreateNotificationsTable {
    public function up()
    {
        Schema::create('notifications', function ($table) {
            $table->increments('id_notification');
            $table->string('uid', 36)->unique();
            $table->string('uid_user', 36);
            $table->string('judul');
            $table->text('pesan');
            $table->boolean('is_read');
            
            $table->timestamps();

            $table->foreign('uid_user')->references('uid')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}