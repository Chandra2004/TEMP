<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_062622_CreateNotificationsTable
{
    public function up()
    {
        Schema::create('notifications', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('uid_user', 36);
            $table->string('judul');
            $table->text('pesan');
            $table->boolean('is_read')->default(0);
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->foreign('uid_user')->references('uid')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}