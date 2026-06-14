<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name'        => 'Software Development',
                'description' => 'Pengembangan aplikasi perangkat lunak untuk kebutuhan bisnis dengan teknologi terkini.',
                'icon_url'    => null,
                'order'       => 1,
                'is_active'   => true,
            ],
            [
                'name'        => 'Video Game Development',
                'description' => 'Pengembangan dan penerbitaan permainan digital yang inovatif dan engaging.',
                'icon_url'    => null,
                'order'       => 2,
                'is_active'   => true,
            ],
            [
                'name'        => 'E-Commerce Solutions',
                'description' => 'Pengembangan solusi e-commerce yang fleksibel untuk kemudahan transaksi online.',
                'icon_url'    => null,
                'order'       => 3,
                'is_active'   => true,
            ],
            [
                'name'        => 'Immersive Media',
                'description' => 'Pemrograman & produksi konten media immersive untuk pengalaman interaktif.',
                'icon_url'    => null,
                'order'       => 4,
                'is_active'   => true,
            ],
            [
                'name'        => 'AI & Machine Learning',
                'description' => 'Membangun solusi cerdas untuk otomasi dan analisis data.',
                'icon_url'    => null,
                'order'       => 5,
                'is_active'   => true,
            ],
            [
                'name'        => 'IoT Consulting',
                'description' => 'Konsultasi dan perencanaan solusi Internet of Things (IoT).',
                'icon_url'    => null,
                'order'       => 6,
                'is_active'   => true,
            ],
            [
                'name'        => 'IT Management Consulting',
                'description' => 'Optimalkan infrastruktur dan manajemen IT dengan konsultasi ahli.',
                'icon_url'    => null,
                'order'       => 7,
                'is_active'   => true,
            ],
            [
                'name'        => 'Digital Portal & Platform',
                'description' => 'Portal Web dan platform untuk kebutuhan komersial.',
                'icon_url'    => null,
                'order'       => 8,
                'is_active'   => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
