<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\TestimoniPage;
use App\Models\Testimonial;
use App\Models\ClientPartner;
use App\Models\FeaturedCustomer;
use Illuminate\Http\Request;

class TestimoniController extends Controller
{
    // Public — semua data testimoni
    public function show()
    {
        return response()->json([
            'hero'        => TestimoniPage::first(),
            'featured_customers' => FeaturedCustomer::where('is_active', true)->orderBy('order')->get(),
            'testimonials'=> Testimonial::where('is_active', true)->orderBy('order')->get(),
            'partners'    => ClientPartner::where('is_active', true)->orderBy('order')->get(),
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
            'hero'        => TestimoniPage::first(),
            'featured_customers' => FeaturedCustomer::orderBy('order')->get(),
            'testimonials'=> Testimonial::orderBy('order')->get(),
            'partners'    => ClientPartner::orderBy('order')->get(),
        ]);
    }

    // ==================== HERO (Section 1) ====================
    public function storeHero(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data = $request->only(['title', 'description']);
        $hero = TestimoniPage::create($data);

        ActivityLogger::log($user->id, 'create_testimoni_hero', 'website', 'Membuat hero Testimoni');

        return response()->json(['message' => 'Hero berhasil disimpan.', 'data' => $hero], 201);
    }

    public function updateHero(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $hero = TestimoniPage::findOrFail($id);
        $hero->update($request->only(['title', 'description']));
        $hero->refresh();

        ActivityLogger::log($user->id, 'update_testimoni_hero', 'website', 'Mengupdate hero Testimoni');

        return response()->json(['message' => 'Hero berhasil diupdate.', 'data' => $hero]);
    }

    // ==================== TESTIMONIALS (Section 2) ====================
    public function storeTestimonial(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'photo'         => 'nullable|string',
            'title'         => 'required|string',
            'description'   => 'required|string',
            'name'          => 'required|string|max:255',
            'social_handle' => 'nullable|string|max:255',
            'order'         => 'nullable|integer',
        ]);

        $data = $request->only(['photo', 'title', 'description', 'name', 'social_handle', 'order']);
        $data['is_active'] = true;

        $testimonial = Testimonial::create($data);

        ActivityLogger::log($user->id, 'create_testimonial', 'website', 'Menambahkan testimoni', ['testimonial_id' => $testimonial->id]);

        return response()->json(['message' => 'Testimoni berhasil ditambahkan.', 'data' => $testimonial], 201);
    }

    public function updateTestimonial(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'photo'         => 'nullable|string',
            'title'         => 'required|string',
            'description'   => 'required|string',
            'name'          => 'required|string|max:255',
            'social_handle' => 'nullable|string|max:255',
            'order'         => 'nullable|integer',
        ]);

        $testimonial = Testimonial::findOrFail($id);

        $data = $request->only(['photo', 'title', 'description', 'name', 'social_handle', 'order']);
        $data['is_active'] = $request->boolean('is_active');

        $testimonial->update($data);
        $testimonial->refresh();

        ActivityLogger::log($user->id, 'update_testimonial', 'website', 'Mengupdate testimoni', ['testimonial_id' => $id]);

        return response()->json(['message' => 'Testimoni berhasil diupdate.', 'data' => $testimonial]);
    }

    public function destroyTestimonial(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        Testimonial::findOrFail($id)->delete();

        ActivityLogger::log($user->id, 'delete_testimonial', 'website', 'Menghapus testimoni', ['testimonial_id' => $id]);

        return response()->json(['message' => 'Testimoni berhasil dihapus.']);
    }

    // ==================== CLIENT & PARTNERS (Section 3) ====================
    public function storePartner(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'name'       => 'nullable|string|max:255',
            'logo_image' => 'required|string',
            'order'      => 'nullable|integer',
        ]);

        $data = $request->only(['name', 'logo_image', 'order']);
        $data['is_active'] = true;

        $partner = ClientPartner::create($data);

        ActivityLogger::log($user->id, 'create_client_partner', 'website', 'Menambahkan client/partner', ['partner_id' => $partner->id]);

        return response()->json(['message' => 'Client/Partner berhasil ditambahkan.', 'data' => $partner], 201);
    }

    public function updatePartner(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'name'       => 'nullable|string|max:255',
            'logo_image' => 'required|string',
            'order'      => 'nullable|integer',
        ]);

        $partner = ClientPartner::findOrFail($id);

        $data = $request->only(['name', 'logo_image', 'order']);
        $data['is_active'] = $request->boolean('is_active');

        $partner->update($data);
        $partner->refresh();

        ActivityLogger::log($user->id, 'update_client_partner', 'website', 'Mengupdate client/partner', ['partner_id' => $id]);

        return response()->json(['message' => 'Client/Partner berhasil diupdate.', 'data' => $partner]);
    }

    public function destroyPartner(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        ClientPartner::findOrFail($id)->delete();

        ActivityLogger::log($user->id, 'delete_client_partner', 'website', 'Menghapus client/partner', ['partner_id' => $id]);

        return response()->json(['message' => 'Client/Partner berhasil dihapus.']);
    }
    
    // ==================== FEATURED CUSTOMERS (Section 4) ====================
    public function storeFeaturedCustomer(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'photo'       => 'nullable|string',
            'designation' => 'nullable|string|max:255',
            'order'       => 'nullable|integer',
        ]);

        $data = $request->only(['photo', 'name', 'designation', 'order']);
        $data['is_active'] = true;

        $customer = FeaturedCustomer::create($data);

        ActivityLogger::log($user->id, 'create_featured_customer', 'website', 'Menambahkan featured customer', ['customer_id' => $customer->id]);

        return response()->json(['message' => 'Featured customer berhasil ditambahkan.', 'data' => $customer], 201);
    }

    public function updateFeaturedCustomer(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'photo'       => 'nullable|string',
            'designation' => 'nullable|string|max:255',
            'order'       => 'nullable|integer',
        ]);

        $customer = FeaturedCustomer::findOrFail($id);

        $data = $request->only(['photo', 'name', 'designation', 'order']);
        $data['is_active'] = $request->boolean('is_active');

        $customer->update($data);
        $customer->refresh();

        ActivityLogger::log($user->id, 'update_featured_customer', 'website', 'Mengupdate featured customer', ['customer_id' => $id]);

        return response()->json(['message' => 'Featured customer berhasil diupdate.', 'data' => $customer]);
    }

    public function destroyFeaturedCustomer(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        FeaturedCustomer::findOrFail($id)->delete();

        ActivityLogger::log($user->id, 'delete_featured_customer', 'website', 'Menghapus featured customer', ['customer_id' => $id]);

        return response()->json(['message' => 'Featured customer berhasil dihapus.']);
    }
}