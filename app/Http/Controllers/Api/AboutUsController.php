<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\AboutUsPage;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
    // Public — untuk halaman /tentang-kami
    public function show()
    {
        return response()->json(AboutUsPage::first());
    }

    // Admin — untuk form edit
    public function index(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json(AboutUsPage::first());
    }

    // Admin — create (kalau belum ada record)
    public function store(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'vision'              => 'nullable|string',
            'mission'             => 'nullable|string',
            'visi_misi_image'     => 'nullable|string',
            'founder_title'       => 'nullable|string|max:255',
            'founder_description' => 'nullable|string',
            'founder_name'        => 'nullable|string|max:255',
            'founder_position'    => 'nullable|string|max:255',
            'founder_image'       => 'nullable|string',
        ]);

        $data = $request->only([
            'title', 'description', 'vision', 'mission', 'visi_misi_image',
            'founder_title', 'founder_description', 'founder_name',
            'founder_position', 'founder_image',
        ]);

        $page = AboutUsPage::create($data);

        ActivityLogger::log($user->id, 'create_about_us_page', 'website', 'Membuat halaman Tentang Kami');

        return response()->json(['message' => 'Halaman Tentang Kami berhasil disimpan.', 'data' => $page], 201);
    }

    // Admin — update
    public function update(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'vision'              => 'nullable|string',
            'mission'             => 'nullable|string',
            'visi_misi_image'     => 'nullable|string',
            'founder_title'       => 'nullable|string|max:255',
            'founder_description' => 'nullable|string',
            'founder_name'        => 'nullable|string|max:255',
            'founder_position'    => 'nullable|string|max:255',
            'founder_image'       => 'nullable|string',
        ]);

        $page = AboutUsPage::findOrFail($id);

        $data = $request->only([
            'title', 'description', 'vision', 'mission', 'visi_misi_image',
            'founder_title', 'founder_description', 'founder_name',
            'founder_position', 'founder_image',
        ]);

        $page->update($data);
        $page->refresh();

        ActivityLogger::log($user->id, 'update_about_us_page', 'website', 'Mengupdate halaman Tentang Kami');

        return response()->json(['message' => 'Halaman Tentang Kami berhasil diupdate.', 'data' => $page]);
    }
}