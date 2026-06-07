<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    // ambil semua setting
    public function index(Request $request)
    {
        $settings = Setting::all()->pluck('value', 'key');
        return response()->json($settings);
    }

    // update setting
    public function update(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $fields = [
            'company_name', 'company_email', 'company_phone',
            'company_address', 'company_whatsapp',
            'office_open', 'office_close',
            'facebook_url', 'instagram_url', 'youtube_url',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                Setting::updateOrCreate(
                    ['key' => $field],
                    ['value' => $request->input($field)]
                );
            }
        }

        // upload logo
        if ($request->hasFile('logo')) {
            $request->validate([
                'logo' => 'image|mimes:jpg,jpeg,png,svg|max:2048',
            ]);

            // Hapus logo lama
            $oldLogo = Setting::where('key', 'logo')->value('value');
            if ($oldLogo && Storage::disk('public')->exists('settings/' . $oldLogo)) {
                Storage::disk('public')->delete('settings/' . $oldLogo);
            }

            $filename = $request->file('logo')->store('settings', 'public');
            Setting::updateOrCreate(
                ['key' => 'logo'],
                ['value' => basename($filename)]
            );
        }

        // activity log
        ActivityLogger::log(
            $user->id,
            'update_setting',
            'setting',
            'Mengupdate pengaturan website',
            []
        );

        return response()->json([
            'message'  => 'Pengaturan berhasil disimpan.',
            'settings' => Setting::all()->pluck('value', 'key'),
        ]);
    }
}
