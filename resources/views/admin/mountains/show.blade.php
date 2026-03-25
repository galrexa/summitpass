<x-layouts.web>
    <x-slot:title>{{ $mountain->name }}</x-slot:title>
    <x-slot:breadcrumb>['Admin', 'Gunung & Jalur', $mountain->name]</x-slot:breadcrumb>

    {{-- Header --}}
    <div class="flex items-start justify-between mb-5 gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h2 class="text-xl font-bold" style="color:var(--color-text);">{{ $mountain->name }}</h2>
                @if($mountain->is_active)
                <span class="badge badge-green">Aktif</span>
                @else
                <span class="badge badge-gray">Nonaktif</span>
                @endif
                <span class="badge {{ ['Easy'=>'badge-green','Moderate'=>'badge-yellow','Hard'=>'badge-red'][$mountain->difficulty] ?? 'badge-gray' }}">{{ $mountain->difficulty }}</span>
            </div>
            <p class="text-sm" style="color:var(--color-text-muted);">
                {{ $mountain->location }}{{ $mountain->province ? ', '.$mountain->province : '' }}
                &nbsp;&middot;&nbsp; {{ number_format($mountain->height_mdpl) }} mdpl
            </p>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('admin.mountains.edit', $mountain->id) }}" class="btn btn-outline btn-sm">Edit</a>
            <form method="POST" action="{{ route('admin.mountains.destroy', $mountain->id) }}"
                  onsubmit="return confirm('Hapus gunung ini beserta semua jalur dan datanya?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;">Hapus</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

        {{-- Regulasi --}}
        <div class="card">
            <h3 class="font-semibold text-sm mb-3" style="color:var(--color-text);">Regulasi</h3>
            @if($mountain->regulation)
            @php $reg = $mountain->regulation; @endphp
            <div class="flex flex-col gap-2">
                <div class="flex justify-between text-sm">
                    <span style="color:var(--color-text-muted);">Harga dasar</span>
                    <span class="font-semibold">Rp {{ number_format($reg->base_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span style="color:var(--color-text-muted);">Kuota/jalur/hari</span>
                    <span class="font-semibold">{{ $reg->quota_per_trail_per_day }} orang</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span style="color:var(--color-text-muted);">Maks. hari pendakian</span>
                    <span class="font-semibold">{{ $reg->max_hiking_days }} hari</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span style="color:var(--color-text-muted);">Maks. peserta/booking</span>
                    <span class="font-semibold">{{ $reg->max_participants_per_account }} orang</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span style="color:var(--color-text-muted);">Batas checkout</span>
                    <span class="font-semibold">Pukul {{ str_pad($reg->checkout_deadline_hour, 2, '0', STR_PAD_LEFT) }}:00</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span style="color:var(--color-text-muted);">Guide wajib</span>
                    <span class="font-semibold">{{ $reg->guide_required ? 'Ya' : 'Tidak' }}</span>
                </div>
            </div>
            @else
            <p class="text-sm" style="color:#dc2626;">Regulasi belum dikonfigurasi. <a href="{{ route('admin.mountains.edit', $mountain->id) }}" style="color:var(--color-forest-700);">Edit sekarang</a></p>
            @endif
        </div>

        {{-- Deskripsi --}}
        <div class="card lg:col-span-2">
            <h3 class="font-semibold text-sm mb-3" style="color:var(--color-text);">Deskripsi</h3>
            @if($mountain->description)
            <p class="text-sm" style="color:var(--color-text-muted);line-height:1.6;">{{ $mountain->description }}</p>
            @else
            <p class="text-sm" style="color:var(--color-text-muted);">Belum ada deskripsi.</p>
            @endif
            @if($mountain->image_url)
            <div class="mt-3">
                <img src="{{ $mountain->image_url }}" alt="{{ $mountain->name }}"
                     style="max-height:160px;border-radius:8px;object-fit:cover;" loading="lazy">
            </div>
            @endif
        </div>
    </div>

    {{-- Jalur Pendakian --}}
    <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-sm" style="color:var(--color-text);">Jalur Pendakian ({{ $mountain->trails->count() }})</h3>
        <button onclick="document.getElementById('form-add-trail').classList.toggle('hidden')" class="btn btn-outline btn-sm" style="gap:0.4rem;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah Jalur
        </button>
    </div>

    {{-- Form tambah jalur --}}
    <div id="form-add-trail" class="card mb-4 hidden">
        <h4 class="font-semibold text-sm mb-3" style="color:var(--color-text);">Tambah Jalur Baru</h4>
        <form method="POST" action="{{ route('admin.mountains.trails.store', $mountain->id) }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-1">
                    <label class="form-label">Nama Jalur <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="name" class="form-input" placeholder="cth. Ranu Pane" required>
                </div>
                <div>
                    <label class="form-label">Urutan Rute <span style="color:#dc2626;">*</span></label>
                    <input type="number" name="route_order" class="form-input" value="{{ $mountain->trails->count() + 1 }}" min="1" required>
                </div>
                <div>
                    <label class="form-label">Deskripsi</label>
                    <input type="text" name="description" class="form-input" placeholder="Opsional">
                </div>
            </div>
            <div class="flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary btn-sm">Simpan Jalur</button>
                <button type="button" onclick="document.getElementById('form-add-trail').classList.add('hidden')" class="btn btn-ghost btn-sm">Batal</button>
            </div>
        </form>
    </div>

    {{-- Daftar jalur --}}
    @forelse($mountain->trails->sortBy('route_order') as $trail)
    <div class="card mb-4" style="padding:0;overflow:hidden;">
        {{-- Trail header --}}
        <div class="flex items-center justify-between px-4 py-3" style="background:var(--color-forest-50);border-bottom:1px solid var(--color-border);">
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold px-2 py-0.5 rounded" style="background:var(--color-forest-700);color:white;">#{{ $trail->route_order }}</span>
                <span class="font-semibold text-sm" style="color:var(--color-text);">{{ $trail->name }}</span>
                @if(!$trail->is_active)
                <span class="badge badge-gray" style="font-size:0.65rem;">Nonaktif</span>
                @endif
                <span class="text-xs" style="color:var(--color-text-muted);">{{ $trail->checkpoints->count() }} pos</span>
            </div>
            <div class="flex gap-1">
                <button onclick="document.getElementById('edit-trail-{{ $trail->id }}').classList.toggle('hidden')" class="btn btn-ghost btn-sm btn-icon" title="Edit jalur">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </button>
                <button onclick="document.getElementById('add-checkpoint-{{ $trail->id }}').classList.toggle('hidden')" class="btn btn-ghost btn-sm btn-icon" title="Tambah pos">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                </button>
                <form method="POST" action="{{ route('admin.mountains.trails.destroy', [$mountain->id, $trail->id]) }}"
                      onsubmit="return confirm('Hapus jalur {{ addslashes($trail->name) }} beserta semua posnya?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-ghost btn-sm btn-icon" title="Hapus jalur" style="color:#dc2626;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3,6 5,6 21,6"/><path d="M19 6l-1 14H6L5 6"/>
                            <path d="M10 11v6"/><path d="M14 11v6"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Form edit jalur --}}
        <div id="edit-trail-{{ $trail->id }}" class="hidden px-4 py-3" style="background:#f9fafb;border-bottom:1px solid var(--color-border);">
            <form method="POST" action="{{ route('admin.mountains.trails.update', [$mountain->id, $trail->id]) }}">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 items-end">
                    <div>
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" value="{{ $trail->name }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Urutan</label>
                        <input type="number" name="route_order" value="{{ $trail->route_order }}" class="form-input" min="1" required>
                    </div>
                    <div>
                        <label class="form-label">Deskripsi</label>
                        <input type="text" name="description" value="{{ $trail->description }}" class="form-input">
                    </div>
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer mb-2">
                            <input type="checkbox" name="is_active" value="1" {{ $trail->is_active ? 'checked' : '' }}
                                   style="width:14px;height:14px;accent-color:var(--color-forest-600);">
                            <span class="text-sm">Aktif</span>
                        </label>
                        <button type="submit" class="btn btn-primary btn-sm w-full">Simpan</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Form tambah checkpoint --}}
        <div id="add-checkpoint-{{ $trail->id }}" class="hidden px-4 py-3" style="background:#f0fdf4;border-bottom:1px solid var(--color-border);">
            <p class="text-xs font-semibold mb-2" style="color:var(--color-forest-700);">Tambah Pos ke Jalur {{ $trail->name }}</p>
            <form method="POST" action="{{ route('admin.mountains.checkpoints.store', [$mountain->id, $trail->id]) }}">
                @csrf
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 mb-2">
                    <div>
                        <label class="form-label">Nama Pos <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="name" class="form-input" placeholder="cth. Pos 1 Landengan Dowo" required>
                    </div>
                    <div>
                        <label class="form-label">Tipe <span style="color:#dc2626;">*</span></label>
                        <select name="type" class="form-input" required>
                            <option value="gate_in">Gate In (Pintu Masuk)</option>
                            <option value="pos">Pos (Pemberhentian)</option>
                            <option value="summit">Puncak</option>
                            <option value="gate_out">Gate Out (Pintu Keluar)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Urutan <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="order_seq" class="form-input" value="{{ $trail->checkpoints->count() + 1 }}" min="1" required>
                    </div>
                    <div>
                        <label class="form-label">Deskripsi</label>
                        <input type="text" name="description" class="form-input" placeholder="Opsional">
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-3">
                    <div>
                        <label class="form-label">Latitude <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="latitude" class="form-input" step="any" placeholder="-8.1060" required>
                    </div>
                    <div>
                        <label class="form-label">Longitude <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="longitude" class="form-input" step="any" placeholder="112.9063" required>
                    </div>
                    <div>
                        <label class="form-label">Ketinggian (m dpl)</label>
                        <input type="number" name="altitude" class="form-input" placeholder="cth. 2100">
                    </div>
                    <div>
                        <label class="form-label">Est. Waktu dari Pos Sebelumnya (menit)</label>
                        <input type="number" name="estimated_duration_minutes" class="form-input" placeholder="cth. 90" min="1">
                        <p class="text-xs mt-0.5" style="color:var(--color-text-muted);">Kosongkan untuk pos pertama</p>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Tambah Pos</button>
            </form>
        </div>

        {{-- Daftar checkpoint --}}
        @if($trail->checkpoints->count() > 0)
        <div style="overflow-x:auto;">
            <table class="data-table" style="font-size:0.8rem;">
                <thead>
                    <tr>
                        <th style="width:36px;">#</th>
                        <th>Nama Pos</th>
                        <th>Tipe</th>
                        <th>Koordinat</th>
                        <th>Ketinggian</th>
                        <th>Est. Waktu</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trail->checkpoints->sortBy('order_seq') as $cp)
                    @php
                    $typeLabel = ['gate_in'=>'Gate In','pos'=>'Pos','summit'=>'Puncak','gate_out'=>'Gate Out'][$cp->type] ?? $cp->type;
                    $typeBadge = ['gate_in'=>'badge-blue','pos'=>'badge-gray','summit'=>'badge-yellow','gate_out'=>'badge-blue'][$cp->type] ?? 'badge-gray';
                    @endphp
                    <tr>
                        <td class="text-center font-mono font-bold">{{ $cp->order_seq }}</td>
                        <td>
                            <div class="font-medium">{{ $cp->name }}</div>
                            @if($cp->description)
                            <div class="text-xs" style="color:var(--color-text-muted);">{{ $cp->description }}</div>
                            @endif
                        </td>
                        <td><span class="badge {{ $typeBadge }}">{{ $typeLabel }}</span></td>
                        <td class="font-mono text-xs" style="color:var(--color-text-muted);">
                            {{ number_format($cp->latitude, 5) }},<br>{{ number_format($cp->longitude, 5) }}
                        </td>
                        <td class="text-sm">
                            {{ $cp->altitude ? number_format($cp->altitude).' m dpl' : '—' }}
                        </td>
                        <td class="text-sm">
                            @if($cp->estimated_duration_minutes)
                                @php
                                    $h = intdiv($cp->estimated_duration_minutes, 60);
                                    $m = $cp->estimated_duration_minutes % 60;
                                @endphp
                                @if($h > 0){{ $h }}j @endif{{ $m > 0 ? $m.'m' : '' }}
                                <div class="text-xs" style="color:var(--color-text-muted);">dari pos sblm.</div>
                            @else
                                <span style="color:var(--color-text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.mountains.checkpoints.destroy', [$mountain->id, $trail->id, $cp->id]) }}"
                                  onsubmit="return confirm('Hapus pos {{ addslashes($cp->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-icon btn-sm" title="Hapus pos" style="color:#dc2626;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3,6 5,6 21,6"/><path d="M19 6l-1 14H6L5 6"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-4 py-3 text-sm" style="color:var(--color-text-muted);">
            Belum ada pos/checkpoint.
            <button onclick="document.getElementById('add-checkpoint-{{ $trail->id }}').classList.remove('hidden')"
                    style="color:var(--color-forest-700);font-weight:600;background:none;border:none;cursor:pointer;">Tambah sekarang</button>
        </div>
        @endif
    </div>
    @empty
    <div class="card text-center py-8">
        <p class="text-sm" style="color:var(--color-text-muted);">Belum ada jalur pendakian.</p>
        <button onclick="document.getElementById('form-add-trail').classList.remove('hidden');document.getElementById('form-add-trail').scrollIntoView({behavior:'smooth'})"
                class="btn btn-outline btn-sm mt-3">Tambah Jalur Pertama</button>
    </div>
    @endforelse

</x-layouts.web>
