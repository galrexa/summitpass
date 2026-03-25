<x-layouts.web>
    <x-slot:title>Manajemen Booking</x-slot:title>
    <x-slot:breadcrumb>['Admin', 'Booking / SIMAKSI']</x-slot:breadcrumb>

    @php
    $statusMap = [
        'pending_payment' => ['class' => 'badge-yellow', 'label' => 'Pending Bayar'],
        'paid'            => ['class' => 'badge-blue',   'label' => 'Dibayar'],
        'active'          => ['class' => 'badge-green',  'label' => 'Aktif'],
        'completed'       => ['class' => 'badge-gray',   'label' => 'Selesai'],
        'cancelled'       => ['class' => 'badge-red',    'label' => 'Dibatalkan'],
    ];
    @endphp

    {{-- Header & stat ringkasan --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-semibold" style="color:var(--color-text);">Booking / SIMAKSI</h2>
            <p class="text-sm mt-0.5" style="color:var(--color-text-muted);">Semua pendaftaran pendakian masuk.</p>
        </div>
    </div>

    {{-- Status summary pills --}}
    <div class="flex flex-wrap gap-2 mb-5">
        <a href="{{ route('admin.bookings.index') }}"
           class="px-3 py-1.5 rounded-full text-xs font-semibold border transition-colors {{ !request('status') ? 'border-transparent' : 'border-transparent' }}"
           style="{{ !request('status') ? 'background:var(--color-forest-700);color:white;' : 'background:var(--color-forest-50);color:var(--color-forest-700);border-color:var(--color-forest-200);' }}">
            Semua ({{ $statusCounts->sum() }})
        </a>
        @foreach($statusMap as $key => $s)
        <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['status' => $key])) }}"
           class="px-3 py-1.5 rounded-full text-xs font-semibold border"
           style="{{ request('status') === $key ? 'background:var(--color-forest-700);color:white;border-color:transparent;' : 'background:white;color:var(--color-text-muted);border-color:var(--color-border);' }}">
            {{ $s['label'] }} ({{ $statusCounts[$key] ?? 0 }})
        </a>
        @endforeach
    </div>

    {{-- Filter --}}
    <form method="GET" class="card mb-5" style="padding:1rem;">
        <div class="flex flex-wrap gap-3 items-end">
            <div style="flex:2;min-width:180px;">
                <label class="form-label">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-input"
                       placeholder="Kode booking atau nama leader...">
            </div>
            <div style="min-width:160px;">
                <label class="form-label">Gunung</label>
                <select name="mountain_id" class="form-input">
                    <option value="">Semua Gunung</option>
                    @foreach($mountains as $m)
                    <option value="{{ $m->id }}" {{ request('mountain_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Tanggal Mulai (dari)</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Tanggal Mulai (sampai)</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input">
            </div>
            @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <button type="submit" class="btn btn-outline">Filter</button>
            @if(request()->hasAny(['search','mountain_id','date_from','date_to','status']))
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </div>
    </form>

    {{-- Tabel --}}
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode Booking</th>
                        <th>Leader</th>
                        <th>Gunung & Jalur</th>
                        <th>Tanggal Pendakian</th>
                        <th>Peserta</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    @php $s = $statusMap[$booking->status] ?? ['class'=>'badge-gray','label'=>$booking->status]; @endphp
                    <tr>
                        <td>
                            @if($booking->booking_code)
                            <span class="font-mono text-xs font-bold" style="color:var(--color-forest-700);">{{ $booking->booking_code }}</span>
                            @else
                            <span class="font-mono text-xs" style="color:var(--color-text-muted);">#{{ $booking->id }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-sm font-medium">{{ $booking->leader?->name ?? '—' }}</div>
                            <div class="text-xs" style="color:var(--color-text-muted);">{{ $booking->leader?->email }}</div>
                        </td>
                        <td>
                            <div class="text-sm font-medium">{{ $booking->mountain?->name ?? '—' }}</div>
                            <div class="text-xs" style="color:var(--color-text-muted);">{{ $booking->trail?->name }}</div>
                        </td>
                        <td>
                            <div class="text-sm">{{ $booking->start_date?->format('d M Y') }}</div>
                            <div class="text-xs" style="color:var(--color-text-muted);">s/d {{ $booking->end_date?->format('d M Y') }}</div>
                        </td>
                        <td class="text-sm text-center">{{ $booking->participants_count ?? '—' }}</td>
                        <td class="text-sm font-medium">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        <td><span class="badge {{ $s['class'] }}">{{ $s['label'] }}</span></td>
                        <td>
                            @if($booking->payment)
                                @if($booking->payment->status === 'paid')
                                <span class="badge badge-green">Lunas</span>
                                @else
                                <span class="badge badge-yellow">Pending</span>
                                @endif
                            @else
                            <span class="text-xs" style="color:var(--color-text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.bookings.show', $booking->id) }}"
                               class="btn btn-ghost btn-sm btn-icon" title="Detail">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:3rem;color:var(--color-text-muted);">
                            Belum ada data booking.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bookings->hasPages())
        <div class="px-5 py-3" style="border-top:1px solid var(--color-border);">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>

</x-layouts.web>
