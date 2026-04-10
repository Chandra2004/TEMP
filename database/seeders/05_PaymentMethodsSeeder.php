<?php

namespace Database\Seeders;

use TheFramework\Database\Seeder;
use TheFramework\Helpers\Helper;

class PaymentMethodsSeeder extends Seeder
{
    public function run()
    {
        Seeder::setTable('payment_method');

        Seeder::create([
            [
                'uid' => Helper::uuid(),
                'bank' => 'BCA',
                'rekening' => '1234567890',
                'atas_nama' => 'Admin KSC',
                'photo' => null
            ],
            [
                'uid' => Helper::uuid(),
                'bank' => 'Mandiri',
                'rekening' => '0987654321',
                'atas_nama' => 'Bendahara KSC',
                'photo' => null
            ]
        ]);
    }
}
