<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;

class HeroSlideController extends Controller
{
    public function index()
    {
        $slides = HeroSlide::where('is_active', true)
            ->orderBy('order')
            ->get();

        return response()->json($slides);
    }

    // Admin — semua slide
    public function adminIndex(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json(HeroSlide::orderBy('order')->get());
    }

    // Admin — create
    public function store(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'button_text'      => 'nullable|string|max:100',
            'button_url'       => 'nullable|string|max:255',
            'icon_url'         => 'nullable|string',
            'background_type'  => 'required|in:color,image',
            'background_value' => 'required|string',
            'order'            => 'nullable|integer',
            'is_active'        => 'nullable|boolean',
        ]);

        $slide = HeroSlide::create($request->all());

        ActivityLogger::log(
            $user->id,
            'create_hero_slide',
            'website',
            'Menambahkan hero slide: ' . $slide->title,
            ['slide_id' => $slide->id]
        );

        return response()->json([
            'message' => 'Slide berhasil ditambahkan.',
            'data'    => $slide,
        ], 201);
    }

    // Admin — update
    public function update(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $slide = HeroSlide::findOrFail($id);
        $slide->update($request->all());

        ActivityLogger::log(
            $user->id,
            'update_hero_slide',
            'website',
            'Mengupdate hero slide: ' . $slide->title,
            ['slide_id' => $slide->id]
        );

        return response()->json([
            'message' => 'Slide berhasil diupdate.',
            'data'    => $slide,
        ]);
    }

    // Admin — delete
    public function destroy(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $slide = HeroSlide::findOrFail($id);
        $title = $slide->title;
        $slide->delete();

        ActivityLogger::log(
            $user->id,
            'delete_hero_slide',
            'website',
            'Menghapus hero slide: ' . $title,
            ['slide_id' => $id]
        );

        return response()->json(['message' => 'Slide berhasil dihapus.']);
    }
}
