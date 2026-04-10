<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_02_10_053543_CreateUsersTable {    
    public function up()
    {
        Schema::create('users', function ($table) {
            $table->increments('id_user');
            $table->string('uid', 36)->unique();
            
            $table->string('nama_lengkap', 100);
            $table->string('email', 150)->unique();
            $table->string('password', 255);
            
            $table->string('no_telepon', 20);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            
            $table->string('nama_klub', 100)->nullable();
            $table->text('alamat')->nullable();
            
            $table->string('foto_ktp', 255)->nullable();
            $table->string('foto_profil', 255)->nullable();
            
            $table->boolean('is_active')->default(1);
            $table->string('uid_role', 36);
        
            $table->timestamps();
        
            $table->foreign('uid_role')->references('uid')->on('roles')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}