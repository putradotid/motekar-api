<?php

namespace Database\Seeders;

use App\Models\AboutUsPage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AboutUsPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AboutUsPage::create([
            'title' => 'Tentang Kami',
            'description' => 'PT Motekar Cipta Teknologi adalah perusahaan yang bergerak di bidang industri, informasi dan komunikasi, aktivitas profesional, ilmiah dan teknis, serta pendidikan. Kami berkomitmen menjadi perusahaan unggul di bidang teknologi informasi melalui penciptaan inovasi dan pengembangan solusi yang berkelanjutan.',

            'vision' => 'Menjadi perusahaan yang unggul di bidang teknologi informasi melalui berbagai kegiatan usaha yang inovatif, kreatif, dan berkelanjutan.',
            'mission' => 'Memberikan solusi teknologi informasi yang inovatif dan berorientasi kepada kebutuhan pengguna, serta mendukung transformasi digital di berbagai sektor.',
            'visi_misi_image' => null,

            'founder_title' => 'The Founder',
            'founder_description' => 'PT Motekar Cipta Teknologi didirikan dengan visi untuk menghadirkan solusi teknologi yang inovatif dan relevan dengan kebutuhan industri modern. Di balik berdirinya perusahaan ini adalah sosok pemimpin yang memiliki semangat untuk mengembangkan teknologi sebagai alat transformasi dan peningkatan produktivitas di berbagai sektor.',
            'founder_name' => 'John Doe',
            'founder_position' => 'Founder & Chief Executive Officer',
            'founder_image' => null,
        ]);
    }
}
