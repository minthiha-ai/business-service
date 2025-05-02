<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('UserToken')->accessToken;

        return response()->json([
            'message' => 'User registered successfully.',
            'token' => $token,
            'user'    => $user,
        ], 201);
    }

    /**
     * Login and issue access token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.'
            ], 401);
        }

        $token = $user->createToken('UserToken')->accessToken;

        return response()->json([
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => $user
        ]);
    }

    /**
     * Logout (revoke current token)
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }

    /**
     * Get current authenticated user
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
