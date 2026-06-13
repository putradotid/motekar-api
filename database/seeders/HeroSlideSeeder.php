<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slides = [
            [
                'title'            => 'Where Innovation Meets Execution',
                'description'      => 'Kami membantu perusahaan dan UMKM bertransformasi secara digital melalui pengembangan software, sistem terintegrasi, dan solusi teknologi yang scalable.',
                'button_text'      => 'Hubungi Kami',
                'button_url'       => '/hubungi-kami',
                'icon_url'         => null,
                'background_type'  => 'color',
                'background_value' => '#D1D5DB',
                'order'            => 1,
                'is_active'        => true,
            ],
            [
                'title'            => 'Solusi Digital untuk Bisnis Anda',
                'description'      => 'Platform digital yang scalable dan terintegrasi untuk mempercepat pertumbuhan bisnis di era digital.',
                'button_text'      => 'Lihat Layanan',
                'button_url'       => '/produk',
                'icon_url'         => null,
                'background_type'  => 'color',
                'background_value' => '#FEF3C7',
                'order'            => 2,
                'is_active'        => true,
            ],
            [
                'title'            => 'Mitra Strategis Transformasi Digital',
                'description'      => 'Bersama kami, wujudkan visi digital perusahaan Anda dengan teknologi terkini dan tim profesional berpengalaman.',
                'button_text'      => 'Jadwalkan Meeting',
                'button_url'       => '/login',
                'icon_url'         => null,
                'background_type'  => 'color',
                'background_value' => '#DBEAFE',
                'order'            => 3,
                'is_active'        => true,
            ],
        ];

        foreach ($slides as $slide) {
            HeroSlide::create($slide);
        }
    }
}
