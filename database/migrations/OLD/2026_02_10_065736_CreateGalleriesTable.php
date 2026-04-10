<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_02_10_065736_CreateGalleriesTable {
    public function up()
    {
        Schema::create('galleries', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('uid_event', 36);
            $table->string('foto_event', 255);
            
            $table->timestamps();

            $table->foreign('uid_event')->references('uid')->on('events')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('galleries');
    }
}