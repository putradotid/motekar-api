<?php

namespace Database\Seeders;

use App\Models\ClientPartner;
use App\Models\FeaturedCustomer;
use App\Models\Testimonial;
use App\Models\TestimoniPage;
use Illuminate\Database\Seeder;

class TestimoniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hero
        TestimoniPage::create([
            'title' => 'Pelanggan Kami',
            'description' => 'Beberapa pelanggan yang pernah bekerja sama dengan kami',
        ]);

        // Testimonials
        Testimonial::create([
            'photo' => null,
            'title' => 'PT Motekar Cipta Teknologi memberikan solusi yang benar-benar sesuai dengan kebutuhan bisnis kami.',
            'description' => 'Tim mereka sangat profesional dalam memahami kebutuhan sistem yang kami butuhkan dan mampu menerjemahkannya menjadi platform digital yang efisien dan mudah digunakan. Proses pengerjaan terstruktur, komunikatif, dan hasilnya meningkatkan produktivitas operasional perusahaan kami secara signifikan.',
            'name' => 'Andi Pratama',
            'social_handle' => '@andipratama',
            'order' => 1,
            'is_active' => true,
        ]);

        // Client & Partners (contoh, sesuaikan/upload logo via media library)
        $partners = ['Client A', 'Client B', 'Client C', 'Client D'];
        foreach ($partners as $i => $name) {
            ClientPartner::create([
                'name' => $name,
                'logo_image' => null,
                'order' => $i + 1,
                'is_active' => true,
            ]);
        }

        // Featured Customers (contoh, sesuaikan/upload foto via media library)
        $customers = [
            ['name' => 'John Doe', 'designation' => 'CEO, Tech Corp'],
            ['name' => 'John Doe', 'designation' => 'Manager, StartupX'],
            ['name' => 'Jane Doe', 'designation' => 'Director, DigitalCo'],
            ['name' => 'Jane Doe', 'designation' => 'Founder, InnovateLab'],
        ];

        foreach ($customers as $i => $customer) {
            FeaturedCustomer::create([
                'photo'       => null,
                'name'        => $customer['name'],
                'designation' => $customer['designation'],
                'order'       => $i + 1,
                'is_active'   => true,
            ]);
        }
    }
}
