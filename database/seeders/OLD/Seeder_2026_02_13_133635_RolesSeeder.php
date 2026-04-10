<?php

namespace Database\Seeders;

use Faker\Factory;
use TheFramework\Database\Seeder;
use TheFramework\Helpers\Helper;

class Seeder_2026_02_13_133635_RolesSeeder extends Seeder {

    public function run() {
        $faker = Factory::create();
        Seeder::setTable('roles');

        Seeder::create([
            [
                'uid' => Helper::uuid(),
                'nama_role' => 'admin',
            ],
            [
                'uid' => Helper::uuid(),
                'nama_role' => 'coach',
            ],
            [
                'uid' => Helper::uuid(),
                'nama_role' => 'member',
            ]
        ]);
    }
}
