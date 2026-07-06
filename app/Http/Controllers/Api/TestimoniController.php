<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\TestimoniPage;
use App\Models\Testimonial;
use App\Models\ClientPartner;
use App\Models\FeaturedCustomer;
use App\Models\MeetingRequests;
use Illuminate\Http\Request;

class TestimoniController extends Controller
{
    // Public — semua data testimoni
    public function index(Request $request)
{
    $user = $request->attributes->get('user');

    if (!$user || $user->role !== 'admin') {
        return response()->json([
            'message' => 'Forbidden'
        ], 403);
    }

    return response()->json([
        'hero' => TestimoniPage::first(),

        'featured_customers' => FeaturedCustomer::orderBy('order')->get(),

        'testimonials' => Testimonial::with('user')
            ->orderBy('created_at', 'desc')
            ->get(),

        'partners' => ClientPartner::orderBy('order')->get(),
    ]);
}
    // Public — semua data testimoni
public function show()
    {
        $testimonials = Testimonial::with('user')
            ->where('status', 'approved')
            ->where('is_active', 1)
            ->orderBy('order')
            ->get()
            ->map(function ($item) {

                return [
                    'id'            => $item->id,
                    'title'         => $item->title,
                    'description'   => $item->description,
                    'name'          => $item->name,
                    'social_handle' => $item->social_handle,
                    'rating'        => $item->rating,
                    'order'         => $item->order,

                    // Foto diambil dari profile user
                    'photo' => $item->user && $item->user->photo
                        ? asset('storage/' . $item->user->photo)
                        : null,
                ];
            });

        return response()->json([
            'hero'               => TestimoniPage::first(),
            'featured_customers' => FeaturedCustomer::where('is_active', true)
                                        ->orderBy('order')
                                        ->get(),
            'testimonials'       => $testimonials,
            'partners'           => ClientPartner::where('is_active', true)
                                        ->orderBy('order')
                                        ->get(),
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
    // public function storeTestimonial(Request $request)
    // {
    //     $user = $request->attributes->get('user');
    //     if ($user->role !== 'user') {
    //         return response()->json([
    //             'message' => 'Forbidden'
    //         ], 403);
    //     }

    //     $request->validate([
    //         'meeting_id'  => 'required|integer',
    //         'title'       => 'required|string',
    //         'description' => 'required|string',
    //         'rating'      => 'required|integer|min:1|max:5',
    //         'position'    => 'nullable|string|max:255',
    //         'social_handle' => 'nullable|string|max:255',
    //     ]);

    //     // Cek meeting milik user dan statusnya done
    //     $meeting = MeetingRequests::where('id', $request->meeting_id)
    //                               ->where('user_id', $user->id)
    //                               ->where('status', 'done')
    //                               ->first();

    //     if (!$meeting) {
    //         return response()->json([
    //             'message' => 'Meeting tidak ditemukan atau belum selesai.'
    //         ], 422);
    //     }

    //     // Cek sudah pernah testimoni untuk meeting ini
    //     $existing = Testimonial::where('meeting_id', $request->meeting_id)
    //                            ->where('user_id', $user->id)
    //                            ->first();

    //     if ($existing) {
    //         return response()->json([
    //             'message' => 'Anda sudah memberikan testimoni untuk meeting ini.'
    //         ], 422);
    //     }

    //     $testimonial = Testimonial::create([
    //         'user_id'      => $user->id,
    //         'meeting_id'   => $request->meeting_id,
    //         'name'         => $user->name,
    //         'photo'        => $user->photo ?? null,
    //         'title'        => $request->title,
    //         'description'  => $request->description,
    //         'rating'       => $request->rating,
    //         'position'     => $request->position,
    //         'social_handle'=> $request->social_handle,
    //         'status'       => 'pending',
    //         'is_active'    => false,
    //         'order'        => 0,
    //     ]);

    //     return response()->json([
    //         'message' => 'Testimoni berhasil dikirim. Menunggu persetujuan admin.',
    //         'data'    => $testimonial,
    //     ], 201);
        
    // }

    // Admin — edit testimoni sebelum approve
    // public function updateTestimonial(Request $request, int $id)
    // {
    //     $user = $request->attributes->get('user');
    //     if ($user->role !== 'admin') {
    //         return response()->json(['message' => 'Forbidden'], 403);
    //     }

    //     $request->validate([
    //         'title'        => 'required|string',
    //         'description'  => 'required|string',
    //         'name'         => 'required|string|max:255',
    //         'position'     => 'nullable|string|max:255',
    //         'social_handle'=> 'nullable|string|max:255',
    //         'rating'       => 'required|integer|min:1|max:5',
    //         'photo'        => 'nullable|string',
    //         'order'        => 'nullable|integer',
    //     ]);

    //     $testimonial = Testimonial::findOrFail($id);

    //     $data = $request->only([
    //         'title', 'description', 'name', 'position',
    //         'social_handle', 'rating', 'photo', 'order',
    //     ]);
    //     $data['is_active'] = $request->boolean('is_active');

    //     $testimonial->update($data);
    //     $testimonial->refresh();

    //     ActivityLogger::log(
    //         $user->id,
    //         'update_testimonial',
    //         'testimoni',
    //         'Mengupdate testimoni dari: ' . $testimonial->name,
    //         ['testimonial_id' => $id]
    //     );

    //     return response()->json(['message' => 'Testimoni berhasil diupdate.', 'data' => $testimonial]);
    // }

    // public function destroyTestimonial(Request $request, int $id)
    // {
    //     $user = $request->attributes->get('user');
    //     if ($user->role !== 'admin') {
    //         return response()->json(['message' => 'Forbidden'], 403);
    //     }

    //     Testimonial::findOrFail($id)->delete();

    //     ActivityLogger::log($user->id, 'delete_testimonial', 'website', 'Menghapus testimoni', ['testimonial_id' => $id]);

    //     return response()->json(['message' => 'Testimoni berhasil dihapus.']);
    // }

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

    // Cek apakah user bisa submit testimoni untuk meeting tertentu
    public function canTestify(Request $request, int $meetingId)
    {
        $user = $request->attributes->get('user');

        $meeting = MeetingRequests::where('id', $meetingId)
                                  ->where('user_id', $user->id)
                                  ->where('status', 'done')
                                  ->first();

        if (!$meeting) {
            return response()->json(['can_testify' => false, 'reason' => 'Meeting belum selesai.']);
        }

        $existing = Testimonial::where('meeting_id', $meetingId)
                               ->where('user_id', $user->id)
                               ->first();

        if ($existing) {
            return response()->json(['can_testify' => false, 'reason' => 'Sudah memberikan testimoni.']);
        }

        return response()->json(['can_testify' => true]);
    }

    // Admin — approve testimoni
    public function approve(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $testimonial = Testimonial::findOrFail($id);
        $testimonial->update([
            'status'    => 'approved',
            'is_active' => true,
        ]);

        ActivityLogger::log(
            $user->id,
            'approve_testimonial',
            'testimoni',
            'Menyetujui testimoni dari: ' . $testimonial->name,
            ['testimonial_id' => $id]
        );

        return response()->json(['message' => 'Testimoni berhasil disetujui.']);
    }

    // Admin — reject testimoni
    public function reject(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $testimonial = Testimonial::findOrFail($id);
        $testimonial->update([
            'status'     => 'rejected',
            'is_active'  => false,
            'admin_notes'=> $request->admin_notes ?? null,
        ]);

        ActivityLogger::log(
            $user->id,
            'reject_testimonial',
            'testimoni',
            'Menolak testimoni dari: ' . $testimonial->name,
            ['testimonial_id' => $id]
        );

        return response()->json(['message' => 'Testimoni ditolak.']);
    }

    // store testimoni by user
    public function store(Request $request)
    {
        // Ambil user dari middleware
        $user = $request->attributes->get('user');

        if (!$user || $user->role !== 'user') {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        // Validasi
        $request->validate([
            'meeting_id'    => 'required|integer|exists:meeting_requests,id',
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'rating'        => 'required|integer|min:1|max:5',
            'position'      => 'nullable|string|max:255',
            'social_handle' => 'nullable|string|max:255',
        ]);

        // Pastikan meeting milik user & sudah selesai
        $meeting = MeetingRequests::where('id', $request->meeting_id)
            ->where('user_id', $user->id)
            ->where('status', 'done')
            ->first();

        if (!$meeting) {
            return response()->json([
                'message' => 'Meeting tidak ditemukan atau belum selesai.'
            ], 422);
        }

        // Pastikan user belum pernah mengirim testimoni
        $exists = Testimonial::where('meeting_id', $meeting->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Anda sudah mengirim testimoni untuk meeting ini.'
            ], 422);
        }

        // Simpan testimoni
        $testimonial = Testimonial::create([
            'meeting_id'    => $meeting->id,
            'user_id'       => $user->id,

            // otomatis dari user
            'name'          => $user->name,

            // input user
            'title'         => $request->title,
            'description'   => $request->description,
            'rating'        => $request->rating,
            'position'      => $request->position,
            'social_handle' => $request->social_handle,

            // default
            'status'        => 'pending',
            'is_active'     => false,
            'order'         => 0,
        ]);

        return response()->json([
            'message' => 'Testimoni berhasil dikirim dan menunggu persetujuan admin.',
            'data'    => $testimonial
        ], 201);
    }

    // Admin — edit testimoni sebelum approve
    public function update(Request $request, int $id)
    {
        $user = $request->attributes->get('user');

        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'name'          => 'required|string|max:255',
            'position'      => 'nullable|string|max:255',
            'social_handle' => 'nullable|string|max:255',
            'rating'        => 'required|integer|min:1|max:5',
            'photo'         => 'nullable|string',
            'order'         => 'nullable|integer|min:0',
            'is_active'     => 'nullable|boolean',
        ]);

        $testimonial = Testimonial::findOrFail($id);

        $testimonial->update([

            'title'         => $request->title,
            'description'   => $request->description,

            // admin boleh memperbaiki identitas bila perlu
            'name'          => $request->name,
            'position'      => $request->position,
            'social_handle' => $request->social_handle,

            'rating'        => $request->rating,
            'photo'         => $request->photo,

            'order'         => $request->order ?? 0,

        ]);

        ActivityLogger::log(
            $user->id,
            'update_testimonial',
            'testimonial',
            'Mengupdate testimoni dari: '.$testimonial->name,
            [
                'testimonial_id' => $testimonial->id
            ]
        );

        return response()->json([
            'message' => 'Testimoni berhasil diperbarui.',
            'data'    => $testimonial->fresh()
        ]);
    }
    
}