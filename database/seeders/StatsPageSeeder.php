<?php

namespace Database\Seeders;

use App\Models\StatsPage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatsPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stats = [
            'label_1' => 'Klien Terlayani',
            'value_1' => '190+',
            'label_2' => 'Proyek Selesai',
            'value_2' => '460+',
            'label_3' => 'Tenaga Profesional',
            'value_3' => '230+',
            'label_4' => 'Mitra & Kolaborasi',
            'value_4' => '50+',
        ];

        StatsPage::create($stats);
    }
}
