<?php

namespace Database\Seeders;

use Faker\Factory;
use TheFramework\Database\Seeder;
use TheFramework\Helpers\Helper;
use TheFramework\Models\Category;
use TheFramework\Models\PaymentMethod;
use TheFramework\Models\User;
use TheFramework\Models\Event;
use TheFramework\Models\EventCategory;

class EventsSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create('id_ID');

        // Get dependencies
        $admins = User::query()
            ->select(['users.*', 'roles.name as nama_role'])
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', '=', 'admin')
            ->orWhere('roles.name', '=', 'superadmin')
            ->all();
            
        $categories = Category::query()->all();
        $paymentMethods = PaymentMethod::query()->all();

        if (empty($admins) || empty($categories) || empty($paymentMethods)) {
            echo "Admins, Categories, or PaymentMethods are missing. Run seeders 01-05 first.\n";
            return;
        }

        // 1. CREATE SPECIFIC EVENT: Sidoarjo Open Swim 2026
        $eventUid = Helper::uuid();
        $author = $admins[0]; // Use first admin
        $paymentMethod = $paymentMethods[0];

        Event::query()->insert([
            'uid'                => $eventUid,
            'nama_event'         => 'Sidoarjo Open Swim 2026',
            'slug'               => Helper::slugify('Sidoarjo Open Swim 2026'),
            'banner_event'       => 'banner_sidoarjo_open.webp',
            'logo_kiri'          => 'logo_sidoarjo_open.webp',
            'logo_kanan'         => 'logo_akuatik_indonesia.webp',
            'deskripsi'          => '<h3>Sidoarjo Open Swim 2026</h3><p>Kejuaraan renang terbuka se-Jawa Timur.</p>',
            'lokasi_event'       => 'Kolam Renang GOR Sidoarjo',
            'tanggal_mulai'      => '2026-02-08',
            'tanggal_selesai'    => '2026-02-08',
            'waktu_mulai'        => '08:00',
            'jumlah_lintasan'    => 10,
            'status_event'       => 'berjalan',
            'uid_author'         => $author['uid'],
            'uid_payment_method' => $paymentMethod['uid'],
        ]);

        // Insert matches for this specific event
        $acaraCounter = 101;
        foreach ($categories as $category) {
            EventCategory::query()->insert([
                'uid'               => Helper::uuid(),
                'uid_event'         => $eventUid,
                'uid_category'      => $category['uid'],
                'nomor_acara'       => $acaraCounter++,
                'nama_acara'        => $category['nama_kategori'],
                'tipe_biaya'        => 'berbayar',
                'biaya_pendaftaran' => 150000,
                'waktu_mulai'       => '08:00',
                'jumlah_seri'       => 2,
            ]);
        }

        // 2. CREATE RANDOM EVENTS
        foreach (range(1, 2) as $index) {
            $namaEvent = $faker->sentence(3);
            $eventUid = Helper::uuid();
            $author = $admins[array_rand($admins)];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

            $startDate = $faker->dateTimeBetween('now', '+2 months');
            $endDate = clone $startDate;
            $endDate->modify('+' . rand(1, 3) . ' days');

            Event::query()->insert([
                'uid'                => $eventUid,
                'nama_event'         => $namaEvent,
                'slug'               => Helper::slugify($namaEvent) . '-' . rand(100, 999),
                'banner_event'       => null,
                'deskripsi'          => '<h3>Tentang Event</h3><p>' . $faker->paragraph(5) . '</p>',
                'lokasi_event'       => $faker->company . ', ' . $faker->city,
                'tanggal_mulai'      => $startDate->format('Y-m-d'),
                'tanggal_selesai'    => $endDate->format('Y-m-d'),
                'waktu_mulai'        => $faker->time('H:i'),
                'jumlah_lintasan'    => $faker->randomElement([6, 8, 10]),
                'status_event'       => $faker->randomElement(['berjalan', 'ditunda']),
                'uid_author'         => $author['uid'],
                'uid_payment_method' => $paymentMethod['uid'],
            ]);

            $numMatches = rand(2, 4);
            $eventCategories = array_rand($categories, $numMatches);
            if (!is_array($eventCategories)) $eventCategories = [$eventCategories];

            $acaraCounterInner = 201;
            foreach ($eventCategories as $catIdx) {
                $category = $categories[$catIdx];
                EventCategory::query()->insert([
                    'uid'               => Helper::uuid(),
                    'uid_event'         => $eventUid,
                    'uid_category'      => $category['uid'],
                    'nomor_acara'       => $acaraCounterInner++,
                    'nama_acara'        => $category['nama_kategori'],
                    'tipe_biaya'        => 'gratis',
                    'biaya_pendaftaran' => 0,
                    'waktu_mulai'       => $faker->time('H:i'),
                    'jumlah_seri'       => $faker->numberBetween(1, 3),
                ]);
            }
        }
    }
}
