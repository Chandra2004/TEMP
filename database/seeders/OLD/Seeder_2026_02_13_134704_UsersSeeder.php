<?php

namespace Database\Seeders;

use Faker\Factory;
use TheFramework\Database\Seeder;
use TheFramework\Helpers\Helper;
use TheFramework\Models\Role;

class Seeder_2026_02_13_134704_UsersSeeder extends Seeder {

    public function run() {
        $faker = Factory::create();
        Seeder::setTable('users');

        $users = [
            [
                'uid' => Helper::uuid(),
                'nama_lengkap' => 'admin mada',
                'email' => 'madaakhmad30@gmail.com',
                'password'=> Helper::hash_password('admin123123'),
                'uid_role' => Role::all()[0]['uid'],
            ],
            [
                'uid' => Helper::uuid(),
                'nama_lengkap' => 'Chandra Tri Antomo',
                'email' => 'chandratriantomo123@gmail.com',
                'password'=> Helper::hash_password('chandra123123'),
                'uid_role' => Role::all()[0]['uid'],
            ],
            [
                'uid' => Helper::uuid(),
                'nama_lengkap' => 'pelatih kawandd',
                'email' => 'pelatih@gmail.com',
                'password'=> Helper::hash_password('pelatih123123'),
                'uid_role' => Role::all()[1]['uid'],
            ],
            [
                'uid' => Helper::uuid(),
                'nama_lengkap' => 'member boyy',
                'email' => 'member@gmail.com',
                'password'=> Helper::hash_password('member123123'),
                'uid_role' => Role::all()[2]['uid'],
            ],
            [
                'uid' => Helper::uuid(),
                'nama_lengkap' => 'Farrel Rizna',
                'email' => 'farrelrizna27@gmail.com',
                'password'=> Helper::hash_password('farrel123123'),
                'uid_role' => Role::all()[2]['uid'],
            ]
        ];
        Seeder::create($users);
    }
}
