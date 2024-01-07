<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function store(RegisterUserRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' =>  $user
        ]);
    }

    public function login(LoginUserRequest $request): \Illuminate\Http\JsonResponse
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json(["error" => 'The provided credentials do not match our records.'], 401);
        }

        $user = Auth::user();

        $user->tokens()->delete();
        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' =>  $user
        ]);
    }
}
