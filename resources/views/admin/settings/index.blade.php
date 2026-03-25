<x-layouts.web>
    <x-slot:title>Pengaturan Sistem</x-slot:title>
    <x-slot:breadcrumb>['Admin', 'Pengaturan Sistem']</x-slot:breadcrumb>

    @php
    $settingMeta = [
        'anomaly_check_interval_minutes' => [
            'label' => 'Interval Cek Anomali',
            'unit'  => 'menit',
            'type'  => 'number',
            'min'   => 5,
            'max'   => 1440,
            'hint'  => 'Seberapa sering scheduler mengecek anomali pendaki. Min: 5, maks: 1440 (24 jam).',
        ],
        'anomaly_stall_threshold_hours'  => [
            'label' => 'Threshold Log Terhenti',
            'unit'  => 'jam',
            'type'  => 'number',
            'min'   => 1,
            'max'   => 72,
            'hint'  => 'Jika tidak ada scan baru selama X jam, pendaki dianggap anomali (terhenti/tersesat).',
        ],
        'anomaly_checkout_grace_minutes' => [
            'label' => 'Grace Period Checkout',
            'unit'  => 'menit',
            'type'  => 'number',
            'min'   => 0,
            'max'   => 120,
            'hint'  => 'Toleransi waktu (menit) setelah batas checkout sebelum alert dikirim. Isi 0 untuk langsung alert.',
        ],
    ];
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kiri: daftar settings --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold" style="color:var(--color-text);">Pengaturan Sistem</h2>
                    <p class="text-sm mt-0.5" style="color:var(--color-text-muted);">Konfigurasi parameter deteksi anomali dan monitoring pendakian.</p>
                </div>
            </div>

            @foreach($settings as $setting)
            @php $meta = $settingMeta[$setting->key] ?? null; @endphp
            <div class="card">
                <form method="POST" action="{{ route('admin.settings.update', $setting->key) }}">
                    @csrf
                    <div class="flex items-start gap-4">
                        <div class="flex-1 min-w-0">
                            <label class="form-label font-semibold" style="color:var(--color-text);">
                                {{ $meta['label'] ?? $setting->key }}
                            </label>
                            @if($setting->description || $meta)
                            <p class="text-xs mb-3" style="color:var(--color-text-muted);">
                                {{ $meta['hint'] ?? $setting->description }}
                            </p>
                            @endif

                            <div class="flex items-center gap-3">
                                <div style="position:relative;max-width:160px;">
                                    <input
                                        type="{{ $meta['type'] ?? 'text' }}"
                                        name="value"
                                        value="{{ old('value', $setting->value) }}"
                                        class="form-input"
                                        @if(isset($meta['min'])) min="{{ $meta['min'] }}" @endif
                                        @if(isset($meta['max'])) max="{{ $meta['max'] }}" @endif
                                        style="padding-right: {{ isset($meta['unit']) ? '3.5rem' : '0.75rem' }};"
                                    >
                                    @if(isset($meta['unit']))
                                    <span style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);font-size:0.75rem;color:var(--color-text-muted);pointer-events:none;">
                                        {{ $meta['unit'] }}
                                    </span>
                                    @endif
                                </div>
                                <button type="submit" class="btn btn-outline btn-sm">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            @endforeach

            @if($settings->isEmpty())
            <div class="card text-center py-8">
                <p class="text-sm" style="color:var(--color-text-muted);">Belum ada settings. Jalankan migration terlebih dahulu.</p>
            </div>
            @endif

        </div>

        {{-- Kanan: anomaly check manual --}}
        <div class="flex flex-col gap-4">

            <div class="card">
                <h3 class="font-semibold text-sm mb-1" style="color:var(--color-text);">Anomaly Check Manual</h3>
                <p class="text-xs mb-4" style="color:var(--color-text-muted);">
                    Jalankan pengecekan anomali sekarang tanpa menunggu scheduler.
                    Berguna untuk testing atau cek mendesak.
                </p>

                @if(session('anomaly_output'))
                <div class="mb-4 p-3 rounded-lg font-mono text-xs" style="background:#f1f5f9;color:#334155;white-space:pre-wrap;line-height:1.6;max-height:200px;overflow-y:auto;">{{ session('anomaly_output') }}</div>
                @endif

                <form method="POST" action="{{ route('admin.settings.run-anomaly-check') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary w-full" style="gap:0.5rem;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="23,4 23,10 17,10"/><polyline points="1,20 1,14 7,14"/>
                            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                        </svg>
                        Jalankan Anomaly Check
                    </button>
                </form>
            </div>

            <div class="card">
                <h3 class="font-semibold text-sm mb-3" style="color:var(--color-text);">Status Scheduler</h3>
                <div class="flex flex-col gap-2">
                    <div class="flex justify-between text-sm">
                        <span style="color:var(--color-text-muted);">Terakhir dijalankan</span>
                        <span class="font-semibold text-xs">
                            @if($lastAnomalyRun)
                                {{ \Carbon\Carbon::parse($lastAnomalyRun)->format('d M Y, H:i') }}
                            @else
                                <span style="color:var(--color-text-muted);">Belum pernah</span>
                            @endif
                        </span>
                    </div>
                    @if($lastAnomalyRun)
                    <div class="flex justify-between text-sm">
                        <span style="color:var(--color-text-muted);">Sejak</span>
                        <span>{{ \Carbon\Carbon::parse($lastAnomalyRun)->diffForHumans() }}</span>
                    </div>
                    @endif
                </div>
                <div class="mt-3 pt-3 text-xs" style="border-top:1px solid var(--color-border);color:var(--color-text-muted);">
                    Scheduler otomatis berjalan sesuai interval yang dikonfigurasi. Pastikan <code style="background:#f1f5f9;padding:1px 4px;border-radius:3px;">php artisan schedule:run</code> dijadwalkan di crontab server.
                </div>
            </div>

            <div class="card">
                <h3 class="font-semibold text-sm mb-2" style="color:var(--color-text);">Navigasi Cepat</h3>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('admin.monitoring.index') }}" class="btn btn-outline btn-sm w-full" style="justify-content:flex-start;gap:0.5rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/>
                        </svg>
                        Monitoring Real-time
                    </a>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline btn-sm w-full" style="justify-content:flex-start;gap:0.5rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        </svg>
                        Semua Booking
                    </a>
                </div>
            </div>

        </div>
    </div>

</x-layouts.web>
