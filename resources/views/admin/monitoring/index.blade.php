<x-layouts.web>
    <x-slot:title>Monitoring Pendakian</x-slot:title>
    <x-slot:breadcrumb>['Admin', 'Monitoring']</x-slot:breadcrumb>

    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-semibold" style="color:var(--color-text);">Monitoring Real-time</h2>
            <p class="text-sm mt-0.5" style="color:var(--color-text-muted);">
                Data per {{ now()->format('d M Y, H:i') }} &nbsp;
                <a href="{{ route('admin.monitoring.index') }}" style="color:var(--color-forest-700);font-size:0.75rem;">↻ Refresh</a>
            </p>
        </div>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-ghost btn-sm" style="gap:0.4rem;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.07 4.93l-1.42 1.42M4.93 4.93l1.42 1.42M12 2v2M12 20v2M20 12h2M2 12h2M19.07 19.07l-1.42-1.42M4.93 19.07l1.42-1.42"/>
            </svg>
            Pengaturan Anomali
        </a>
    </div>

    {{-- Stat cards --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem;">
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#dcfce7;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <div class="stat-card-value">{{ $stats['active_now'] }}</div>
                <div class="stat-card-label">Pendaki di Jalur</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#fef9c3;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <div>
                <div class="stat-card-value">{{ $stats['anomalies'] }}</div>
                <div class="stat-card-label">Log Anomali</div>
                @if($stats['anomalies'] > 0)
                <div class="stat-card-delta negative">Perlu perhatian</div>
                @endif
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#fee2e2;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <div>
                <div class="stat-card-value">{{ $stats['expired'] }}</div>
                <div class="stat-card-label">QR Pass Expired</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:var(--color-forest-100);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 20l5-9 4 6 3-4 6 7H3z"/>
                </svg>
            </div>
            <div>
                <div class="stat-card-value">{{ $stats['total_today'] }}</div>
                <div class="stat-card-label">Booking Aktif Hari Ini</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Pendaki aktif di jalur --}}
        <div class="card" style="padding:0;overflow:hidden;">
            <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid var(--color-border);">
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold text-sm" style="color:var(--color-text);">Pendaki Aktif di Jalur</h3>
                    @if($stats['active_now'] > 0)
                    <span class="badge badge-green">{{ $stats['active_now'] }}</span>
                    @endif
                </div>
            </div>

            @forelse($activePasses as $pass)
            @php
                $lastLog = $pass->trekkingLogs->first();
                $booking = $pass->participant?->booking;
            @endphp
            <div class="flex items-center gap-3 px-5 py-3.5" style="border-bottom:1px solid var(--color-border);">
                <div style="width:9px;height:9px;border-radius:50%;flex-shrink:0;background:#22c55e;box-shadow:0 0 0 3px rgba(34,197,94,0.2);"></div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold truncate" style="color:var(--color-text);">
                        {{ $pass->participant?->name ?? '—' }}
                    </div>
                    <div class="text-xs truncate" style="color:var(--color-text-muted);">
                        {{ $booking?->mountain?->name }} &middot; {{ $booking?->trail?->name }}
                    </div>
                    @if($lastLog)
                    <div class="text-xs mt-0.5" style="color:var(--color-forest-600);">
                        Terakhir: {{ $lastLog->checkpoint?->name ?? '—' }}
                        &middot; {{ $lastLog->scanned_at?->diffForHumans() }}
                    </div>
                    @else
                    <div class="text-xs mt-0.5" style="color:var(--color-text-muted);">Belum ada scan</div>
                    @endif
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="text-xs" style="color:var(--color-text-muted);">Batas</div>
                    <div class="text-xs font-semibold" style="color:{{ now()->gt($pass->valid_until->subHours(2)) ? '#dc2626' : 'var(--color-text)' }};">
                        {{ $pass->valid_until?->format('d M, H:i') }}
                    </div>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm" style="color:var(--color-text-muted);">
                Tidak ada pendaki aktif di jalur saat ini.
            </div>
            @endforelse
        </div>

        {{-- Alert anomali --}}
        <div class="card" style="padding:0;overflow:hidden;">
            <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid var(--color-border);">
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold text-sm" style="color:var(--color-text);">Log Anomali</h3>
                    @if($stats['anomalies'] > 0)
                    <span class="badge badge-red">{{ $stats['anomalies'] }}</span>
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.settings.run-anomaly-check') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm" style="font-size:0.75rem;gap:0.4rem;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="23,4 23,10 17,10"/><polyline points="1,20 1,14 7,14"/>
                            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                        </svg>
                        Cek Sekarang
                    </button>
                </form>
            </div>

            @forelse($anomalyLogs as $log)
            @php $booking = $log->qrPass?->participant?->booking; @endphp
            <div class="flex items-start gap-3 px-5 py-3.5" style="border-bottom:1px solid var(--color-border);">
                <div style="width:9px;height:9px;border-radius:50%;flex-shrink:0;margin-top:4px;background:#ef4444;box-shadow:0 0 0 3px rgba(239,68,68,0.15);"></div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold" style="color:var(--color-text);">
                        {{ $log->qrPass?->participant?->name ?? 'Pendaki' }}
                    </div>
                    <div class="text-xs" style="color:var(--color-text-muted);">
                        {{ $booking?->mountain?->name }} &middot; {{ $booking?->trail?->name }}
                        &middot; {{ $log->checkpoint?->name }}
                    </div>
                    <div class="text-xs mt-0.5 font-medium" style="color:#dc2626;">
                        {{ $log->anomaly_reason ?? 'Anomali terdeteksi' }}
                    </div>
                </div>
                <div class="text-xs flex-shrink-0" style="color:var(--color-text-muted);">
                    {{ $log->scanned_at?->diffForHumans() }}
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm" style="color:var(--color-text-muted);">
                Tidak ada log anomali.
            </div>
            @endforelse
        </div>

    </div>

    {{-- QR Pass Expired (belum checkout) --}}
    @if($expiredPasses->isNotEmpty())
    <div class="card mt-6" style="padding:0;overflow:hidden;">
        <div class="px-5 py-4" style="border-bottom:1px solid var(--color-border);">
            <div class="flex items-center gap-2">
                <h3 class="font-semibold text-sm" style="color:var(--color-text);">QR Pass Expired — Belum Checkout</h3>
                <span class="badge badge-red">{{ $expiredPasses->count() }}</span>
            </div>
            <p class="text-xs mt-0.5" style="color:var(--color-text-muted);">Pendaki yang melewati batas waktu checkout tanpa scan gate out.</p>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Peserta</th>
                        <th>Gunung & Jalur</th>
                        <th>Batas Checkout</th>
                        <th>Lewat Sejak</th>
                        <th>Booking</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expiredPasses as $pass)
                    @php $booking = $pass->participant?->booking; @endphp
                    <tr>
                        <td class="font-medium text-sm">{{ $pass->participant?->name ?? '—' }}</td>
                        <td>
                            <div class="text-sm">{{ $booking?->mountain?->name ?? '—' }}</div>
                            <div class="text-xs" style="color:var(--color-text-muted);">{{ $booking?->trail?->name }}</div>
                        </td>
                        <td class="text-sm font-semibold" style="color:#dc2626;">
                            {{ $pass->valid_until?->format('d M Y, H:i') }}
                        </td>
                        <td class="text-sm">{{ $pass->valid_until?->diffForHumans() }}</td>
                        <td>
                            @if($booking)
                            <a href="{{ route('admin.bookings.show', $booking->id) }}"
                               class="font-mono text-xs hover:underline" style="color:var(--color-forest-700);">
                                {{ $booking->booking_code ?? '#'.$booking->id }}
                            </a>
                            @else —
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</x-layouts.web>
