<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    /**
     * Daftar semua user, bisa difilter per role
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $users = $query->select('id', 'name', 'email', 'phone', 'role', 'created_at')
            ->orderBy('role')
            ->orderBy('name')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page'    => $users->lastPage(),
                'per_page'     => $users->perPage(),
                'total'        => $users->total(),
            ],
        ], 200);
    }

    /**
     * Buat user baru dengan role pengelola_tn atau officer (admin only)
     * Role pendaki tidak boleh dibuat lewat endpoint ini — gunakan /auth/register
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:pengelola_tn,officer',
            'password' => ['required', Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => "User {$validated['role']} berhasil dibuat",
            'data'    => $user->only('id', 'name', 'email', 'phone', 'role', 'created_at'),
        ], 201);
    }

    /**
     * Detail user
     */
    public function show($id)
    {
        $user = User::select('id', 'name', 'email', 'phone', 'role', 'created_at')
            ->find($id);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        return response()->json(['data' => $user], 200);
    }

    /**
     * Ubah role user (admin only)
     * Tidak bisa mengubah role admin lain
     */
    public function updateRole(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Tidak bisa mengubah role akun sendiri'], 422);
        }

        if ($user->role === 'admin') {
            return response()->json(['message' => 'Tidak bisa mengubah role admin lain'], 403);
        }

        $validated = $request->validate([
            'role' => 'required|in:pendaki,pengelola_tn,officer',
        ]);

        $user->update(['role' => $validated['role']]);

        return response()->json([
            'message' => "Role user berhasil diubah menjadi {$validated['role']}",
            'data'    => $user->fresh()->only('id', 'name', 'email', 'role'),
        ], 200);
    }

    /**
     * Hapus user (admin only)
     * Tidak bisa menghapus sesama admin atau diri sendiri
     */
    public function destroy(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Tidak bisa menghapus akun sendiri'], 422);
        }

        if ($user->role === 'admin') {
            return response()->json(['message' => 'Tidak bisa menghapus akun admin lain'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User berhasil dihapus'], 200);
    }
}
