<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_062819_CreateGalleriesTable
{
    public function up()
    {
        Schema::create('galleries', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('uid_event', 36)->nullable();
            $table->string('judul')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('is_active')->default(1);

            $table->timestamps();

            $table->foreign('uid_event')->references('uid')->on('events')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::dropIfExists('galleries');
    }
}