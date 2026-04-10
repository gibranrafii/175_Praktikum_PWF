<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // ✅ PERBAIKAN 1: Mengubah all() menjadi paginate() agar fungsi links() dan hasPages() di Blade berfungsi.
        // Saya juga menambahkan with('user') untuk mencegah N+1 Query problem (agar lebih cepat).
        $products = Product::with('user')->paginate(10);

        return view('product.index', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer', // Menangkap input 'quantity' dari form
            'price' => 'required|numeric',
            'user_id' => 'required|exists:users,id',
        ]);

        // ✅ PERBAIKAN 2: Mapping key 'quantity' dari form menjadi 'qty' untuk database
        $validated['qty'] = $validated['quantity'];
        unset($validated['quantity']); // Hapus 'quantity' agar tidak dikirim ke database

        // Set user_id sesuai user yang sedang login
        $validated['user_id'] = auth()->id();
        
        Product::create($validated);

        return redirect()->route('product.index')->with('success', 'Product created successfully.');
    }

    public function create()
    {
        $users = User::orderBy('name')->get();

        return view('product.create', compact('users'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return view('product.view', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'quantity' => 'sometimes|integer',
            'price' => 'sometimes|numeric',
            'user_id' => 'sometimes|exists:users,id',
        ]);

        // ✅ PERBAIKAN 3: Melakukan hal yang sama (mapping quantity ke qty) untuk fungsi Update
        if (isset($validated['quantity'])) {
            $validated['qty'] = $validated['quantity'];
            unset($validated['quantity']);
        }

        $product->update($validated);

        return redirect()->route('product.index')->with('success', 'Product updated successfully.');
    }

    public function edit(Product $product)
    {
        $users = User::orderBy('name')->get();

        return view('product.edit', compact('product', 'users'));
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return redirect()->route('product.index')->with('success', 'Product berhasil dihapus');
    }
}