<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\ServiceDetail;
use App\Models\ServiceItem;
use Illuminate\Http\Request;

class ServiceDetailController extends Controller
{
    // ==================== LIST DETAIL LAYANAN ====================
    public function index(Request $request, int $serviceId)
    {
        $user = $request->attributes->get('user');

        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $service = ServiceItem::findOrFail($serviceId);

        return response()->json([
            'service' => $service,
            'details' => ServiceDetail::where('service_id', $serviceId)
                ->orderBy('order')
                ->get(),
        ]);
    }

    // ==================== TAMBAH DETAIL ====================
    public function store(Request $request)
    {
        $user = $request->attributes->get('user');

        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $request->validate([
            'service_id'  => 'required|exists:service_items,id',
            'title'       => 'required|string|max:255',
            'image'       => 'nullable|string',
            'description' => 'nullable|string',
            'order'       => 'nullable|integer',
        ]);

        $detail = ServiceDetail::create([
            'service_id'  => $request->service_id,
            'title'       => $request->title,
            'image'       => $request->image,
            'description' => $request->description,
            'order'       => $request->order ?? 1,
            'is_active'   => true,
        ]);

        ActivityLogger::log(
            $user->id,
            'create_service_detail',
            'website',
            'Menambahkan detail layanan',
            [
                'service_detail_id' => $detail->id
            ]
        );

        return response()->json([
            'message' => 'Detail layanan berhasil ditambahkan.',
            'data'    => $detail
        ], 201);
    }

    // ==================== DETAIL ====================
    public function show(Request $request, int $id)
    {
        $user = $request->attributes->get('user');

        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        return response()->json(
            ServiceDetail::findOrFail($id)
        );
    }

    // ==================== UPDATE ====================
    public function update(Request $request, int $id)
    {
        $user = $request->attributes->get('user');

        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'image'       => 'nullable|string',
            'description' => 'nullable|string',
            'order'       => 'nullable|integer',
        ]);

        $detail = ServiceDetail::findOrFail($id);

        $detail->update([
            'title'       => $request->title,
            'image'       => $request->image,
            'description' => $request->description,
            'order'       => $request->order ?? 1,
            'is_active'   => $request->boolean('is_active'),
        ]);

        $detail->refresh();

        ActivityLogger::log(
            $user->id,
            'update_service_detail',
            'website',
            'Mengupdate detail layanan',
            [
                'service_detail_id' => $detail->id
            ]
        );

        return response()->json([
            'message' => 'Detail layanan berhasil diupdate.',
            'data'    => $detail
        ]);
    }

    // ==================== HAPUS ====================
    public function destroy(Request $request, int $id)
    {
        $user = $request->attributes->get('user');

        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $detail = ServiceDetail::findOrFail($id);

        $detail->delete();

        ActivityLogger::log(
            $user->id,
            'delete_service_detail',
            'website',
            'Menghapus detail layanan',
            [
                'service_detail_id' => $id
            ]
        );

        return response()->json([
            'message' => 'Detail layanan berhasil dihapus.'
        ]);
    }
}