<x-layouts.web>
    <x-slot:title>Manajemen Gunung</x-slot:title>
    <x-slot:breadcrumb>['Admin', 'Gunung & Jalur']</x-slot:breadcrumb>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-semibold" style="color:var(--color-text);">Gunung & Jalur</h2>
            <p class="text-sm mt-0.5" style="color:var(--color-text-muted);">Kelola data gunung, regulasi, jalur, dan pos pendakian.</p>
        </div>
        <a href="{{ route('admin.mountains.create') }}" class="btn btn-primary" style="gap:0.5rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah Gunung
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" class="card mb-5" style="padding:1rem;">
        <div class="flex flex-wrap gap-3 items-end">
            <div style="flex:1;min-width:200px;">
                <label class="form-label">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Nama atau lokasi gunung...">
            </div>
            <div>
                <label class="form-label">Tingkat Kesulitan</label>
                <select name="difficulty" class="form-input">
                    <option value="">Semua</option>
                    <option value="Easy" {{ request('difficulty') === 'Easy' ? 'selected' : '' }}>Easy</option>
                    <option value="Moderate" {{ request('difficulty') === 'Moderate' ? 'selected' : '' }}>Moderate</option>
                    <option value="Hard" {{ request('difficulty') === 'Hard' ? 'selected' : '' }}>Hard</option>
                </select>
            </div>
            <button type="submit" class="btn btn-outline">Filter</button>
            @if(request('search') || request('difficulty'))
            <a href="{{ route('admin.mountains.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Gunung</th>
                        <th>Lokasi</th>
                        <th>Ketinggian</th>
                        <th>Kesulitan</th>
                        <th>Jalur</th>
                        <th>Kuota/Jalur/Hari</th>
                        <th>Harga Dasar</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mountains as $mountain)
                    @php
                    $diffColor = ['Easy' => 'badge-green', 'Moderate' => 'badge-yellow', 'Hard' => 'badge-red'][$mountain->difficulty] ?? 'badge-gray';
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('admin.mountains.show', $mountain->id) }}"
                               class="font-medium text-sm hover:underline"
                               style="color:var(--color-forest-700);">{{ $mountain->name }}</a>
                        </td>
                        <td>
                            <div class="text-sm">{{ $mountain->location }}</div>
                            @if($mountain->province)
                            <div class="text-xs" style="color:var(--color-text-muted);">{{ $mountain->province }}</div>
                            @endif
                        </td>
                        <td class="text-sm">{{ number_format($mountain->height_mdpl) }} mdpl</td>
                        <td><span class="badge {{ $diffColor }}">{{ $mountain->difficulty }}</span></td>
                        <td class="text-sm">{{ $mountain->trails_count }}</td>
                        <td class="text-sm">{{ $mountain->regulation?->quota_per_trail_per_day ?? '—' }}</td>
                        <td class="text-sm">
                            @if($mountain->regulation)
                            Rp {{ number_format($mountain->regulation->base_price, 0, ',', '.') }}
                            @else
                            <span style="color:var(--color-text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            @if($mountain->is_active)
                            <span class="badge badge-green">Aktif</span>
                            @else
                            <span class="badge badge-gray">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.mountains.show', $mountain->id) }}" class="btn btn-ghost btn-icon btn-sm" title="Detail & Jalur">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.mountains.edit', $mountain->id) }}" class="btn btn-ghost btn-icon btn-sm" title="Edit">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.mountains.destroy', $mountain->id) }}"
                                      onsubmit="return confirm('Hapus gunung {{ addslashes($mountain->name) }}? Semua jalur dan data terkait akan ikut terhapus.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-icon btn-sm" title="Hapus" style="color:#dc2626;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3,6 5,6 21,6"/><path d="M19 6l-1 14H6L5 6"/>
                                            <path d="M10 11v6"/><path d="M14 11v6"/>
                                            <path d="M9 6V4h6v2"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:3rem;color:var(--color-text-muted);">
                            Belum ada data gunung.
                            <a href="{{ route('admin.mountains.create') }}" style="color:var(--color-forest-700);font-weight:600;">Tambah sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($mountains->hasPages())
        <div class="px-5 py-3" style="border-top:1px solid var(--color-border);">
            {{ $mountains->links() }}
        </div>
        @endif
    </div>

</x-layouts.web>
