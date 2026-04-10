<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_062459_CreateDataUsersTable
{
    public function up()
    {
        Schema::create('data_users', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('uid_user', 36);

            // Data Pribadi
            $table->string('nama_lengkap');
            $table->string('nama_panggilan')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->string('jenis_kelamin', 20)->nullable();
            $table->text('alamat_lengkap')->nullable();
            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('no_telepon_darurat')->nullable();
            $table->string('email')->nullable();

            // Data Identitas
            $table->string('nomor_ktp', 20)->nullable();
            $table->string('nomor_kk', 20)->nullable();
            $table->string('foto_profil')->nullable();
            $table->string('foto_ktp')->nullable();
            $table->string('foto_akta')->nullable();

            // Data Fisik & Kesehatan
            $table->decimal('tinggi_badan', 5, 2)->nullable(); // cm
            $table->decimal('berat_badan', 5, 2)->nullable(); // kg
            $table->string('golongan_darah', 5)->nullable();
            $table->text('riwayat_penyakit')->nullable();
            $table->text('alergi')->nullable();
            $table->text('obat_rutin')->nullable();
            $table->boolean('vaksin_covid')->default(0);

            // Data Olahraga/Renang
            $table->integer('pengalaman_renang')->nullable(); // tahun
            $table->string('tingkat_keahlian', 50)->nullable(); // pemula, menengah, mahir
            $table->text('prestasi_renang')->nullable();
            $table->string('klub_renang')->nullable();
            $table->string('pelatih_renang')->nullable();

            // Data Keluarga & Pendidikan
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('sekolah')->nullable();
            $table->string('kelas')->nullable();

            // Data Klub & Status
            $table->string('uid_klub', 36)->nullable();
            $table->string('jabatan_klub')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamp('tanggal_bergabung')->nullable();

            $table->timestamps();

            $table->foreign('uid_user')->references('uid')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('uid_klub')->references('uid')->on('clubs')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::dropIfExists('data_users');
    }
}