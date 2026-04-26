<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class ProductController extends Controller
{
    public function index()
    {
        // ✅ PERBAIKAN 1: Mengubah all() menjadi paginate() agar fungsi links() dan hasPages() di Blade berfungsi.
        // Saya juga menambahkan with('user') untuk mencegah N+1 Query problem (agar lebih cepat).
        $products = Product::with('user')->paginate(10);

        return view('product.index', compact('products'));
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        // ✅ PERBAIKAN 2: Mapping key 'quantity' dari form menjadi 'qty' untuk database
        $validated['qty'] = $validated['quantity'];
        unset($validated['quantity']); // Hapus 'quantity' agar tidak dikirim ke database

        // Set user_id sesuai user yang sedang login
        $validated['user_id'] = auth()->id();

        try {
            Product::create($validated);

            return redirect()
                ->route('product.index')
                ->with('success', 'Product created successfully.');
        } catch (QueryException $e) {
            Log::error('Product store database error', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Database error while creating product.');
        } catch (\Throwable $e) {
            Log::error('Product store unexpected error', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Unexpected error occurred.');
        }
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('product.create', compact('users', 'categories'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return view('product.view', compact('product'));
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        \Illuminate\Support\Facades\Gate::authorize('update', $product);

        $validated = $request->validated();

        // ✅ PERBAIKAN 3: Melakukan hal yang sama (mapping quantity ke qty) untuk fungsi Update
        if (isset($validated['quantity'])) {
            $validated['qty'] = $validated['quantity'];
            unset($validated['quantity']);
        }

        try {
            $product->update($validated);
            return redirect()->route('product.index')->with('success', 'Product updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Product update error', [
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->withInput()->with('error', 'Error while updating product.');
        }
    }

    public function edit(Product $product)
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $product);
        $users = User::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('product.edit', compact('product', 'users', 'categories'));
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        \Illuminate\Support\Facades\Gate::authorize('delete', $product);

        $product->delete();

        return redirect()->route('product.index')->with('success', 'Product berhasil dihapus');
    }
}