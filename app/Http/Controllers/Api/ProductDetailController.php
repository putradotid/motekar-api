<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    // ==================== LIST DETAIL PRODUK ====================
    public function index(Request $request, int $productId)
    {
        $user = $request->attributes->get('user');

        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $product = Product::findOrFail($productId);

        return response()->json([
            'product' => $product,
            'details' => ProductDetail::where('product_id', $productId)
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
            'product_id'  => 'required|exists:products,id',
            'title'       => 'required|string|max:255',
            'image'       => 'nullable|string',
            'description' => 'nullable|string',
            'order'       => 'nullable|integer',
        ]);

        $detail = ProductDetail::create([
            'product_id' => $request->product_id,
            'title'      => $request->title,
            'image'      => $request->image,
            'description'=> $request->description,
            'order'      => $request->order ?? 1,
            'is_active'  => true,
        ]);

        ActivityLogger::log(
            $user->id,
            'create_product_detail',
            'website',
            'Menambahkan detail produk',
            [
                'product_detail_id' => $detail->id
            ]
        );

        return response()->json([
            'message' => 'Detail produk berhasil ditambahkan.',
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
            ProductDetail::findOrFail($id)
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

        $detail = ProductDetail::findOrFail($id);

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
            'update_product_detail',
            'website',
            'Mengupdate detail produk',
            [
                'product_detail_id' => $detail->id
            ]
        );

        return response()->json([
            'message' => 'Detail produk berhasil diupdate.',
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

        $detail = ProductDetail::findOrFail($id);

        $detail->delete();

        ActivityLogger::log(
            $user->id,
            'delete_product_detail',
            'website',
            'Menghapus detail produk',
            [
                'product_detail_id' => $id
            ]
        );

        return response()->json([
            'message' => 'Detail produk berhasil dihapus.'
        ]);
    }
}