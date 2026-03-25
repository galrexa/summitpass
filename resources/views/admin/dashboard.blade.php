<x-layouts.web :title="'Admin Dashboard'">
    <x-slot:title>Admin Dashboard</x-slot:title>
    <x-slot:breadcrumb>['SummitPass', 'Admin', 'Dashboard']</x-slot:breadcrumb>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        <div class="stat-card">
            <div class="stat-card-icon" style="background:var(--color-forest-100);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 20l5-9 4 6 3-4 6 7H3z"/>
                </svg>
            </div>
            <div>
                <div class="stat-card-value">{{ $stats['total_mountains'] }}</div>
                <div class="stat-card-label">Gunung Aktif</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background:var(--color-lake-100);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-lake-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/>
                </svg>
            </div>
            <div>
                <div class="stat-card-value">{{ $stats['pending_bookings'] }}</div>
                <div class="stat-card-label">Booking Pending</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background:#fef3c7;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <div class="stat-card-value">{{ $stats['active_trekkers'] }}</div>
                <div class="stat-card-label">Pendaki di Jalur</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background:var(--color-forest-100);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
            </div>
            <div>
                <div class="stat-card-value">Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}</div>
                <div class="stat-card-label">Pendapatan Hari Ini</div>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Booking terbaru --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            <div class="card" style="padding:0;overflow:hidden;">
                <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid var(--color-border);">
                    <h2 class="font-semibold text-sm" style="color:var(--color-text);">Booking Terbaru</h2>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-ghost btn-sm" style="font-size:0.75rem;">Lihat semua</a>
                </div>
                <div style="overflow-x:auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Leader</th>
                                <th>Gunung</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                            @php
                            $statusMap = [
                                'pending_payment' => ['class' => 'badge-yellow', 'label' => 'Pending'],
                                'paid'            => ['class' => 'badge-blue',   'label' => 'Dibayar'],
                                'active'          => ['class' => 'badge-green',  'label' => 'Aktif'],
                                'completed'       => ['class' => 'badge-gray',   'label' => 'Selesai'],
                                'cancelled'       => ['class' => 'badge-red',    'label' => 'Dibatalkan'],
                            ];
                            $s = $statusMap[$booking->status] ?? ['class' => 'badge-gray', 'label' => $booking->status];
                            @endphp
                            <tr>
                                <td><span class="font-mono text-xs font-semibold" style="color:var(--color-forest-700);">{{ $booking->booking_code ?? '—' }}</span></td>
                                <td class="text-sm">{{ $booking->leader?->name ?? '—' }}</td>
                                <td>
                                    <div class="text-sm font-medium">{{ $booking->mountain?->name ?? '—' }}</div>
                                    <div class="text-xs" style="color:var(--color-text-muted);">{{ $booking->trail?->name ?? '' }}</div>
                                </td>
                                <td class="text-xs" style="color:var(--color-text-muted);">
                                    {{ $booking->start_date?->format('d M') }} — {{ $booking->end_date?->format('d M Y') }}
                                </td>
                                <td><span class="badge {{ $s['class'] }}">{{ $s['label'] }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-sm" style="color:var(--color-text-muted);padding:2rem;">Belum ada booking</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Alert anomali --}}
            <div class="card" style="padding:0;overflow:hidden;">
                <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid var(--color-border);">
                    <div class="flex items-center gap-2">
                        <h2 class="font-semibold text-sm" style="color:var(--color-text);">Alert Anomali</h2>
                        @if($anomalyAlerts->count() > 0)
                        <span class="badge badge-red">{{ $anomalyAlerts->count() }}</span>
                        @endif
                    </div>
                </div>
                @forelse($anomalyAlerts as $log)
                <div class="flex items-center gap-3 px-5 py-3.5" style="border-bottom:1px solid var(--color-border);">
                    <div style="width:8px;height:8px;border-radius:50%;flex-shrink:0;background:#ef4444;box-shadow:0 0 0 3px rgba(239,68,68,0.15);"></div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium" style="color:var(--color-text);">
                            {{ $log->qrPass?->bookingParticipant?->name ?? 'Pendaki' }}
                        </div>
                        <div class="text-xs" style="color:var(--color-text-muted);">
                            Pos: {{ $log->checkpoint?->name ?? '—' }} &middot; {{ $log->scanned_at?->diffForHumans() }}
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="badge badge-red">Anomali</span>
                        <div class="text-xs mt-1" style="color:var(--color-text-muted);">{{ Str::limit($log->anomaly_reason, 30) }}</div>
                    </div>
                </div>
                @empty
                <div class="px-5 py-4 text-sm" style="color:var(--color-text-muted);">Tidak ada alert anomali saat ini.</div>
                @endforelse
            </div>

        </div>

        {{-- Quick actions --}}
        <div class="flex flex-col gap-4">
            <div class="card">
                <h2 class="font-semibold text-sm mb-4" style="color:var(--color-text);">Aksi Cepat</h2>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('admin.mountains.index') }}" class="btn btn-primary btn-sm w-full" style="justify-content:flex-start;gap:0.625rem;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 20l5-9 4 6 3-4 6 7H3z"/>
                        </svg>
                        Kelola Gunung
                    </a>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline btn-sm w-full" style="justify-content:flex-start;gap:0.625rem;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                        </svg>
                        Semua Booking
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm w-full" style="justify-content:flex-start;gap:0.625rem;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                        </svg>
                        Kelola Pengguna
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline btn-sm w-full" style="justify-content:flex-start;gap:0.625rem;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.07 4.93l-1.42 1.42M4.93 4.93l1.42 1.42M12 2v2M12 20v2M20 12h2M2 12h2M19.07 19.07l-1.42-1.42M4.93 19.07l1.42-1.42"/>
                        </svg>
                        Pengaturan Sistem
                    </a>
                </div>
            </div>

            <div class="card">
                <h2 class="font-semibold text-sm mb-2" style="color:var(--color-text);">Info Akun</h2>
                <div class="text-sm" style="color:var(--color-text-muted);">
                    Login sebagai <strong style="color:var(--color-text);">{{ auth()->user()->name }}</strong>
                </div>
                <div class="mt-1">
                    <span class="badge badge-green">Admin</span>
                </div>
            </div>
        </div>

    </div>

</x-layouts.web>
