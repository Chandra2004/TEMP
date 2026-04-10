<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_062522_CreateSocialMediasTable
{
    public function up()
    {
        Schema::create('social_medias', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('uid_user', 36);
            $table->string('platform', 100);
            $table->string('link_sosial_media');
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(1);

            $table->timestamps();

            $table->foreign('uid_user')->references('uid')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::dropIfExists('social_medias');
    }
}