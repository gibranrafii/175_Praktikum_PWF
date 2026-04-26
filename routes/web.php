<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ProductController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Product Page
    Route::get('/product/export', function () {
        return "Berhasil mengekspor produk!";
    })->name('product.export')->middleware('can:export-product');

    Route::get('/product', [ProductController::class, 'index'])->name('product.index');
    Route::post('/product', [ProductController::class, 'store'])->name('product.store');
    Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
    Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
    Route::put('/product/update/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::get('/product/edit/{product}', [ProductController::class, 'edit'])->name('product.edit');
    Route::delete('/product/delete/{id}', [ProductController::class, 'delete'])->name('product.delete');

    // Category Routes (Admin Only)
    Route::middleware('can:view-category')->group(function () {
        Route::get('/category', [App\Http\Controllers\CategoryController::class, 'index'])->name('category.index');
        Route::get('/category/create', [App\Http\Controllers\CategoryController::class, 'create'])->name('category.create');
        Route::post('/category', [App\Http\Controllers\CategoryController::class, 'store'])->name('category.store');
        Route::get('/category/edit/{category}', [App\Http\Controllers\CategoryController::class, 'edit'])->name('category.edit');
        Route::put('/category/update/{category}', [App\Http\Controllers\CategoryController::class, 'update'])->name('category.update');
        Route::delete('/category/delete/{category}', [App\Http\Controllers\CategoryController::class, 'destroy'])->name('category.destroy');
    });
});

Route::get('/about', [AboutController::class, 'index'])->name('about');


require __DIR__ . '/auth.php';
