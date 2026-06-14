<?php

namespace Database\Seeders;

use App\Models\CallToAction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CallToActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cta = [
            'title'       => 'Anda Berminat?',
            'description' => 'Mari ciptakan produk yang menjadi solusi untuk negeri bersama kami.',
            'button_text' => 'Hubungi Kami',
            'button_url'  => '/hubungi-kami',
        ];

        CallToAction::create($cta);
    }
}
