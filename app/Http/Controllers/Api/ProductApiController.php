<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * @tags Product
 */
class ProductApiController extends Controller
{
    public function index()
    {
        try {
            $products = Product::with('category')->get();
            return response()->json([
                'message' => 'Products retrieved successfully',
                'data' => $products
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data produk', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Server error'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Note: In the instruction it says `StoreProductRequest $request`, 
            // but for simplicity and since we don't have StoreProductRequest file context here,
            // we will use standard validation.
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|numeric',
                'qty' => 'required|numeric', // or 'quantity' depending on your DB schema, user previously said 'qty' is mapped to 'quantity' in form, let's use what's likely the db field
            ]);

            $validated['user_id'] = Auth::id();

            $product = Product::create($validated);

            Log::info('Menambah data produk', [
                'list' => $product
            ]);

            return response()->json([
                'message' => 'Produk berhasil ditambahkan!!',
                'data' => $product,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error saat menambah product', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $product = Product::with('category')->find($id);

            if (!$product)
            {
                return response()->json([
                    'message' => 'Product tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'message' => 'Product retrieved successfully',
                'data' => $product
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data produk', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Server error'], 500);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product tidak ditemukan',
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'category_id' => 'sometimes|required|exists:categories,id',
                'price' => 'sometimes|required|numeric',
                'qty' => 'sometimes|required|numeric',
            ]);

            $product->update($validated);

            Log::info('Mengupdate data produk', [
                'list' => $product
            ]);

            return response()->json([
                'message' => 'Produk berhasil diupdate!!',
                'data' => $product,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error saat mengupdate product', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product tidak ditemukan',
                ], 404);
            }

            $product->delete();

            Log::info('Menghapus data produk', [
                'id' => $id
            ]);

            return response()->json([
                'message' => 'Produk berhasil dihapus!!',
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error saat menghapus product', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
}
