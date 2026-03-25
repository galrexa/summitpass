<x-layouts.web>
    <x-slot:title>{{ $user->name }}</x-slot:title>
    <x-slot:breadcrumb>['Admin', 'Pengguna', $user->name]</x-slot:breadcrumb>

    @php
    $roleMap = [
        'admin'        => ['badge-red',    'Admin'],
        'pengelola_tn' => ['badge-blue',   'Pengelola TN'],
        'officer'      => ['badge-green',  'Officer'],
        'pendaki'      => ['badge-gray',   'Pendaki'],
    ];
    [$badge, $label] = $roleMap[$user->role] ?? ['badge-gray', $user->role];
    @endphp

    <div class="flex items-start justify-between mb-5 gap-4 flex-wrap">
        <div class="flex items-center gap-4">
            <div class="avatar" style="width:52px;height:52px;font-size:1.1rem;">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h2 class="text-xl font-bold" style="color:var(--color-text);">{{ $user->name }}</h2>
                    <span class="badge {{ $badge }}">{{ $label }}</span>
                </div>
                <p class="text-sm" style="color:var(--color-text-muted);">
                    {{ $user->email }}
                    @if($user->phone) &middot; {{ $user->phone }} @endif
                </p>
            </div>
        </div>
        <div class="flex gap-2 flex-wrap">
            @if($user->id !== auth()->id() && $user->role !== 'admin')
            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                  onsubmit="return confirm('Hapus akun {{ addslashes($user->name) }}? Semua data booking terkait tetap tersimpan.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;">
                    Hapus Akun
                </button>
            </form>
            @endif
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Kiri: info & ganti role --}}
        <div class="flex flex-col gap-5">

            <div class="card">
                <h3 class="font-semibold text-sm mb-3" style="color:var(--color-text);">Info Akun</h3>
                <div class="flex flex-col gap-2.5">
                    <div class="flex justify-between text-sm">
                        <span style="color:var(--color-text-muted);">ID</span>
                        <span class="font-mono">#{{ $user->id }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span style="color:var(--color-text-muted);">Bergabung</span>
                        <span>{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span style="color:var(--color-text-muted);">Total Booking</span>
                        <span class="font-semibold">{{ $user->bookings_count }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span style="color:var(--color-text-muted);">Login via</span>
                        <span>{{ $user->google_id ? 'Google' : 'Email' }}</span>
                    </div>
                </div>
            </div>

            {{-- Ganti Role --}}
            @if($user->id !== auth()->id() && $user->role !== 'admin')
            <div class="card">
                <h3 class="font-semibold text-sm mb-3" style="color:var(--color-text);">Ubah Role</h3>
                <form method="POST" action="{{ route('admin.users.update-role', $user->id) }}">
                    @csrf @method('PATCH')
                    <div class="flex flex-col gap-3">
                        @foreach(['pendaki' => 'Pendaki', 'pengelola_tn' => 'Pengelola TN', 'officer' => 'Officer'] as $val => $lbl)
                        <label class="flex items-center gap-2.5 cursor-pointer p-2 rounded-lg transition-colors"
                               style="{{ $user->role === $val ? 'background:var(--color-forest-50);' : '' }}">
                            <input type="radio" name="role" value="{{ $val }}"
                                   {{ $user->role === $val ? 'checked' : '' }}
                                   style="accent-color:var(--color-forest-600);width:15px;height:15px;">
                            <span class="text-sm font-medium" style="color:var(--color-text);">{{ $lbl }}</span>
                            @if($user->role === $val)
                            <span class="text-xs ml-auto" style="color:var(--color-forest-600);">Saat ini</span>
                            @endif
                        </label>
                        @endforeach
                        <button type="submit" class="btn btn-outline btn-sm mt-1"
                                onclick="return confirm('Ubah role {{ addslashes($user->name) }}?')">
                            Simpan Role
                        </button>
                    </div>
                </form>
            </div>
            @elseif($user->id === auth()->id())
            <div class="card">
                <p class="text-sm" style="color:var(--color-text-muted);">Ini adalah akun Anda sendiri. Role tidak dapat diubah dari sini.</p>
            </div>
            @else
            <div class="card">
                <p class="text-sm" style="color:var(--color-text-muted);">Role admin tidak dapat diubah.</p>
            </div>
            @endif

        </div>

        {{-- Kanan: riwayat booking --}}
        <div class="lg:col-span-2">
            <div class="card" style="padding:0;overflow:hidden;">
                <div class="px-5 py-4" style="border-bottom:1px solid var(--color-border);">
                    <h3 class="font-semibold text-sm" style="color:var(--color-text);">
                        Riwayat Booking ({{ $user->bookings_count }})
                    </h3>
                </div>

                @if($recentBookings->isEmpty())
                <div class="px-5 py-8 text-center text-sm" style="color:var(--color-text-muted);">
                    Belum ada booking.
                </div>
                @else
                <div style="overflow-x:auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Gunung & Jalur</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $statusMap = [
                                'pending_payment' => ['badge-yellow', 'Pending'],
                                'paid'            => ['badge-blue',   'Dibayar'],
                                'active'          => ['badge-green',  'Aktif'],
                                'completed'       => ['badge-gray',   'Selesai'],
                                'cancelled'       => ['badge-red',    'Dibatalkan'],
                            ];
                            @endphp
                            @foreach($recentBookings as $booking)
                            @php [$sc, $sl] = $statusMap[$booking->status] ?? ['badge-gray', $booking->status]; @endphp
                            <tr>
                                <td>
                                    <span class="font-mono text-xs font-bold" style="color:var(--color-forest-700);">
                                        {{ $booking->booking_code ?? '#'.$booking->id }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-sm font-medium">{{ $booking->mountain?->name ?? '—' }}</div>
                                    <div class="text-xs" style="color:var(--color-text-muted);">{{ $booking->trail?->name }}</div>
                                </td>
                                <td class="text-xs" style="color:var(--color-text-muted);">
                                    {{ $booking->start_date?->format('d M Y') }}
                                </td>
                                <td><span class="badge {{ $sc }}">{{ $sl }}</span></td>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking->id) }}"
                                       class="btn btn-ghost btn-icon btn-sm" title="Detail Booking">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9,18 15,12 9,6"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($user->bookings_count > 5)
                <div class="px-5 py-3 text-xs" style="border-top:1px solid var(--color-border);color:var(--color-text-muted);">
                    Menampilkan 5 booking terbaru dari total {{ $user->bookings_count }}.
                </div>
                @endif
                @endif
            </div>
        </div>

    </div>

</x-layouts.web>
