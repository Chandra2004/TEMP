<?php

namespace Database\Seeders;

use Faker\Factory;
use TheFramework\Database\Seeder;
use TheFramework\Helpers\Helper;

class Seeder_2026_02_15_172354_CategoriesSeeder extends Seeder {

    public function run() {
        $faker = Factory::create();
        Seeder::setTable('categories');

        Seeder::create([
            ['uid' => Helper::uuid(), 'nama_kategori' => 'Internal KSC', 'slug_kategori' => Helper::slugify('Internal KSC')],
            ['uid' => Helper::uuid(), 'nama_kategori' => 'Provinsi', 'slug_kategori' => Helper::slugify('Provinsi')],
            ['uid' => Helper::uuid(), 'nama_kategori' => 'Kota', 'slug_kategori' => Helper::slugify('Kota')],
            ['uid' => Helper::uuid(), 'nama_kategori' => 'Nasional', 'slug_kategori' => Helper::slugify('Nasional')],
            ['uid' => Helper::uuid(), 'nama_kategori' => 'Internasional', 'slug_kategori' => Helper::slugify('Internasional')],
        
            // // Pendidikan
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Seminar', 'slug_kategori' => Helper::slugify('Seminar')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Workshop', 'slug_kategori' => Helper::slugify('Workshop')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Webinar', 'slug_kategori' => Helper::slugify('Webinar')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Pelatihan', 'slug_kategori' => Helper::slugify('Pelatihan')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Bootcamp', 'slug_kategori' => Helper::slugify('Bootcamp')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Kelas Online', 'slug_kategori' => Helper::slugify('Kelas Online')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Kelas Offline', 'slug_kategori' => Helper::slugify('Kelas Offline')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Kuliah Umum', 'slug_kategori' => Helper::slugify('Kuliah Umum')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Sertifikasi', 'slug_kategori' => Helper::slugify('Sertifikasi')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Lomba Akademik', 'slug_kategori' => Helper::slugify('Lomba Akademik')],
        
            // // Teknologi
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Hackathon', 'slug_kategori' => Helper::slugify('Hackathon')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Coding Competition', 'slug_kategori' => Helper::slugify('Coding Competition')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'AI & Machine Learning', 'slug_kategori' => Helper::slugify('AI & Machine Learning')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Cyber Security', 'slug_kategori' => Helper::slugify('Cyber Security')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Web Development', 'slug_kategori' => Helper::slugify('Web Development')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Mobile Development', 'slug_kategori' => Helper::slugify('Mobile Development')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Game Development', 'slug_kategori' => Helper::slugify('Game Development')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'UI/UX Design', 'slug_kategori' => Helper::slugify('UI/UX Design')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Startup Pitching', 'slug_kategori' => Helper::slugify('Startup Pitching')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Tech Talk', 'slug_kategori' => Helper::slugify('Tech Talk')],
        
            // // Bisnis
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Business Talk', 'slug_kategori' => Helper::slugify('Business Talk')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Entrepreneurship', 'slug_kategori' => Helper::slugify('Entrepreneurship')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Digital Marketing', 'slug_kategori' => Helper::slugify('Digital Marketing')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Branding', 'slug_kategori' => Helper::slugify('Branding')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Networking Event', 'slug_kategori' => Helper::slugify('Networking Event')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Job Fair', 'slug_kategori' => Helper::slugify('Job Fair')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Career Expo', 'slug_kategori' => Helper::slugify('Career Expo')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Investor Meeting', 'slug_kategori' => Helper::slugify('Investor Meeting')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'UMKM Expo', 'slug_kategori' => Helper::slugify('UMKM Expo')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Product Launching', 'slug_kategori' => Helper::slugify('Product Launching')],
        
            // // Kreatif
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Festival Musik', 'slug_kategori' => Helper::slugify('Festival Musik')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Konser', 'slug_kategori' => Helper::slugify('Konser')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Pameran Seni', 'slug_kategori' => Helper::slugify('Pameran Seni')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Photography', 'slug_kategori' => Helper::slugify('Photography')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Film Screening', 'slug_kategori' => Helper::slugify('Film Screening')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Teater', 'slug_kategori' => Helper::slugify('Teater')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Stand Up Comedy', 'slug_kategori' => Helper::slugify('Stand Up Comedy')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Fashion Show', 'slug_kategori' => Helper::slugify('Fashion Show')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Bazar', 'slug_kategori' => Helper::slugify('Bazar')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Kuliner', 'slug_kategori' => Helper::slugify('Kuliner')],
        
            // // Olahraga
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Turnamen Futsal', 'slug_kategori' => Helper::slugify('Turnamen Futsal')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Turnamen Badminton', 'slug_kategori' => Helper::slugify('Turnamen Badminton')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Marathon', 'slug_kategori' => Helper::slugify('Marathon')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'E-Sport Tournament', 'slug_kategori' => Helper::slugify('E-Sport Tournament')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Basket Competition', 'slug_kategori' => Helper::slugify('Basket Competition')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Volleyball Competition', 'slug_kategori' => Helper::slugify('Volleyball Competition')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Fun Run', 'slug_kategori' => Helper::slugify('Fun Run')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Yoga Class', 'slug_kategori' => Helper::slugify('Yoga Class')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Fitness Challenge', 'slug_kategori' => Helper::slugify('Fitness Challenge')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Outbound', 'slug_kategori' => Helper::slugify('Outbound')],
        
            // // Sosial & Komunitas
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Bakti Sosial', 'slug_kategori' => Helper::slugify('Bakti Sosial')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Donor Darah', 'slug_kategori' => Helper::slugify('Donor Darah')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Community Gathering', 'slug_kategori' => Helper::slugify('Community Gathering')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Meetup Komunitas', 'slug_kategori' => Helper::slugify('Meetup Komunitas')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Reuni', 'slug_kategori' => Helper::slugify('Reuni')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Charity Event', 'slug_kategori' => Helper::slugify('Charity Event')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Lingkungan Hidup', 'slug_kategori' => Helper::slugify('Lingkungan Hidup')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Keagamaan', 'slug_kategori' => Helper::slugify('Keagamaan')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Kepemudaan', 'slug_kategori' => Helper::slugify('Kepemudaan')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Organisasi Mahasiswa', 'slug_kategori' => Helper::slugify('Organisasi Mahasiswa')],
        
            // // Tambahan untuk genap 100+
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Talkshow', 'slug_kategori' => Helper::slugify('Talkshow')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Diskusi Publik', 'slug_kategori' => Helper::slugify('Diskusi Publik')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Kompetisi Desain', 'slug_kategori' => Helper::slugify('Kompetisi Desain')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Kompetisi Video', 'slug_kategori' => Helper::slugify('Kompetisi Video')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Kompetisi Fotografi', 'slug_kategori' => Helper::slugify('Kompetisi Fotografi')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Open Recruitment', 'slug_kategori' => Helper::slugify('Open Recruitment')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Grand Opening', 'slug_kategori' => Helper::slugify('Grand Opening')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Anniversary', 'slug_kategori' => Helper::slugify('Anniversary')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Awarding Night', 'slug_kategori' => Helper::slugify('Awarding Night')],
            // ['uid' => Helper::uuid(), 'nama_kategori' => 'Press Conference', 'slug_kategori' => Helper::slugify('Press Conference')],
        ]);
    }
}
