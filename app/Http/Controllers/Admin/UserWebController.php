<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mountain;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserWebController extends Controller
{
    public function __construct()
    {
        // Only admin can access user management
        if (auth()->check() && auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat mengelola pengguna.');
        }
    }

    public function index(Request $request)
    {
        $query = User::with('userRole');

        if ($request->filled('role')) {
            $query->whereHas('userRole', fn($q) => $q->where('name', $request->role));
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $users = $query->select('users.id', 'users.name', 'users.email', 'users.phone', 'users.role_id', 'users.created_at')
            ->join('user_roles', 'users.role_id', '=', 'user_roles.id')
            ->orderBy('user_roles.name')->orderBy('users.name')
            ->paginate(20)->withQueryString();

        $roleCounts = User::join('user_roles', 'users.role_id', '=', 'user_roles.id')
            ->selectRaw('user_roles.name as role, count(*) as total')
            ->groupBy('user_roles.name')
            ->pluck('total', 'role');

        $roles = UserRole::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roleCounts', 'roles'));
    }

    public function create()
    {
        $pengelolaList = Mountain::with('pengelola')->orderBy('name')->get(['id', 'name', 'pengelola_id']);
        return view('admin.users.create', compact('pengelolaList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255|unique:users',
            'phone'       => 'nullable|string|max:20',
            'role'        => 'required|in:pengelola_tn,officer',
            'password'    => ['required', Password::defaults()],
            'mountain_id' => 'nullable|exists:mountains,id',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign mountain to pengelola_tn
        if ($validated['role'] === 'pengelola_tn' && !empty($validated['mountain_id'])) {
            Mountain::where('id', $validated['mountain_id'])
                ->update(['pengelola_id' => $user->id]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$validated['name']} ({$validated['role']}) berhasil dibuat.");
    }

    public function show($id)
    {
        $user = User::with(['userRole', 'managedMountain'])->withCount(['bookings'])->findOrFail($id);

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

        $user->role = $validated['role'];
        $user->save();

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
