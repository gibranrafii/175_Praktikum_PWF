<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\CategoryApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'getToken']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/product', [ProductApiController::class, 'store'])->name('product.store');
    Route::put('/product/{id}', [ProductApiController::class, 'update'])->name('product.update');
    Route::delete('/product/{id}', [ProductApiController::class, 'destroy'])->name('product.destroy');
    
    // Category API routes protected by Sanctum
    Route::post('/category', [CategoryApiController::class, 'store'])->name('category.store');
    Route::put('/category/{id}', [CategoryApiController::class, 'update'])->name('category.update');
    Route::delete('/category/{id}', [CategoryApiController::class, 'destroy'])->name('category.destroy');
});

Route::get('/product', [ProductApiController::class, 'index'])->name('product.index');
Route::get('/product/{id}', [ProductApiController::class, 'show'])->name('product.show');

Route::get('/category', [CategoryApiController::class, 'index'])->name('category.index');
Route::get('/category/{id}', [CategoryApiController::class, 'show'])->name('category.show');
