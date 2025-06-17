<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\User;
use App\Enums\UserRoles;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (auth()->user()->hasRole(UserRoles::ADMIN->value)) {
            return $this->respondWithToken($token);
        }else{
            return response()->json(['message' => 'You\'re not a admin'], 403);
        }
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

        $user->assignRole(UserRoles::ADMIN->value);
        $user->save();

        return new AuthResource($user);
    }

    // Tampilkan detail admin tertentu
    public function show($id)
    {
        $user = User::role(UserRoles::ADMIN->value)->findOrFail($id);
        return new AuthResource($user);
    }

    // Update data admin
    public function update(UpdateAdminRequest $request, $id)
    {
        $user = User::role(UserRoles::ADMIN->value)->findOrFail($id);

        $data = $request->validated();

        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        if (!empty($data['password'])) {
            $user->update(['password' => bcrypt($data['password'])]);
        }

        return new AuthResource($user->fresh()->load('roles'));
    }

    // Hapus admin
    public function destroy($id)
    {
        $user = User::role(UserRoles::ADMIN->value)->findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Admin deleted successfully.']);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
            'user'         => [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'email' => auth()->user()->email, 
                'role' => auth()->user()->getRoleNames()->first(),
            ],
        ]);
    }

}
