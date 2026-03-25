<x-layouts.mobile>
    <x-slot:title>QR SummitPass</x-slot:title>
    <x-slot:subtitle>Aktif hingga {{ $pass->valid_until ?? '30 Mar 2025, 17.00' }}</x-slot:subtitle>

    <div class="px-4 pt-4 pb-2">

        {{-- Pass card --}}
        <div class="qr-pass-card">
            {{-- Header --}}
            <div class="qr-pass-header">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 20l5-9 4 6 3-4 6 7H3z"/>
                        </svg>
                        <span style="font-size:0.75rem;color:rgba(255,255,255,0.65);font-weight:600;letter-spacing:0.05em;">SUMMITPASS</span>
                    </div>
                    <span class="badge" style="background:rgba(255,255,255,0.15);color:white;font-size:0.65rem;">
                        <span style="width:6px;height:6px;border-radius:50%;background:#4ade80;display:inline-block;"></span>
                        AKTIF
                    </span>
                </div>
                <div style="font-size:1.25rem;font-weight:700;color:#fff;margin-bottom:0.25rem;">
                    {{ $user->name ?? 'Budi Santoso' }}
                </div>
                <div style="font-size:0.8rem;color:rgba(255,255,255,0.6);">
                    NIK: ••••••••••{{ substr($user->nik ?? '3201234567890123', -4) }}
                </div>
            </div>

            {{-- Divider with notch --}}
            <div class="qr-pass-divider">
                <div class="qr-pass-notch qr-pass-notch-left"></div>
                <div class="qr-pass-dashed"></div>
                <div class="qr-pass-notch qr-pass-notch-right"></div>
            </div>

            {{-- Body --}}
            <div class="qr-pass-body">
                {{-- QR Code --}}
                <div style="display:flex;justify-content:center;margin-bottom:1.25rem;">
                    <div style="padding:1rem;background:white;border-radius:12px;border:1px solid var(--color-border);display:inline-block;">
                        {{-- Placeholder QR — replace with actual QR code --}}
                        <div style="width:160px;height:160px;background:var(--color-forest-50);border-radius:4px;display:flex;align-items:center;justify-content:center;position:relative;">
                            {{-- Fake QR pattern --}}
                            <svg width="140" height="140" viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="10" y="10" width="40" height="40" rx="4" fill="var(--color-forest-800)"/>
                                <rect x="15" y="15" width="30" height="30" rx="2" fill="white"/>
                                <rect x="20" y="20" width="20" height="20" rx="1" fill="var(--color-forest-800)"/>
                                <rect x="90" y="10" width="40" height="40" rx="4" fill="var(--color-forest-800)"/>
                                <rect x="95" y="15" width="30" height="30" rx="2" fill="white"/>
                                <rect x="100" y="20" width="20" height="20" rx="1" fill="var(--color-forest-800)"/>
                                <rect x="10" y="90" width="40" height="40" rx="4" fill="var(--color-forest-800)"/>
                                <rect x="15" y="95" width="30" height="30" rx="2" fill="white"/>
                                <rect x="20" y="100" width="20" height="20" rx="1" fill="var(--color-forest-800)"/>
                                <rect x="60" y="10" width="20" height="10" rx="1" fill="var(--color-forest-800)"/>
                                <rect x="60" y="25" width="10" height="20" rx="1" fill="var(--color-forest-800)"/>
                                <rect x="10" y="60" width="10" height="20" rx="1" fill="var(--color-forest-800)"/>
                                <rect x="25" y="60" width="20" height="10" rx="1" fill="var(--color-forest-800)"/>
                                <rect x="60" y="60" width="20" height="20" rx="2" fill="var(--color-lake-600)"/>
                                <rect x="85" y="60" width="10" height="10" rx="1" fill="var(--color-forest-800)"/>
                                <rect x="100" y="55" width="10" height="15" rx="1" fill="var(--color-forest-800)"/>
                                <rect x="115" y="60" width="15" height="10" rx="1" fill="var(--color-forest-800)"/>
                                <rect x="60" y="85" width="15" height="10" rx="1" fill="var(--color-forest-800)"/>
                                <rect x="80" y="80" width="10" height="20" rx="1" fill="var(--color-forest-800)"/>
                                <rect x="95" y="85" width="20" height="10" rx="1" fill="var(--color-forest-800)"/>
                                <rect x="120" y="80" width="10" height="15" rx="1" fill="var(--color-forest-800)"/>
                                <rect x="60" y="100" width="10" height="20" rx="1" fill="var(--color-forest-800)"/>
                                <rect x="75" y="105" width="20" height="10" rx="1" fill="var(--color-forest-800)"/>
                                <rect x="100" y="100" width="15" height="10" rx="1" fill="var(--color-forest-800)"/>
                                <rect x="120" y="100" width="10" height="20" rx="1" fill="var(--color-forest-800)"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Info rows --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.875rem 1rem;margin-bottom:1rem;">
                    <div>
                        <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--color-text-muted);font-weight:600;margin-bottom:0.2rem;">Gunung</div>
                        <div style="font-size:0.875rem;font-weight:600;color:var(--color-text);">{{ $booking->mountain->name ?? 'Semeru' }}</div>
                    </div>
                    <div>
                        <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--color-text-muted);font-weight:600;margin-bottom:0.2rem;">Jalur</div>
                        <div style="font-size:0.875rem;font-weight:600;color:var(--color-text);">{{ $booking->trail->name ?? 'Ranu Pane' }}</div>
                    </div>
                    <div>
                        <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--color-text-muted);font-weight:600;margin-bottom:0.2rem;">Tanggal Naik</div>
                        <div style="font-size:0.875rem;font-weight:600;color:var(--color-text);">{{ $booking->start_date ?? '28 Mar 2025' }}</div>
                    </div>
                    <div>
                        <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--color-text-muted);font-weight:600;margin-bottom:0.2rem;">Tanggal Turun</div>
                        <div style="font-size:0.875rem;font-weight:600;color:var(--color-text);">{{ $booking->end_date ?? '30 Mar 2025' }}</div>
                    </div>
                </div>

                <div style="padding:0.75rem;background:var(--color-forest-50);border-radius:8px;display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--color-text-muted);font-weight:600;margin-bottom:0.15rem;">Kode Booking</div>
                        <div style="font-size:0.9rem;font-weight:700;font-family:monospace;color:var(--color-forest-700);">{{ $booking->booking_code ?? 'SP-2403-0021' }}</div>
                    </div>
                    <button class="btn btn-ghost btn-sm" onclick="navigator.clipboard?.writeText('SP-2403-0021')" style="font-size:0.72rem;">
                        Salin
                    </button>
                </div>
            </div>
        </div>

        {{-- Trekking Log summary --}}
        <div class="card card-sm mt-4">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.875rem;">
                <h3 style="font-size:0.875rem;font-weight:600;color:var(--color-text);">Log Perjalanan</h3>
                <span class="badge badge-green">Naik</span>
            </div>
            @php
            $logs = [
                ['pos' => 'Gerbang Masuk', 'time' => '06:12', 'dir' => 'check_in', 'done' => true],
                ['pos' => 'Pos 1 — Landengan Dowo', 'time' => '07:45', 'dir' => 'up', 'done' => true],
                ['pos' => 'Pos 2 — Watu Rejeng', 'time' => '09:20', 'dir' => 'up', 'done' => true],
                ['pos' => 'Pos 3 — Kalimati', 'time' => '—', 'dir' => 'up', 'done' => false],
                ['pos' => 'Puncak Mahameru', 'time' => '—', 'dir' => 'summit', 'done' => false],
            ];
            @endphp
            <div style="display:flex;flex-direction:column;gap:0;">
                @foreach($logs as $i => $log)
                <div style="display:flex;align-items:center;gap:0.875rem;position:relative;">
                    {{-- Timeline line --}}
                    @if($i < count($logs) - 1)
                    <div style="position:absolute;left:10px;top:22px;width:2px;height:calc(100% - 4px);background:{{ $log['done'] ? 'var(--color-forest-400)' : 'var(--color-border)' }};z-index:0;"></div>
                    @endif
                    {{-- Dot --}}
                    <div style="width:20px;height:20px;border-radius:50%;flex-shrink:0;z-index:1;display:flex;align-items:center;justify-content:center;
                        {{ $log['done'] ? 'background:var(--color-forest-600);' : 'background:var(--color-surface);border:2px solid var(--color-border);' }}">
                        @if($log['done'])
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg>
                        @endif
                    </div>
                    {{-- Content --}}
                    <div style="flex:1;padding:0.625rem 0;">
                        <div style="font-size:0.8rem;font-weight:{{ $log['done'] ? '600' : '500' }};color:{{ $log['done'] ? 'var(--color-text)' : 'var(--color-text-muted)' }};">{{ $log['pos'] }}</div>
                    </div>
                    <div style="font-size:0.72rem;color:var(--color-text-muted);flex-shrink:0;">{{ $log['time'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Info --}}
        <div class="alert alert-info mt-3">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span style="font-size:0.8rem;">Scan QR ini di setiap pos untuk mencatat perjalanan Anda. Batas checkout <strong>17.00 WIB</strong>.</span>
        </div>

    </div>

</x-layouts.mobile>
