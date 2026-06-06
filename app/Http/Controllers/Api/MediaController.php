<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    private function checkAdmin(Request $request)
    {
        $user = $request->attributes->get('user');

        if (!$user || $user->role !== 'admin') {
            return null;
        }
        return $user;
    }

    // list media with filter by category
    public function index(Request $request)
    {
        $admin = $this->checkAdmin($request);
        if (!$admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $category = $request->get('category', '');
        $perPage  = $request->get('per_page', 15);

        $media = Media::when($category, function ($query) use ($category) {
            $query->where('category', $category);
        })->latest()->paginate($perPage);

        return response()->json($media);
    }

    // upload media
    public function store(Request $request)
    {
        $admin = $this->checkAdmin($request);
        if (!$admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,webp,svg|max:3072',
            'category' => 'required|in:image,icon,team,clients,background',
        ]);

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $path     = $file->store('media/' . $request->category, 'public');
        $url      = Storage::url($path);

        $media = Media::create([
            'filename' => $filename,
            'path' => $path,
            'url' => $url,
            'category' => $request->category,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => $admin->id,
        ]);

        ActivityLogger::log(
            $admin->id,
            'upload_media',
            'media',
            'Mengupload media: ' . $media->filename . ' (kategori: ' . $media->category . ')',
            ['media_id' => $media->id]
        );

        return response()->json([
            'message' => 'Media berhasil diupload.',
            'data'    => $media,
        ], 201);
    }

    // Lihat gambar detail
    public function show(Request $request, int $id)
    {
        $admin = $this->checkAdmin($request);
        if (!$admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $media = Media::findOrFail($id);

        return response()->json($media);
    }

    // delete media
    public function destroy(Request $request, int $id)
    {
        $admin = $this->checkAdmin($request);
        if (!$admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $media = Media::findOrFail($id);

        // hapus file dari storage
        Storage::disk('public')->delete($media->path);

        // hapus record dari database
        $media->delete();

        ActivityLogger::log(
            $admin->id,
            'delete_media',
            'media',
            'Menghapus media: ' . $media->filename . ' (kategori: ' . $media->category . ')',
            ['media_id' => $media->id]
        );
        
        return response()->json(['message' => 'Media berhasil dihapus.']);
    }
}
