<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HomepageServiceSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\HomepageServiceSection::create([
            'title'       => 'Solusi & Layanan Kami',
            'description' => 'Kami menyediakan layanan pengembangan dan konsultasi teknologi yang dirancang untuk menjawab kebutuhan industri modern.',
            'image_1'     => null,
            'image_2'     => null,
            'image_3'     => null,
            'image_4'     => null,
        ]);
    }
}
