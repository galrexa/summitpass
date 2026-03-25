<x-layouts.web>
    <x-slot:title>Manajemen Pengguna</x-slot:title>
    <x-slot:breadcrumb>['Admin', 'Pengguna']</x-slot:breadcrumb>

    @php
    $roleMap = [
        'admin'        => ['badge-red',    'Admin'],
        'pengelola_tn' => ['badge-blue',   'Pengelola TN'],
        'officer'      => ['badge-green',  'Officer'],
        'pendaki'      => ['badge-gray',   'Pendaki'],
    ];
    @endphp

    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-semibold" style="color:var(--color-text);">Pengguna</h2>
            <p class="text-sm mt-0.5" style="color:var(--color-text-muted);">Kelola akun pengelola, officer, dan pendaki.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary" style="gap:0.5rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah Akun
        </a>
    </div>

    {{-- Role pills --}}
    <div class="flex flex-wrap gap-2 mb-5">
        <a href="{{ route('admin.users.index', request()->except(['role','page'])) }}"
           class="px-3 py-1.5 rounded-full text-xs font-semibold"
           style="{{ !request('role') ? 'background:var(--color-forest-700);color:white;' : 'background:var(--color-forest-50);color:var(--color-forest-700);border:1px solid var(--color-forest-200);' }}">
            Semua ({{ $roleCounts->sum() }})
        </a>
        @foreach($roleMap as $key => [$badge, $label])
        <a href="{{ route('admin.users.index', array_merge(request()->except(['role','page']), ['role' => $key])) }}"
           class="px-3 py-1.5 rounded-full text-xs font-semibold"
           style="{{ request('role') === $key ? 'background:var(--color-forest-700);color:white;border:none;' : 'background:white;color:var(--color-text-muted);border:1px solid var(--color-border);' }}">
            {{ $label }} ({{ $roleCounts[$key] ?? 0 }})
        </a>
        @endforeach
    </div>

    {{-- Filter --}}
    <form method="GET" class="card mb-5" style="padding:1rem;">
        <div class="flex flex-wrap gap-3 items-end">
            <div style="flex:1;min-width:200px;">
                <label class="form-label">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Nama atau email...">
            </div>
            @if(request('role'))
            <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            <button type="submit" class="btn btn-outline">Cari</button>
            @if(request('search'))
            <a href="{{ route('admin.users.index', request()->except(['search','page'])) }}" class="btn btn-ghost">Reset</a>
            @endif
        </div>
    </form>

    {{-- Tabel --}}
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Role</th>
                        <th>Bergabung</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    @php [$badge, $label] = $roleMap[$user->role] ?? ['badge-gray', $user->role]; @endphp
                    <tr>
                        <td>
                            <a href="{{ route('admin.users.show', $user->id) }}"
                               class="flex items-center gap-2 hover:underline" style="color:var(--color-text);text-decoration:none;">
                                <div class="avatar" style="width:30px;height:30px;font-size:0.7rem;flex-shrink:0;">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <span class="font-medium text-sm">{{ $user->name }}</span>
                            </a>
                        </td>
                        <td class="text-sm" style="color:var(--color-text-muted);">{{ $user->email }}</td>
                        <td class="text-sm">{{ $user->phone ?? '—' }}</td>
                        <td><span class="badge {{ $badge }}">{{ $label }}</span></td>
                        <td class="text-xs" style="color:var(--color-text-muted);">{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-ghost btn-icon btn-sm" title="Detail">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </a>
                                @if($user->id !== auth()->id() && $user->role !== 'admin')
                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                      onsubmit="return confirm('Hapus akun {{ addslashes($user->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-icon btn-sm" title="Hapus" style="color:#dc2626;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3,6 5,6 21,6"/><path d="M19 6l-1 14H6L5 6"/>
                                            <path d="M10 11v6"/><path d="M14 11v6"/>
                                            <path d="M9 6V4h6v2"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:3rem;color:var(--color-text-muted);">
                            Tidak ada pengguna ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-5 py-3" style="border-top:1px solid var(--color-border);">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</x-layouts.web>
