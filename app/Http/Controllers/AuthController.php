<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Register new user (pendaki)
     * Wajib mengisi NIK (WNI) atau passport_number (WNA)
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'phone'    => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $roleId = \App\Models\UserRole::where('name', 'pendaki')->value('id');

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role_id'  => $roleId,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message'      => 'Registrasi berhasil',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ], 201);
    }

    private function redirectAfterLogin()
    {
        $role = Auth::user()->role;
        if (in_array($role, ['admin', 'pengelola_tn'])) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('pendaki.bookings');
    }

    /**
     * Show registration form (web)
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectAfterLogin();
        }

        return view('auth.register');
    }

    /**
     * Handle web registration form submission
     */
    public function webRegister(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'phone'    => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $roleId = \App\Models\UserRole::where('name', 'pendaki')->value('id');

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role_id'  => $roleId,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('profile.setup')->with('success', 'Akun berhasil dibuat! Lengkapi profilmu untuk bisa booking SIMAKSI.');
    }

    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::error('Google OAuth error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('login')->withErrors(['email' => 'Login dengan Google gagal. Silakan coba lagi.']);
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Update google_id jika login lewat email biasa sebelumnya
            if (! $user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                ]);
            }
        } else {
            // Buat akun baru via Google (NIK/paspor kosong, wajib dilengkapi nanti)
            $roleId = \App\Models\UserRole::where('name', 'pendaki')->value('id');

            $user = User::create([
                'name'      => $googleUser->getName(),
                'email'     => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
                'password'  => null,
                'role_id'   => $roleId,
            ]);
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        // New Google user — prompt to complete profile
        if (empty($user->nik) && empty($user->passport_number) && $user->role === 'pendaki') {
            return redirect()->route('profile.setup')->with('success', 'Login berhasil! Lengkapi profilmu untuk bisa booking SIMAKSI.');
        }

        return $this->redirectAfterLogin();
    }

    /**
     * Show login form (web)
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectAfterLogin();
        }

        return view('auth.login');
    }

    /**
     * Handle web login form submission
     */
    public function webLogin(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($validated, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        $request->session()->regenerate();

        return $this->redirectAfterLogin();
    }

    /**
     * Handle web logout
     */
    public function webLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    /**
     * User login (API)
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
