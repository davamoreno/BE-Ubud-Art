<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\User;
use App\Enums\UserRoles;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role; // <-- 1. IMPORT MODEL ROLE
use App\Http\Requests\Admin\Auth\AuthRequest;
use App\Http\Resources\Admin\Auth\AuthResource;
use App\Http\Requests\Admin\Auth\UpdateAdminRequest;
use App\Http\Requests\Admin\Auth\LoginRequest;

class AuthAdminController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Email atau password salah.'], 401);
        }

        // Cek apakah user yang berhasil login adalah seorang Admin
        // Menggunakan auth()->user() setelah attempt sudah aman.
        if (!auth()->user()->hasRole(UserRoles::ADMIN->value)) {
            auth('api')->logout(); // Logout paksa jika bukan admin
            return response()->json(['error' => 'Akun ini tidak memiliki akses admin.'], 403);
        }

        return $this->respondWithToken($token);
    }

    // Ambil semua user dengan role Admin
    public function index()
    {
        $admins = User::role(UserRoles::ADMIN->value)->get();
        return AuthResource::collection($admins);
    }

    // Buat user admin baru
    public function store(AuthRequest $request)
    {
        
        $data = $request->validated();

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        // 4. PERBAIKAN BUG UTAMA: Cari dan berikan role dari guard 'api'
        $adminRole = Role::findByName(UserRoles::ADMIN->value, 'api');
        $user->assignRole($adminRole);
        
        // $user->save() tidak perlu dipanggil setelah create.

        return new AuthResource($user);
    }

    // 5. PERBAIKAN: Gunakan Route-Model Binding untuk konsistensi
    // Ini untuk melihat detail admin SPESIFIK, bukan profil diri sendiri
    public function show(User $user)
    {
        return new AuthResource($user);
    }

    // 6. PERBAIKAN: Gunakan Route-Model Binding
    public function update(UpdateAdminRequest $request, User $user)
    {

        $data = $request->validated();
        
        // Tidak perlu lagi: $user = User::role(...)->findOrFail($id);

        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        if (!empty($data['password'])) {
            $user->update(['password' => bcrypt($data['password'])]);
        }

        return new AuthResource($user->fresh()->load('roles'));
    }

    // 7. PERBAIKAN: Gunakan Route-Model Binding
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'Admin berhasil dihapus.']);
    }

    // Method ini bisa dibuat menjadi "get my profile"
    public function profile()
    {
        return new AuthResource(auth()->user());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 1440, // 24 jam
            'user'         => new AuthResource(auth()->user()),
        ]);
    }
}
