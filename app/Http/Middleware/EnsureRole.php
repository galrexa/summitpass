<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Pastikan user memiliki salah satu dari role yang diizinkan.
     *
     * Penggunaan di routes:
     *   Route::middleware('role:admin')
     *   Route::middleware('role:admin,pengelola_tn')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        return $next($request);
    }
}
