<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the profile completion form.
     */
    public function showSetup()
    {
        $user = Auth::user();

        return view('profile.setup', compact('user'));
    }

    /**
     * Save profile identity data (NIK or passport).
     */
    public function saveSetup(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nationality'     => 'required|in:wni,wna',
            'nik'             => [
                Rule::requiredIf($request->nationality === 'wni'),
                'nullable', 'string', 'size:16', 'regex:/^[0-9]{16}$/',
                Rule::unique('users')->ignore($user->id),
            ],
            'passport_number' => [
                Rule::requiredIf($request->nationality === 'wna'),
                'nullable', 'string', 'max:20',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'nik'             => $validated['nik'] ?? null,
            'passport_number' => $validated['passport_number'] ?? null,
            'phone'           => $validated['phone'] ?? $user->phone,
        ]);

        return redirect()->route('pendaki.bookings')->with('success', 'Profil berhasil dilengkapi!');
    }
}
