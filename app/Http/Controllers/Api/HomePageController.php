<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\AboutPage;
use App\Models\StatsPage;
use App\Models\Service;
use App\Models\CallToAction;
use App\Models\ClientPartner;
use App\Models\MeetingRequests;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    // Public — semua data homepage
    public function show()
    {
        return response()->json([
            'hero'     => HeroSlide::where('is_active', true)->orderBy('order')->get(),
        'about'    => AboutPage::first(),
        'stats'    => [
            [
                'label' => 'Request Meeting',
                'value' => MeetingRequests::count(),
            ],
            [
                'label' => 'Meeting Selesai',
                'value' => MeetingRequests::where('status', 'done')->count(),
            ],
            [
                'label' => 'Tenaga Profesional',
                'value' => TeamMember::where('is_active', true)->count(),
            ],
            [
                'label' => 'Mitra Kolaborasi',
                'value' => ClientPartner::where('is_active', true)->count(),
            ],
        ],
        'services' => Service::where('is_active', true)->orderBy('order')->take(4)->get(),
        'cta'      => CallToAction::first(),
        ]);
    }

    // Admin — semua data untuk edit
    public function index(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json([
            'hero'     => HeroSlide::orderBy('order')->get(),
            'about'    => AboutPage::first(),
            'stats'    => [
                [
                    'label' => 'Request Meeting',
                    'value' => MeetingRequests::count(),
                ],
                [
                    'label' => 'Meeting Selesai',
                    'value' => MeetingRequests::where('status', 'done')->count(),
                ],
                [
                    'label' => 'Tenaga Profesional',
                    'value' => TeamMember::where('is_active', true)->count(),
                ],
                [
                    'label' => 'Mitra Kolaborasi',
                    'value' => ClientPartner::where('is_active', true)->count(),
                ],
            ],
            'services' => Service::orderBy('order')->take(4)->get(),
            'cta'      => CallToAction::first(),
        ]);
    }

    // ==================== HERO ====================
    public function storeHero(Request $request)
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
    ]);

    $data = $request->only([
        'title', 'description', 'button_text', 'button_url',
        'icon_url', 'background_type', 'background_value', 'order',
    ]);
    $data['is_active'] = true;

    $slide = HeroSlide::create($data);

    ActivityLogger::log($user->id, 'create_hero_slide', 'website', 'Menambahkan hero slide', ['slide_id' => $slide->id]);

    return response()->json(['message' => 'Hero berhasil ditambahkan.', 'data' => $slide], 201);
    }

    public function updateHero(Request $request, int $id)
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
    ]);

    $slide = HeroSlide::findOrFail($id);

    $data = $request->only([
        'title', 'description', 'button_text', 'button_url',
        'icon_url', 'background_type', 'background_value',
    ]);
    $data['is_active'] = $request->boolean('is_active');

    $slide->update($data);
    $slide->refresh();

    ActivityLogger::log($user->id, 'update_hero_slide', 'website', 'Mengupdate hero slide', ['slide_id' => $id]);

    return response()->json(['message' => 'Hero berhasil diupdate.', 'data' => $slide]);
    }

    public function destroyHero(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        HeroSlide::findOrFail($id)->delete();

        ActivityLogger::log($user->id, 'delete_hero_slide', 'website', 'Menghapus hero slide', ['slide_id' => $id]);

        return response()->json(['message' => 'Hero berhasil dihapus.']);
    }

    // ==================== ABOUT ====================
    public function storeAbout(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url'   => 'nullable|string',
            'image_1'     => 'nullable|string',
            'image_2'     => 'nullable|string',
            'image_3'     => 'nullable|string',
            'image_4'     => 'nullable|string',
            'image_5'     => 'nullable|string',
            'image_6'     => 'nullable|string',
        ]);

        $data = $request->only([
            'title', 'description', 'image_url',
            'image_1', 'image_2', 'image_3',
            'image_4', 'image_5', 'image_6',
        ]);

        $about = AboutPage::create($data);

        ActivityLogger::log($user->id, 'create_about_page', 'website', 'Membuat halaman About');

        return response()->json(['message' => 'About berhasil disimpan.', 'data' => $about], 201);
    }

    public function updateAbout(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url'   => 'nullable|string',
            'image_1'     => 'nullable|string',
            'image_2'     => 'nullable|string',
            'image_3'     => 'nullable|string',
            'image_4'     => 'nullable|string',
            'image_5'     => 'nullable|string',
            'image_6'     => 'nullable|string',
        ]);

        $about = AboutPage::findOrFail($id);

        $data = $request->only([
            'title', 'description', 'image_url',
            'image_1', 'image_2', 'image_3',
            'image_4', 'image_5', 'image_6',
        ]);

        $about->update($data);
        $about->refresh();

        ActivityLogger::log($user->id, 'update_about_page', 'website', 'Mengupdate halaman About');

        return response()->json(['message' => 'About berhasil diupdate.', 'data' => $about]);
    }

    // ==================== STATS ====================
    public function storeStats(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'label_1' => 'required|string', 'value_1' => 'required|string',
            'label_2' => 'required|string', 'value_2' => 'required|string',
            'label_3' => 'required|string', 'value_3' => 'required|string',
            'label_4' => 'required|string', 'value_4' => 'required|string',
        ]);

        $data = $request->only([
            'label_1', 'value_1', 'label_2', 'value_2',
            'label_3', 'value_3', 'label_4', 'value_4',
        ]);

        $stats = StatsPage::create($data);

        ActivityLogger::log($user->id, 'create_stats_page', 'website', 'Membuat halaman Stats');

        return response()->json(['message' => 'Stats berhasil disimpan.', 'data' => $stats], 201);
    }

    public function updateStats(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'label_1' => 'required|string', 'value_1' => 'required|string',
            'label_2' => 'required|string', 'value_2' => 'required|string',
            'label_3' => 'required|string', 'value_3' => 'required|string',
            'label_4' => 'required|string', 'value_4' => 'required|string',
        ]);

        $stats = StatsPage::findOrFail($id);

        $data = $request->only([
            'label_1', 'value_1', 'label_2', 'value_2',
            'label_3', 'value_3', 'label_4', 'value_4',
        ]);

        $stats->update($data);
        $stats->refresh();

        ActivityLogger::log($user->id, 'update_stats_page', 'website', 'Mengupdate halaman Stats');

        return response()->json(['message' => 'Stats berhasil diupdate.', 'data' => $stats]);
    }

    // ==================== SERVICES ====================
    public function storeService(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon_url'    => 'nullable|string',
            'image_1'     => 'nullable|string',
            'image_2'     => 'nullable|string',
            'image_3'     => 'nullable|string',
            'image_4'     => 'nullable|string',
            'order'       => 'nullable|integer',
        ]);

        $data = $request->only(['name', 'description', 'icon_url', 'image_1', 'image_2', 'image_3', 'image_4', 'order']);
        $data['is_active'] = true;

        $service = Service::create($data);

        ActivityLogger::log($user->id, 'create_service', 'website', 'Menambahkan layanan', ['service_id' => $service->id]);

        return response()->json(['message' => 'Layanan berhasil ditambahkan.', 'data' => $service], 201);
    }

    public function updateService(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon_url'    => 'nullable|string',
            'order'       => 'nullable|integer',
        ]);

        $service = Service::findOrFail($id);

        $data = $request->only(['name', 'description', 'icon_url', 'order']);
        $data['is_active'] = $request->boolean('is_active');

        $service->update($data);
        $service->refresh();

        ActivityLogger::log($user->id, 'update_service', 'website', 'Mengupdate layanan', ['service_id' => $id]);

        return response()->json(['message' => 'Layanan berhasil diupdate.', 'data' => $service]);
    }

    public function destroyService(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        Service::findOrFail($id)->delete();

        ActivityLogger::log($user->id, 'delete_service', 'website', 'Menghapus layanan', ['service_id' => $id]);

        return response()->json(['message' => 'Layanan berhasil dihapus.']);
    }

    // ==================== Call To Action ====================
    public function storeCta(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_url'  => 'nullable|string|max:255',
        ]);

        $data = $request->only(['title', 'description', 'button_text', 'button_url']);

        $cta = CallToAction::create($data);

        ActivityLogger::log($user->id, 'create_cta', 'website', 'Membuat Call to Action');

        return response()->json(['message' => 'CTA berhasil disimpan.', 'data' => $cta], 201);
    }

    public function updateCta(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_url'  => 'nullable|string|max:255',
        ]);

        $cta = CallToAction::findOrFail($id);

        $data = $request->only(['title', 'description', 'button_text', 'button_url']);
        $cta->update($data);
        $cta->refresh();

        ActivityLogger::log($user->id, 'update_cta', 'website', 'Mengupdate Call to Action');

        return response()->json(['message' => 'CTA berhasil diupdate.', 'data' => $cta]);
    }
}