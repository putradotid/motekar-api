<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProdukLayananPage;
use App\Models\ServiceItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdukLayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Section 1 — Hero
        ProdukLayananPage::create([
            'title' => 'Produk Kami',
            'description' => 'Produk kami dirancang untuk menjawab tantangan industri modern melalui solusi digital yang inovatif, aman, dan scalable.',
        ]);

        // Section 2 — Produk
        $products = [
            [
                'title' => 'Software',
                'description' => 'Pengebangan aplikasi perangkat lunak.',
                'detail_description' => 'Kami mengembangkan aplikasi perangkat lunak custom sesuai kebutuhan bisnis, mulai dari aplikasi internal, sistem informasi, hingga aplikasi enterprise dengan teknologi modern dan scalable.',
                'order' => 1,
            ],
            [
                'title' => 'Video Game',
                'description' => 'Pengebangan dan penerbitan permainan digital.',
                'detail_description' => 'Tim kami mengembangkan game untuk berbagai platform, mulai dari konsep, desain, pengembangan, hingga penerbitan ke berbagai marketplace digital.',
                'order' => 2,
            ],
            [
                'title' => 'E-Commerce',
                'description' => 'Pengebangan solusi e-commerce yang fleksibel.',
                'detail_description' => 'Kami membangun platform e-commerce yang fleksibel, mendukung berbagai metode pembayaran, manajemen inventori, dan terintegrasi dengan sistem logistik.',
                'order' => 3,
            ],
            [
                'title' => 'Immersive Media',
                'description' => 'Pemrograman & produksi kontent media imersif.',
                'detail_description' => 'Kami memproduksi konten media immersive seperti AR/VR, 3D interaktif, dan pengalaman digital lainnya untuk berbagai keperluan promosi maupun edukasi.',
                'order' => 4,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product + ['is_active' => true]);
        }

        // Section 3 — Layanan
        $services = [
            [
                'title' => 'Pemrograman Kecerdasan Artifisial',
                'description' => 'Membangun solusi cerdas untuk otomasi dan analisis data.',
                'detail_description' => 'Layanan AI kami mencakup machine learning, computer vision, NLP, dan otomasi proses bisnis untuk meningkatkan efisiensi operasional perusahaan Anda.',
                'order' => 1,
            ],
            [
                'title' => 'Pemrograman dan Konsultasi IoT',
                'description' => 'Konsultasi dan perancangan solusi Internet of Things (IoT).',
                'detail_description' => 'Kami membantu merancang dan mengimplementasikan sistem IoT, mulai dari sensor, konektivitas, dashboard monitoring, hingga integrasi data ke cloud.',
                'order' => 2,
            ],
            [
                'title' => 'Konsultasi Keamanan Informasi',
                'description' => 'Konsultasi untuk perancangan dan keamanan fasilitas komputer.',
                'detail_description' => 'Layanan konsultasi keamanan informasi mencakup audit keamanan, penetration testing, kebijakan keamanan, dan implementasi sistem proteksi data.',
                'order' => 3,
            ],
            [
                'title' => 'Konsultasi Management IT',
                'description' => 'Optimalkan infrastruktur dan management IT dengan konsultasi ahli.',
                'detail_description' => 'Kami membantu perusahaan merancang strategi IT, manajemen infrastruktur, hingga tata kelola TI yang sesuai standar industri.',
                'order' => 4,
            ],
            [
                'title' => 'Portal & Platform Digital',
                'description' => 'Portal Web dan platform untuk kebutuhan komersial.',
                'detail_description' => 'Kami membangun portal web dan platform digital untuk berbagai kebutuhan komersial, termasuk company profile, marketplace, hingga sistem informasi terintegrasi.',
                'order' => 5,
            ],
        ];

        foreach ($services as $service) {
            ServiceItem::create($service + ['is_active' => true]);
        }
    }
}
