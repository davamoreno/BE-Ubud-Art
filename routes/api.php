<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TokoController;
use App\Http\Controllers\User\RiviewController;
use App\Http\Controllers\Admin\BeritaController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\User\Auth\AuthCostController;
use App\Http\Controllers\Admin\Auth\AuthAdminController;
use App\Http\Controllers\Admin\TagController;

Route::post('/refresh', function () {
    return response()->json([
        'access_token' => auth('api')->refresh(),
        'token_type' => 'bearer',
        'expires_in' => auth('api')->factory()->getTTL() * 60
    ]);
});

Route::prefix('admin')->group(function () {
    Route::get('users', [AuthAdminController::class, 'index']);
    Route::post('users/login', [AuthAdminController::class, 'login']);
    Route::get('users/{id}', [AuthAdminController::class, 'show']);
    Route::put('users/{id}', [AuthAdminController::class, 'update']);
    Route::delete('users/{id}', [AuthAdminController::class, 'destroy']); 
});

Route::middleware('auth:api')->prefix('admin')->group(function () {
    Route::apiResource('berita', BeritaController::class);
    Route::apiResource('produk', ProdukController::class);
    Route::apiResource('kategori', KategoriController::class);
    Route::apiResource('tag', TagController::class);
    Route::apiResource('toko', TokoController::class);

     Route::get('/auth-test', function () {
        $user = auth()->user();

        if (!$user) {
            // Ini seharusnya tidak terjadi jika middleware auth:api bekerja
            return response()->json(['message' => 'Middleware auth:api dilewati, user NULL.'], 401);
        }

        return response()->json([
            'status' => 'SUKSES!',
            'pesan' => 'Kamu berhasil mengakses rute terproteksi.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames() // Menggunakan metode dari Spatie untuk memastikan
            ]
        ]);
    });
});

Route::prefix('costumer')->group(function () {
    Route::post('/{produk}/review', [RiviewController::class, 'store']);
    Route::apiResource('review', RiviewController::class);
});

Route::post('users/register', [AuthAdminController::class, 'store']);

Route::prefix('costumer')->group(function () {
    Route::get('users', [AuthCostController::class, 'index']);
    Route::get('users/profile', [AuthCostController::class, 'show']);
    Route::post('users/register', [AuthCostController::class, 'store']);
    Route::post('users/login', [AuthCostController::class, 'login']);
});