<?php

namespace Database\Seeders;

use TheFramework\Database\Seeder;
use TheFramework\Helpers\Helper;

class RolesSeeder extends Seeder
{
    public function run()
    {
        Seeder::setTable('roles');

        Seeder::create([
            [
                'uid' => Helper::uuid(),
                'name' => 'superadmin',
                'guard_name' => 'web',
                'created_at' => Helper::updateAt(),
                'updated_at' => Helper::updateAt(),
            ],
            [
                'uid' => Helper::uuid(),
                'name' => 'admin',
                'guard_name' => 'web',
                'created_at' => Helper::updateAt(),
                'updated_at' => Helper::updateAt(),
            ],
            [
                'uid' => Helper::uuid(),
                'name' => 'pelatih',
                'guard_name' => 'web',
                'created_at' => Helper::updateAt(),
                'updated_at' => Helper::updateAt(),
            ],
            [
                'uid' => Helper::uuid(),
                'name' => 'atlet',
                'guard_name' => 'web',
                'created_at' => Helper::updateAt(),
                'updated_at' => Helper::updateAt(),
            ]
        ]);
    }
}
