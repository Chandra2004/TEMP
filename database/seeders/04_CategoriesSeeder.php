<?php

namespace Database\Seeders;

use TheFramework\Database\Seeder;
use TheFramework\Helpers\Helper;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        Seeder::setTable('categories');

        $categories = [
            ['nama' => '50M KICKING BEBAS PUTRA', 'ku' => 'KU 2017'],
            ['nama' => '50M KICKING BEBAS PUTRI', 'ku' => 'KU 2017'],
            ['nama' => '100M GAYA GANTI PERORANGAN PUTRA', 'ku' => 'KU 1'],
            ['nama' => '100M GAYA GANTI PERORANGAN PUTRI', 'ku' => 'KU 1'],
            ['nama' => '50M GAYA BEBAS PUTRA', 'ku' => 'KU 2'],
            ['nama' => '50M GAYA BEBAS PUTRI', 'ku' => 'KU 2'],
            ['nama' => '100M GAYA DADA PUTRA', 'ku' => 'KU 3'],
            ['nama' => '100M GAYA DADA PUTRI', 'ku' => 'KU 3'],
        ];

        foreach ($categories as $cat) {
            Seeder::create([
                'uid'           => Helper::uuid(),
                'nama_kategori' => $cat['nama'],
                'kode_ku'       => $cat['ku'],
                'slug_kategori' => Helper::slugify($cat['nama'] . '-' . $cat['ku']),
            ]);
        }
    }
}
