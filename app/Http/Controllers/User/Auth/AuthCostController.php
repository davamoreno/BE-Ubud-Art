<?php

namespace App\Http\Controllers\User\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Validated;
use App\Http\Requests\User\Auth\AuthRequest;
use App\Http\Resources\User\Auth\AuthResource;
use App\Enums\UserRoles;
use App\Http\Requests\User\Auth\LoginRequest;

class AuthCostController extends Controller
{
    public function index() {
        $user = User::role(UserRoles::COSTUMER->value)->get();
        return AuthResource::collection($user);
    }

    public function store(AuthRequest $request) {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        $user->assignRole(UserRoles::COSTUMER->value);
    
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

        if (auth()->user()->hasRole(UserRoles::COSTUMER->value)) {
            return $this->respondWithToken($token);
        }else{
            return response()->json(['message' => 'You\'re not a admin'], 403);
        }
    }

    public function respondWithToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => [
                'id'    => auth()->id(),
                'name'  => auth()->user()->name,
                'email' => auth()->user()->email,
                'role'  => auth()->user()->getRoleNames()->first(),
            ]
        ]);
    }
}
