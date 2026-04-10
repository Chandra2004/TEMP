<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_063409_CreateLogActivityTable
{
    public function up()
    {
        Schema::create('log_activity', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('uid_user', 36);
            $table->string('aktivitas', 255);
            $table->ipAddress('ip_address');
            $table->timestamp('activity_at')->useCurrent();

            $table->timestamps();

            $table->foreign('uid_user')->references('uid')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_activity');
    }
}