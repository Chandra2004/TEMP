<?php

namespace Database\Seeders;

use Faker\Factory;
use TheFramework\Database\Seeder;
use TheFramework\Helpers\Helper;
use TheFramework\Models\Category;
use TheFramework\Models\PaymentMethod;
use TheFramework\Models\User;

class Seeder_2026_03_07_071444_EventsSeeder extends Seeder {

    public function run() {
        $faker = Factory::create();
        Seeder::setTable('events');

        // foreach (range(1, 10) as $index) {
        //     $namaEvent = $faker->sentence(3);
        //     $tipeEvent = $faker->randomElement(['berbayar', 'gratis']);
            
        //     $biaya = ($tipeEvent == 'gratis') ? 0 : $faker->numberBetween(50, 500) * 1000;
        
        //     Seeder::create([
        //         'uid'                => Helper::uuid(),
        //         'nama_event'         => $namaEvent,
        //         'deskripsi'          => $faker->paragraph(3),
        //         'lokasi_event'       => $faker->address,
        //         'waktu_event'        => $faker->time('H:i'),
        //         'tanggal_event'      => $faker->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
        //         'biaya_event'        => $biaya,
        //         'status_event'       => $faker->randomElement(['berjalan', 'ditunda', 'ditutup']),
        //         'kuota_peserta'      => $faker->numberBetween(50, 200),
        //         'tipe_event'         => $tipeEvent,
        //         'slug'               => Helper::slugify($namaEvent) . '-' . $faker->unique()->numberBetween(100, 999),
        //         'uid_kategori'       => Category::all()[rand(0, 4)]->uid ?? null,
        //         'uid_author'         => User::all()[rand(0,3)]->uid ?? null,
        //         'uid_payment_method' => PaymentMethod::all()[rand(0,3)]->uid ?? null,
        //     ]);
        // }
    }
}
