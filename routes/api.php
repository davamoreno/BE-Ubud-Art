<?php

use App\Jobs\TestQueueJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\TokoController;
use App\Http\Controllers\Admin\BeritaController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\Auth\AuthAdminController;
// routes/api.php
use App\Http\Controllers\User\Auth\AuthCustomerController; // Nama class diperbaiki
use App\Http\Controllers\User\RiviewController; // Menggunakan nama controller yang ada

Route::get('/test-queue', function () {
    TestQueueJob::dispatch();
    return 'Pekerjaan tes telah dikirim ke antrian!';
});
/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Rute untuk registrasi, login, dan manajemen token.
|
*/

Route::prefix('auth')->group(function () {
    // Rute Autentikasi untuk Admin
    Route::prefix('admin')->group(function () {
        Route::post('login', [AuthAdminController::class, 'login']);
        // Registrasi admin biasanya tidak publik, tapi disesuaikan dengan kebutuhan.
        // Jika registrasi admin seharusnya dilindungi, pindahkan ke grup middleware.
        Route::post('register', [AuthAdminController::class, 'store']);
    });

    // Rute Autentikasi untuk Customer
    Route::prefix('customer')->group(function () {
        Route::post('login', [AuthCustomerController::class, 'login']);
        Route::post('register', [AuthCustomerController::class, 'store']);
    });

    // Rute untuk refresh token (membutuhkan token yang valid/bisa di-refresh)
    Route::middleware('auth:api')->post('/refresh', function () {
        return response()->json([
            'access_token' => auth('api')->refresh(),
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    });
});


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
| Rute yang dapat diakses oleh siapa saja tanpa perlu autentikasi.
| Umumnya untuk menampilkan data (Read operations).
|
*/

// Rute untuk Berita
Route::get('berita', [BeritaController::class, 'index']);
Route::get('berita/{berita}', [BeritaController::class, 'show']); // Menggunakan {berita} untuk route model binding

// Rute untuk Produk
Route::get('produk/random', [ProdukController::class, 'getRandomProducts']);
Route::get('produk', [ProdukController::class, 'index']);
Route::get('produk/{produk}', [ProdukController::class, 'show']);
Route::get('produk/{produk}/reviews', [RiviewController::class, 'indexByProduct']);

// Rute untuk Kategori
Route::get('kategori', [KategoriController::class, 'index']);
Route::get('kategori/{id}', [KategoriController::class, 'show']);

// Rute untuk Tag
Route::get('tag', [TagController::class, 'index']);
Route::get('tag/{id}', [TagController::class, 'show']);

// Rute untuk Toko
Route::get('toko', [TokoController::class, 'index']);
Route::get('toko/{toko}', [TokoController::class, 'show']);

// Rute untuk Review
Route::get('review', [RiviewController::class, 'index']);
Route::get('review/{id}', [RiviewController::class, 'show']); // Menggunakan {riview} sesuai nama model/controller

Route::get('dashboard/recommendations', [DashboardController::class, 'recommendations']);

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
|
| Rute yang memerlukan autentikasi API.
| Umumnya untuk Create, Update, Delete operations.
|
*/

Route::middleware('auth:api')->group(function () {
    Route::get('profile', [AuthCustomerController::class, 'show']);

    // Rute Manajemen untuk Admin
    Route::prefix('admin')->group(function () {
        // CRUD untuk Berita
        Route::post('berita', [BeritaController::class, 'store']);
        Route::put('berita/{berita}', [BeritaController::class, 'update']);
        Route::delete('berita/{berita}', [BeritaController::class, 'destroy']);

        // CRUD untuk Produk
        Route::post('produk', [ProdukController::class, 'store']);
        Route::put('produk/{produk}', [ProdukController::class, 'update']);
        Route::delete('produk/{produk}', [ProdukController::class, 'destroy']);

        // CRUD untuk Kategori
        Route::post('kategori', [KategoriController::class, 'store']);
        Route::put('kategori/{id}', [KategoriController::class, 'update']);
        Route::delete('kategori/{id}', [KategoriController::class, 'destroy']);

        // CRUD untuk Tag
        Route::post('tag', [TagController::class, 'store']);
        Route::put('tag/{id}', [TagController::class, 'update']);
        Route::delete('tag/{id}', [TagController::class, 'destroy']);

        // CRUD untuk Toko
        Route::post('toko', [TokoController::class, 'store']);
        Route::put('toko/{toko}', [TokoController::class, 'update']);
        Route::delete('toko/{toko}', [TokoController::class, 'destroy']);

        // Manajemen User oleh Admin
        Route::get('admins', [AuthAdminController::class, 'index']);
        Route::get('profile', [AuthAdminController::class, 'show']);
        Route::put('edit', [AuthAdminController::class, 'update']);
        Route::delete('delete', [AuthAdminController::class, 'destroy']);
    });

    // Rute untuk Customer yang sudah login
    Route::prefix('customer')->group(function () {
        Route::post('review/{id}', [RiviewController::class, 'store']); // Create review untuk sebuah produk
        Route::put('review/{id}', [RiviewController::class, 'update']); // Update review spesifik
        Route::delete('review/{id}', [RiviewController::class, 'destroy']); // Delete review spesifik
    });
});
