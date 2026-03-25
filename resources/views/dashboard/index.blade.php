<x-layouts.web>
    <x-slot:title>Dashboard</x-slot:title>
    <x-slot:breadcrumb>['SummitPass', 'Dashboard']</x-slot:breadcrumb>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        <div class="stat-card">
            <div class="stat-card-icon" style="background:var(--color-forest-100);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div>
                <div class="stat-card-value">284</div>
                <div class="stat-card-label">Pendaki Aktif Hari Ini</div>
                <div class="stat-card-delta positive">+18 dari kemarin</div>
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
                <div class="stat-card-value">47</div>
                <div class="stat-card-label">Booking Pending</div>
                <div class="stat-card-delta positive">+5 hari ini</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background:#fef3c7;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <div>
                <div class="stat-card-value">3</div>
                <div class="stat-card-label">Alert Belum Checkout</div>
                <div class="stat-card-delta negative">Perlu perhatian</div>
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
                <div class="stat-card-value">Rp 12,4jt</div>
                <div class="stat-card-label">Pendapatan Hari Ini</div>
                <div class="stat-card-delta positive">+22% dari rata-rata</div>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Alert section --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            {{-- Alert tidak checkout --}}
            <div class="card" style="padding:0;overflow:hidden;">
                <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid var(--color-border);">
                    <div class="flex items-center gap-2">
                        <h2 class="font-semibold text-sm" style="color:var(--color-text);">Alert Pendaki</h2>
                        <span class="badge badge-yellow">3 aktif</span>
                    </div>
                    <button class="btn btn-ghost btn-sm" style="font-size:0.75rem;">Lihat semua</button>
                </div>
                <div>
                    @php
                    $alerts = [
                        ['name' => 'Budi Santoso', 'mountain' => 'Semeru', 'trail' => 'Ranu Pane', 'type' => 'Tidak Checkout', 'since' => '2 jam lalu', 'severity' => 'red'],
                        ['name' => 'Annisa Putri', 'mountain' => 'Rinjani', 'trail' => 'Senaru', 'type' => 'Log Terhenti', 'since' => '5 jam lalu', 'severity' => 'yellow'],
                        ['name' => 'Riko Permana', 'mountain' => 'Gede', 'trail' => 'Gunung Putri', 'type' => 'Tidak Checkout', 'since' => '1 jam lalu', 'severity' => 'red'],
                    ];
                    @endphp
                    @foreach($alerts as $alert)
                    <div class="flex items-center gap-3 px-5 py-3.5" style="border-bottom:1px solid var(--color-border);">
                        <div style="width:8px;height:8px;border-radius:50%;flex-shrink:0;background:{{ $alert['severity'] === 'red' ? '#ef4444' : '#f59e0b' }};box-shadow:0 0 0 3px {{ $alert['severity'] === 'red' ? 'rgba(239,68,68,0.15)' : 'rgba(245,158,11,0.15)' }};"></div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium" style="color:var(--color-text);">{{ $alert['name'] }}</div>
                            <div class="text-xs" style="color:var(--color-text-muted);">{{ $alert['mountain'] }} &middot; {{ $alert['trail'] }}</div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="badge {{ $alert['severity'] === 'red' ? 'badge-red' : 'badge-yellow' }}">{{ $alert['type'] }}</span>
                            <div class="text-xs mt-1" style="color:var(--color-text-muted);">{{ $alert['since'] }}</div>
                        </div>
                        <button class="btn btn-ghost btn-icon btn-sm" title="Lihat detail">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9,18 15,12 9,6"/>
                            </svg>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Recent bookings --}}
            <div class="card" style="padding:0;overflow:hidden;">
                <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid var(--color-border);">
                    <h2 class="font-semibold text-sm" style="color:var(--color-text);">Booking Terbaru</h2>
                    <button class="btn btn-ghost btn-sm" style="font-size:0.75rem;">Lihat semua</button>
                </div>
                <div style="overflow-x:auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kode Booking</th>
                                <th>Leader</th>
                                <th>Gunung & Jalur</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $bookings = [
                                ['code' => 'SP-2403-0021', 'leader' => 'Dina Marlina', 'mountain' => 'Semeru', 'trail' => 'Ranu Pane', 'date' => '28 Mar — 30 Mar', 'status' => 'confirmed'],
                                ['code' => 'SP-2403-0020', 'leader' => 'Fajar Nugroho', 'mountain' => 'Rinjani', 'trail' => 'Senaru', 'date' => '1 Apr — 3 Apr', 'status' => 'pending'],
                                ['code' => 'SP-2403-0019', 'leader' => 'Sinta Wulandari', 'mountain' => 'Gede', 'trail' => 'Gunung Putri', 'date' => '27 Mar — 28 Mar', 'status' => 'paid'],
                                ['code' => 'SP-2403-0018', 'leader' => 'Adi Prasetyo', 'mountain' => 'Merbabu', 'trail' => 'Selo', 'date' => '26 Mar — 27 Mar', 'status' => 'completed'],
                            ];
                            $statusMap = ['confirmed' => ['class' => 'badge-blue', 'label' => 'Dikonfirmasi'], 'pending' => ['class' => 'badge-yellow', 'label' => 'Menunggu'], 'paid' => ['class' => 'badge-green', 'label' => 'Dibayar'], 'completed' => ['class' => 'badge-gray', 'label' => 'Selesai']];
                            @endphp
                            @foreach($bookings as $b)
                            <tr>
                                <td><span class="font-mono text-xs font-semibold" style="color:var(--color-forest-700);">{{ $b['code'] }}</span></td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="avatar" style="width:26px;height:26px;font-size:0.65rem;">{{ strtoupper(substr($b['leader'], 0, 2)) }}</div>
                                        <span class="text-sm">{{ $b['leader'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm font-medium">{{ $b['mountain'] }}</div>
                                    <div class="text-xs" style="color:var(--color-text-muted);">{{ $b['trail'] }}</div>
                                </td>
                                <td><span class="text-xs" style="color:var(--color-text-muted);">{{ $b['date'] }}</span></td>
                                <td><span class="badge {{ $statusMap[$b['status']]['class'] }}">{{ $statusMap[$b['status']]['label'] }}</span></td>
                                <td>
                                    <button class="btn btn-ghost btn-icon btn-sm">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- Right column --}}
        <div class="flex flex-col gap-4">

            {{-- Kuota gunung hari ini --}}
            <div class="card">
                <h2 class="font-semibold text-sm mb-4" style="color:var(--color-text);">Kuota Gunung Hari Ini</h2>
                @php
                $mountains = [
                    ['name' => 'Semeru', 'trail' => 'Ranu Pane', 'used' => 280, 'total' => 300],
                    ['name' => 'Rinjani', 'trail' => 'Senaru', 'used' => 55, 'total' => 80],
                    ['name' => 'Gede', 'trail' => 'Gunung Putri', 'used' => 40, 'total' => 100],
                    ['name' => 'Merbabu', 'trail' => 'Selo', 'used' => 20, 'total' => 60],
                ];
                @endphp
                <div class="flex flex-col gap-3">
                    @foreach($mountains as $m)
                    @php $pct = round($m['used'] / $m['total'] * 100); $color = $pct >= 90 ? '#ef4444' : ($pct >= 70 ? '#f59e0b' : 'var(--color-forest-500)'); @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <div>
                                <span class="text-sm font-medium" style="color:var(--color-text);">{{ $m['name'] }}</span>
                                <span class="text-xs ml-1.5" style="color:var(--color-text-muted);">{{ $m['trail'] }}</span>
                            </div>
                            <span class="text-xs font-semibold" style="color:{{ $color }};">{{ $m['used'] }}/{{ $m['total'] }}</span>
                        </div>
                        <div style="height:5px;background:var(--color-forest-100);border-radius:3px;overflow:hidden;">
                            <div style="height:100%;width:{{ $pct }}%;background:{{ $color }};border-radius:3px;transition:width 0.5s;"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="card">
                <h2 class="font-semibold text-sm mb-4" style="color:var(--color-text);">Aksi Cepat</h2>
                <div class="flex flex-col gap-2">
                    <a href="#" class="btn btn-primary btn-sm w-full" style="justify-content:flex-start;gap:0.625rem;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        Verifikasi QR Pass
                    </a>
                    <a href="#" class="btn btn-outline btn-sm w-full" style="justify-content:flex-start;gap:0.625rem;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Tambah Gunung
                    </a>
                    <a href="#" class="btn btn-outline btn-sm w-full" style="justify-content:flex-start;gap:0.625rem;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7,10 12,15 17,10"/><line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Export Laporan
                    </a>
                </div>
            </div>

            {{-- Active on mountain now --}}
            <div class="card">
                <h2 class="font-semibold text-sm mb-3" style="color:var(--color-text);">Aktif di Jalur Sekarang</h2>
                @php
                $onTrail = [
                    ['name' => 'Budi S.', 'pos' => 'Pos 3 — Naik', 'mountain' => 'Semeru', 'since' => '3j lalu'],
                    ['name' => 'Yuni R.', 'pos' => 'Puncak', 'mountain' => 'Rinjani', 'since' => '1j lalu'],
                    ['name' => 'Iwan D.', 'pos' => 'Pos 2 — Turun', 'mountain' => 'Gede', 'since' => '45m lalu'],
                ];
                @endphp
                <div class="flex flex-col gap-2.5">
                    @foreach($onTrail as $t)
                    <div class="flex items-center gap-2.5">
                        <div class="avatar" style="width:30px;height:30px;font-size:0.7rem;">{{ strtoupper(substr($t['name'], 0, 2)) }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium truncate" style="color:var(--color-text);">{{ $t['name'] }}</div>
                            <div class="text-xs truncate" style="color:var(--color-text-muted);">{{ $t['pos'] }} &middot; {{ $t['mountain'] }}</div>
                        </div>
                        <span class="text-xs" style="color:var(--color-text-muted);flex-shrink:0;">{{ $t['since'] }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="mt-3 pt-3" style="border-top:1px solid var(--color-border);">
                    <a href="#" class="text-xs font-semibold" style="color:var(--color-forest-700);">Lihat semua 284 pendaki &rarr;</a>
                </div>
            </div>

        </div>
    </div>

</x-layouts.web>
