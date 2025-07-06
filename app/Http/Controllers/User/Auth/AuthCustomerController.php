<?php

namespace App\Http\Controllers\User\Auth;

use App\Models\User;
use App\Enums\UserRoles;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Validated;
use App\Http\Requests\User\Auth\AuthRequest;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Http\Resources\User\Auth\AuthResource;

class AuthCustomerController extends Controller
{
    public function index() {
        $user = User::role(UserRoles::CUSTOMER->value)->get();
        return AuthResource::collection($user);
    }

    public function store(AuthRequest $request) {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        $customerRole = Role::findByName(UserRoles::CUSTOMER->value, 'api');
        $user->assignRole($customerRole);
    
        return new AuthResource($user);
    }

    public function show() {
        $user = auth()->user();
        return new AuthResource($user);
    }

    public function login(LoginRequest $request) {
        $credentials = $request->only(['email', 'password']);

         if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (auth()->user()->hasRole(UserRoles::CUSTOMER->value)) {
            return $this->respondWithToken($token);
        }else{
            return response()->json(['message' => 'You\'re not a user'], 403);
        }
    }

    public function respondWithToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 1440,
            'user' => [
                'id'    => auth()->id(),
                'name'  => auth()->user()->name,
                'email' => auth()->user()->email,
                'role'  => auth()->user()->getRoleNames()->first(),
            ]
        ]);
    }
}
