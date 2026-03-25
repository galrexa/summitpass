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
     * Register new user (pendaki)
     * Wajib mengisi NIK (WNI) atau passport_number (WNA)
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|string|email|max:255|unique:users',
            'phone'           => 'nullable|string|max:20',
            'nik'             => 'nullable|string|size:16|unique:users|regex:/^[0-9]{16}$/',
            'passport_number' => 'nullable|string|max:20|unique:users',
            'password'        => ['required', 'confirmed', Password::defaults()],
        ]);

        if (empty($validated['nik']) && empty($validated['passport_number'])) {
            return response()->json([
                'message' => 'NIK atau nomor paspor wajib diisi.',
            ], 422);
        }

        $user = User::create([
            'name'            => $validated['name'],
            'email'           => $validated['email'],
            'phone'           => $validated['phone'] ?? null,
            'nik'             => $validated['nik'] ?? null,
            'passport_number' => $validated['passport_number'] ?? null,
            'password'        => Hash::make($validated['password']),
            'role'            => 'pendaki',
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message'      => 'Registrasi berhasil',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ], 201);
    }

    /**
     * User login
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($validated)) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        $user  = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message'      => 'Login berhasil',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ], 200);
    }

    /**
     * Get current user profile
     */
    public function profile(Request $request)
    {
        return response()->json(['user' => $request->user()], 200);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 200);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil'], 200);
    }

    /**
     * Update profile (name, phone, password)
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'sometimes|string|max:255',
            'phone'            => 'sometimes|string|max:20',
            'current_password' => 'sometimes|string',
            'password'         => 'sometimes|confirmed|min:8',
        ]);

        $user = $request->user();

        if ($request->filled('password')) {
            if (!Hash::check($validated['current_password'] ?? '', $user->password)) {
                return response()->json(['message' => 'Password saat ini salah'], 422);
            }
            $validated['password'] = Hash::make($validated['password']);
        }

        unset($validated['current_password']);
        $user->update($validated);

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'user'    => $user->fresh(),
        ], 200);
    }
}
