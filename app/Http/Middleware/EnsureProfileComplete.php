<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    /**
     * Redirect pendaki ke halaman lengkapi profil jika NIK/paspor belum diisi.
     * Digunakan pada rute yang membutuhkan identitas (misal: booking).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'pendaki' && empty($user->nik) && empty($user->passport_number)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Lengkapi profil terlebih dahulu sebelum melanjutkan.',
                    'redirect' => route('profile.setup'),
                ], 403);
            }

            return redirect()->route('profile.setup')
                ->with('warning', 'Lengkapi identitas dirimu terlebih dahulu sebelum melakukan booking.');
        }

        return $next($request);
    }
}
