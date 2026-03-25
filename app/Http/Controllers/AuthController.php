<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Register new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'hiker',
            'level' => 1,
        ]);

        $token = Auth::login($user);

        return response()->json([
            'message' => 'User registered successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => $user,
        ], 201);
    }

    /**
     * User login
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($validated)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();
        $token = Auth::login($user);

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => $user,
        ], 200);
    }

    /**
     * Get current user profile
     */
    public function profile()
    {
        return response()->json([
            'user' => Auth::user(),
        ], 200);
    }

    /**
     * Refresh JWT token
     */
    public function refresh()
    {
        $token = Auth::refresh();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
        ], 200);
    }

    /**
     * User logout
     */
    public function logout()
    {
        Auth::logout();

        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'current_password' => 'sometimes|string',
            'password' => 'sometimes|confirmed|min:8',
        ]);

        $user = Auth::user();

        // Verify current password if changing password
        if ($request->filled('password')) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect',
                ], 422);
            }
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ], 200);
    }
}