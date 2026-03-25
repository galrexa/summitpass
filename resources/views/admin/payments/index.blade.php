<x-layouts.web>
    <x-slot:title>Manajemen Pembayaran</x-slot:title>
    <x-slot:breadcrumb>['Admin', 'Pembayaran']</x-slot:breadcrumb>

    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-semibold" style="color:var(--color-text);">Pembayaran</h2>
            <p class="text-sm mt-0.5" style="color:var(--color-text-muted);">Rekap seluruh transaksi pembayaran booking.</p>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#dcfce7;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20,6 9,17 4,12"/>
                </svg>
            </div>
            <div>
                <div class="stat-card-value" style="font-size:1.1rem;">Rp {{ number_format($summary['total_paid'], 0, ',', '.') }}</div>
                <div class="stat-card-label">Total Lunas</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#fef9c3;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <div>
                <div class="stat-card-value" style="font-size:1.1rem;">Rp {{ number_format($summary['total_pending'], 0, ',', '.') }}</div>
                <div class="stat-card-label">Total Pending</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#dcfce7;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
            </div>
            <div>
                <div class="stat-card-value">{{ $summary['count_paid'] }}</div>
                <div class="stat-card-label">Transaksi Lunas</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#fef9c3;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
            </div>
            <div>
                <div class="stat-card-value">{{ $summary['count_pending'] }}</div>
                <div class="stat-card-label">Transaksi Pending</div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="card mb-5" style="padding:1rem;">
        <div class="flex flex-wrap gap-3 items-end">
            <div style="flex:2;min-width:180px;">
                <label class="form-label">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-input"
                       placeholder="Kode booking atau nama pemesan...">
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-input">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid"    {{ request('status') === 'paid'    ? 'selected' : '' }}>Lunas</option>
                    <option value="failed"  {{ request('status') === 'failed'  ? 'selected' : '' }}>Gagal</option>
                </select>
            </div>
            <div>
                <label class="form-label">Gunung</label>
                <select name="mountain_id" class="form-input">
                    <option value="">Semua</option>
                    @foreach($mountains as $m)
                    <option value="{{ $m->id }}" {{ request('mountain_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Sampai</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input">
            </div>
            <button type="submit" class="btn btn-outline">Filter</button>
            @if(request()->hasAny(['search','status','mountain_id','date_from','date_to']))
            <a href="{{ route('admin.payments.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </div>
    </form>

    {{-- Tabel --}}
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Booking</th>
                        <th>Pemesan</th>
                        <th>Gunung</th>
                        <th>Gateway</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Dibayar Pada</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td class="font-mono text-xs" style="color:var(--color-text-muted);">#{{ $payment->id }}</td>
                        <td>
                            @if($payment->booking?->booking_code)
                            <a href="{{ route('admin.bookings.show', $payment->booking_id) }}"
                               class="font-mono text-xs font-bold hover:underline" style="color:var(--color-forest-700);">
                                {{ $payment->booking->booking_code }}
                            </a>
                            @else
                            <span class="font-mono text-xs" style="color:var(--color-text-muted);">Booking #{{ $payment->booking_id }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-sm font-medium">{{ $payment->booking?->leader?->name ?? '—' }}</div>
                            <div class="text-xs" style="color:var(--color-text-muted);">{{ $payment->booking?->leader?->email }}</div>
                        </td>
                        <td>
                            <div class="text-sm">{{ $payment->booking?->mountain?->name ?? '—' }}</div>
                            <div class="text-xs" style="color:var(--color-text-muted);">{{ $payment->booking?->trail?->name }}</div>
                        </td>
                        <td>
                            <span class="font-mono text-xs px-2 py-0.5 rounded" style="background:var(--color-forest-50);color:var(--color-forest-700);">
                                {{ strtoupper($payment->gateway) }}
                            </span>
                        </td>
                        <td class="text-sm font-semibold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td>
                            @if($payment->status === 'paid')
                            <span class="badge badge-green">Lunas</span>
                            @elseif($payment->status === 'pending')
                            <span class="badge badge-yellow">Pending</span>
                            @else
                            <span class="badge badge-red">{{ $payment->status }}</span>
                            @endif
                        </td>
                        <td class="text-xs" style="color:var(--color-text-muted);">
                            {{ $payment->paid_at ? $payment->paid_at->format('d M Y, H:i') : '—' }}
                        </td>
                        <td>
                            @if($payment->booking_id)
                            <a href="{{ route('admin.bookings.show', $payment->booking_id) }}"
                               class="btn btn-ghost btn-sm btn-icon" title="Lihat Booking">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:3rem;color:var(--color-text-muted);">
                            Belum ada data transaksi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="px-5 py-3" style="border-top:1px solid var(--color-border);">
            {{ $payments->links() }}
        </div>
        @endif
    </div>

</x-layouts.web>
