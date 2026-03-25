<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserWebController extends Controller
{
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
            ->orderBy('role')->orderBy('name')
            ->paginate(20)->withQueryString();

        $roleCounts = User::selectRaw('role, count(*) as total')
            ->groupBy('role')->pluck('total', 'role');

        return view('admin.users.index', compact('users', 'roleCounts'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:pengelola_tn,officer',
            'password' => ['required', Password::defaults()],
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$validated['name']} ({$validated['role']}) berhasil dibuat.");
    }

    public function show($id)
    {
        $user = User::withCount(['bookings'])->findOrFail($id);

        $recentBookings = $user->bookings()
            ->with(['mountain', 'trail'])
            ->latest()->limit(5)->get();

        return view('admin.users.show', compact('user', 'recentBookings'));
    }

    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa mengubah role akun sendiri.');
        }

        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak bisa mengubah role admin lain.');
        }

        $validated = $request->validate([
            'role' => 'required|in:pendaki,pengelola_tn,officer',
        ]);

        $user->update(['role' => $validated['role']]);

        return back()->with('success', "Role {$user->name} diubah menjadi {$validated['role']}.");
    }

    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak bisa menghapus akun admin lain.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$name} berhasil dihapus.");
    }
}
