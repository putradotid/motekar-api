<?php

namespace Database\Seeders;

use App\Models\TimKamiPage;
use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TimKamiSeeder extends Seeder
{
    public function run(): void
    {
        // Hero + Section 2
        TimKamiPage::create([
            'title'          => 'Tim Kami',
            'description'    => 'PT Motekar Cipta Teknologi adalah perusahaan yang bergerak di bidang industri, informasi dan komunikasi, aktivitas profesional, ilmiah dan teknis, serta pendidikan.',
            'section2_label' => 'Meet Our Team',
        ]);

        // The Leader
        $leaders = [
            ['name' => 'John Doe', 'designation' => 'Chief Executive Officer', 'division' => 'leader', 'order' => 1],
            ['name' => 'Jane Doe', 'designation' => 'Chief Technology Officer', 'division' => 'leader', 'order' => 2],
        ];

        // Client Support
        $clientSupports = [
            ['name' => 'John Doe', 'designation' => 'Client Support Manager', 'division' => 'client_support', 'order' => 1],
            ['name' => 'Jane Doe', 'designation' => 'Client Relations', 'division' => 'client_support', 'order' => 2],
        ];

        // Developer
        $developers = [
            ['name' => 'John Doe', 'designation' => 'Backend Developer', 'division' => 'developer', 'order' => 1],
            ['name' => 'Jane Doe', 'designation' => 'Frontend Developer', 'division' => 'developer', 'order' => 2],
        ];

        foreach (array_merge($leaders, $clientSupports, $developers) as $member) {
            TeamMember::create($member + ['photo' => null, 'is_active' => true]);
        }
    }
}