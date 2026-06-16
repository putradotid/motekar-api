<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\ProdukLayananPage;
use App\Models\Product;
use App\Models\ServiceItem;
use Illuminate\Http\Request;

class ProdukLayananController extends Controller
{
    // ✅ Public — semua data produk & layanan
    public function show()
    {
        return response()->json([
            'hero'     => ProdukLayananPage::first(),
            'products' => Product::where('is_active', true)->orderBy('order')->get(),
            'services' => ServiceItem::where('is_active', true)->orderBy('order')->get(),
        ]);
    }

    // ✅ Admin — semua data untuk edit
    public function index(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json([
            'hero'     => ProdukLayananPage::first(),
            'products' => Product::orderBy('order')->get(),
            'services' => ServiceItem::orderBy('order')->get(),
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
        $hero = ProdukLayananPage::create($data);

        ActivityLogger::log($user->id, 'create_produk_layanan_hero', 'website', 'Membuat hero Produk & Layanan');

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

        $hero = ProdukLayananPage::findOrFail($id);
        $hero->update($request->only(['title', 'description']));
        $hero->refresh();

        ActivityLogger::log($user->id, 'update_produk_layanan_hero', 'website', 'Mengupdate hero Produk & Layanan');

        return response()->json(['message' => 'Hero berhasil diupdate.', 'data' => $hero]);
    }

    // ==================== PRODUCTS (Section 2) ====================
    public function storeProduct(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title'              => 'required|string|max:255',
            'image_url'          => 'nullable|string',
            'description'        => 'nullable|string',
            'detail_description' => 'nullable|string',
            'order'              => 'nullable|integer',
        ]);

        $data = $request->only(['title', 'image_url', 'description', 'detail_description', 'order']);
        $data['is_active'] = true;

        $product = Product::create($data);

        ActivityLogger::log($user->id, 'create_product', 'website', 'Menambahkan produk', ['product_id' => $product->id]);

        return response()->json(['message' => 'Produk berhasil ditambahkan.', 'data' => $product], 201);
    }

    public function updateProduct(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title'              => 'required|string|max:255',
            'image_url'          => 'nullable|string',
            'description'        => 'nullable|string',
            'detail_description' => 'nullable|string',
            'order'              => 'nullable|integer',
        ]);

        $product = Product::findOrFail($id);

        $data = $request->only(['title', 'image_url', 'description', 'detail_description', 'order']);
        $data['is_active'] = $request->boolean('is_active');

        $product->update($data);
        $product->refresh();

        ActivityLogger::log($user->id, 'update_product', 'website', 'Mengupdate produk', ['product_id' => $id]);

        return response()->json(['message' => 'Produk berhasil diupdate.', 'data' => $product]);
    }

    public function destroyProduct(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        Product::findOrFail($id)->delete();

        ActivityLogger::log($user->id, 'delete_product', 'website', 'Menghapus produk', ['product_id' => $id]);

        return response()->json(['message' => 'Produk berhasil dihapus.']);
    }

    // ==================== SERVICES (Section 3) ====================
    public function storeService(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title'              => 'required|string|max:255',
            'icon_url'           => 'nullable|string',
            'description'        => 'nullable|string',
            'detail_description' => 'nullable|string',
            'order'              => 'nullable|integer',
        ]);

        $data = $request->only(['title', 'icon_url', 'description', 'detail_description', 'order']);
        $data['is_active'] = true;

        $service = ServiceItem::create($data);

        ActivityLogger::log($user->id, 'create_service_item', 'website', 'Menambahkan layanan', ['service_id' => $service->id]);

        return response()->json(['message' => 'Layanan berhasil ditambahkan.', 'data' => $service], 201);
    }

    public function updateService(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title'              => 'required|string|max:255',
            'icon_url'           => 'nullable|string',
            'description'        => 'nullable|string',
            'detail_description' => 'nullable|string',
            'order'              => 'nullable|integer',
        ]);

        $service = ServiceItem::findOrFail($id);

        $data = $request->only(['title', 'icon_url', 'description', 'detail_description', 'order']);
        $data['is_active'] = $request->boolean('is_active');

        $service->update($data);
        $service->refresh();

        ActivityLogger::log($user->id, 'update_service_item', 'website', 'Mengupdate layanan', ['service_id' => $id]);

        return response()->json(['message' => 'Layanan berhasil diupdate.', 'data' => $service]);
    }

    public function destroyService(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        ServiceItem::findOrFail($id)->delete();

        ActivityLogger::log($user->id, 'delete_service_item', 'website', 'Menghapus layanan', ['service_id' => $id]);

        return response()->json(['message' => 'Layanan berhasil dihapus.']);
    }
}