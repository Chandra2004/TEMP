<?php

namespace Database\Seeders;

use TheFramework\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan seeder aplikasi.
     */
    public function run()
    {
        // Di framework ini, SeedCommand menjalankan semua file di folder ini secara berurutan.
        // Jadi tidak perlu dipanggil melalui call() agar tidak terjadi duplikasi.
        /*
        $this->call([
            RolesSeeder::class,
            UsersSeeder::class,
            CategoriesSeeder::class,
        ]);
        */
    }
}
