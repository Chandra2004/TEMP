<?php

namespace Database\Seeders;

use TheFramework\Database\Seeder;
use TheFramework\Helpers\Helper;
use TheFramework\App\Database;
use TheFramework\App\Schema;
use TheFramework\Models\User;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $db = Database::getInstance();

        // 1. Ambil Role IDs
        $db->query("SELECT id FROM roles WHERE name = 'superadmin'");
        $superadminRole = $db->single();

        $db->query("SELECT id FROM roles WHERE name = 'admin'");
        $adminRole = $db->single();

        $db->query("SELECT id FROM roles WHERE name = 'pelatih'");
        $pelatihRole = $db->single();

        $db->query("SELECT id FROM roles WHERE name = 'atlet'");
        $atletRole = $db->single();

        // 2. Buat User Superadmin
        $superUid = Helper::uuid();
        $superId = User::query()->insertGetId([
            'uid' => $superUid,
            'username' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => Helper::hash_password('superadmin123'),
            'is_active' => 1,
            'created_at' => Helper::updateAt(),
            'updated_at' => Helper::updateAt(),
        ]);

        Schema::insert('data_users', [[
            'uid' => Helper::uuid(),
            'uid_user' => $superUid,
            'nama_lengkap' => 'Super Administrator',
            'no_telepon' => '080000000001',
            'no_telepon_darurat' => '080000000000',
            'tempat_lahir' => 'Sidoarjo',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'alamat_lengkap' => 'Jl. Pahlawan No. 1, Sidoarjo, Jawa Timur',
            'kota' => 'Sidoarjo',
            'provinsi' => 'Jawa Timur',
            'tinggi_badan' => 175.5,
            'berat_badan' => 70.0,
            'klub_renang' => 'KHAFID SWIMMING CLUB',
            'is_active' => 1,
            'created_at' => Helper::updateAt(),
            'updated_at' => Helper::updateAt(),
        ]]);

        Schema::insert('model_has_roles', [[
            'role_id' => $superadminRole['id'],
            'model_type' => 'User',
            'model_id' => $superId,
        ]]);

        // 3. Buat User Admin
        $adminUid = Helper::uuid();
        $adminId = User::query()->insertGetId([
            'uid' => $adminUid,
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Helper::hash_password('admin123'),
            'is_active' => 1,
            'created_at' => Helper::updateAt(),
            'updated_at' => Helper::updateAt(),
        ]);

        Schema::insert('data_users', [[
            'uid' => Helper::uuid(),
            'uid_user' => $adminUid,
            'nama_lengkap' => 'Admin Pengelola',
            'no_telepon' => '080000000002',
            'tempat_lahir' => 'Sidoarjo',
            'tanggal_lahir' => '1995-05-15',
            'alamat_lengkap' => 'Perumahan Delta Sari Indah, Sidoarjo',
            'is_active' => 1,
            'created_at' => Helper::updateAt(),
            'updated_at' => Helper::updateAt(),
        ]]);

        Schema::insert('model_has_roles', [[
            'role_id' => $adminRole['id'],
            'model_type' => 'User',
            'model_id' => $adminId,
        ]]);

        // 4. Buat User Pelatih
        $pelatihUid = Helper::uuid();
        $pelatihId = User::query()->insertGetId([
            'uid' => $pelatihUid,
            'username' => 'pelatih',
            'email' => 'pelatih@gmail.com',
            'password' => Helper::hash_password('pelatih123'),
            'is_active' => 1,
            'created_at' => Helper::updateAt(),
            'updated_at' => Helper::updateAt(),
        ]);

        Schema::insert('data_users', [[
            'uid' => Helper::uuid(),
            'uid_user' => $pelatihUid,
            'nama_lengkap' => 'Pelatih Renang',
            'no_telepon' => '080000000003',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '1985-08-20',
            'alamat_lengkap' => 'Jl. Manyar Kertoarjo No. 10, Surabaya',
            'tingkat_keahlian' => 'Mahir',
            'pengalaman_renang' => 10,
            'is_active' => 1,
            'created_at' => Helper::updateAt(),
            'updated_at' => Helper::updateAt(),
        ]]);

        Schema::insert('model_has_roles', [[
            'role_id' => $pelatihRole['id'],
            'model_type' => 'User',
            'model_id' => $pelatihId,
        ]]);

        // 5. Buat User Atlet
        $atletUid = Helper::uuid();
        $atletId = User::query()->insertGetId([
            'uid' => $atletUid,
            'username' => 'atlet',
            'email' => 'atlet@gmail.com',
            'password' => Helper::hash_password('atlet123'),
            'is_active' => 1,
            'created_at' => Helper::updateAt(),
            'updated_at' => Helper::updateAt(),
        ]);

        Schema::insert('data_users', [[
            'uid' => Helper::uuid(),
            'uid_user' => $atletUid,
            'nama_lengkap' => 'Atlet Berprestasi',
            'nama_panggilan' => 'Ahmad',
            'no_telepon' => '080000000004',
            'no_telepon_darurat' => '082222222222',
            'email' => 'atlet@gmail.com',
            'tempat_lahir' => 'Sidoarjo',
            'tanggal_lahir' => '2010-03-25',
            'jenis_kelamin' => 'L',
            'alamat_lengkap' => 'Jl. Gajah Mada No. 50, Sidoarjo',
            'kota' => 'Sidoarjo',
            'provinsi' => 'Jawa Timur',
            'kode_pos' => '61211',
            'nomor_ktp' => '3515000000000001',
            'nomor_kk' => '3515000000000002',
            'foto_profil' => 'user_DATA-DESIGN_69d8f609ca9267.98341524.webp',
            'foto_ktp' => 'card_DATA-DESIGN_69d8f60a3222c3.50568020.webp',
            'foto_akta' => 'akta_DATA-DESIGN_69d8f60a744976.26006874.webp',
            'tinggi_badan' => 160.0,
            'berat_badan' => 50.0,
            'golongan_darah' => 'O',
            'riwayat_penyakit' => 'Tidak ada',
            'alergi' => 'Udang',
            'obat_rutin' => 'Vitamin C',
            'vaksin_covid' => 1,
            'pengalaman_renang' => 5,
            'tingkat_keahlian' => 'Mahir',
            'prestasi_renang' => 'Juara 1 Kejurda Jatim 2023',
            'klub_renang' => 'KHAFID SWIMMING CLUB',
            'pelatih_renang' => 'Coach Chandra',
            'nama_ayah' => 'Budi Sudarsono',
            'nama_ibu' => 'Siti Aminah',
            'pekerjaan_ayah' => 'Swasta',
            'pekerjaan_ibu' => 'IRT',
            'sekolah' => 'SMPN 1 Sidoarjo',
            'kelas' => '8-A',
            'uid_klub' => null,
            'jabatan_klub' => 'Anggota Utama',
            'is_active' => 1,
            'tanggal_bergabung' => Helper::updateAt(),
            'created_at' => Helper::updateAt(),
            'updated_at' => Helper::updateAt(),
        ]]);

        Schema::insert('model_has_roles', [[
            'role_id' => $atletRole['id'],
            'model_type' => 'User',
            'model_id' => $atletId,
        ]]);
    }
}
