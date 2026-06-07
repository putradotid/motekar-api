<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'company_name',    'value' => 'PT Motekar Cipta Teknologi'],
            ['key' => 'company_email',   'value' => 'admin@motekar.com'],
            ['key' => 'company_phone',   'value' => '08123456789'],
            ['key' => 'company_address', 'value' => 'Banyumas'],
            ['key' => 'company_whatsapp','value' => '628123456789'],
            ['key' => 'office_open',     'value' => '08:00'],
            ['key' => 'office_close',    'value' => '17:00'],
            ['key' => 'facebook_url',    'value' => ''],
            ['key' => 'instagram_url',   'value' => ''],
            ['key' => 'youtube_url',     'value' => ''],
            ['key' => 'logo',            'value' => 'logo.png'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
