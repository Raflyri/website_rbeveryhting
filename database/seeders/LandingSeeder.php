<?php

namespace Database\Seeders;

use App\Models\LandingSetting;
use Illuminate\Database\Seeder;

class LandingSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan tabel kosong dulu biar gak dobel
        LandingSetting::truncate();

        LandingSetting::create([
            // Input JSON untuk Multi-bahasa
            'hero_title' => [
                'en_US' => 'COMING SOON',
                'en_GB' => 'COMING SOON',
                'id'    => 'SEGERA HADIR',
                'ms'    => 'AKAN DATANG',
                'ja'    => '近日公開',
            ],

            'vision_desc' => [
                'en_US' => 'We are preparing the best platform for IT Consulting, Training, and Development needs.',
                'en_GB' => 'We are preparing the best platform for IT Consulting, Training, and Development needs.',
                'id'    => 'Kami sedang menyiapkan platform terbaik untuk kebutuhan IT Consulting, Training, dan Development.',
                'ms'    => 'Kami sedang menyediakan platform terbaik untuk keperluan Perundingan IT, Latihan, dan Pembangunan.',
                'ja'    => 'ITコンサルティング、トレーニング、開発のニーズに応える最高のプラットフォームを準備しています。',
            ],

            // Masukkan nama file jika file fisiknya sudah ada di storage
            // Contoh: 'landing-assets/01KEE9SKAGAZPPT70J27KE35GX.mp4'
            // Jika tidak ingat, biarkan null dan upload ulang via dashboard.
            'hero_video' => 'landing-assets/01KEEDMVDE53E9XKV696PNSAVJ.mp4',
            'hero_image' => 'landing-assets/01KEEDMVD553GTYGMJ0TPVPTQS.png',
        ]);
    }
}
