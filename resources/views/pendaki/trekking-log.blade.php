<x-layouts.web>
    <x-slot:title>Trekking Log</x-slot:title>
    <x-slot:breadcrumb>['SummitPass', 'Trekking Log']</x-slot:breadcrumb>

    <div style="max-width:640px;">

        {{-- Stats row --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;">
            <div class="stat-card">
                <div class="stat-card-icon" style="background:var(--color-forest-100);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20l5-9 4 6 3-4 6 7H3z"/></svg>
                </div>
                <div>
                    <div class="stat-card-value">{{ $completedBookings }}</div>
                    <div class="stat-card-label">Total Pendakian</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon" style="background:var(--color-lake-100);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-lake-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20l5-9 4 6 3-4 6 7H3z"/></svg>
                </div>
                <div>
                    <div class="stat-card-value">{{ $uniqueMountainsCount }}</div>
                    <div class="stat-card-label">Gunung Didaki</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon" style="background:#fef3c7;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                </div>
                <div>
                    <div class="stat-card-value">{{ $totalScans }}</div>
                    <div class="stat-card-label">Pos Terscan</div>
                </div>
            </div>
        </div>

        {{-- Log list --}}
        <div class="card">
            <h3 style="font-size:.875rem;font-weight:700;color:var(--color-text);margin-bottom:1rem;">Riwayat Scan Pos</h3>

            @if($groupedLogs->isEmpty())
            <div style="text-align:center;padding:2.5rem 0;">
                <div style="width:48px;height:48px;background:var(--color-forest-100);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto .875rem;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                </div>
                <h3 style="font-size:.9rem;font-weight:600;color:var(--color-text);margin-bottom:.4rem;">Belum ada log pendakian</h3>
                <p style="font-size:.8rem;color:var(--color-text-muted);max-width:300px;margin:0 auto .75rem;line-height:1.6;">Log scan pos akan muncul di sini setelah petugas men-scan QR SummitPass-mu di setiap pos.</p>
                <a href="{{ route('pendaki.my-pass') }}" class="btn btn-outline btn-sm">Lihat QR Pass Saya</a>
            </div>
            @else
            <div style="display:flex;flex-direction:column;gap:.75rem;">
                @foreach($groupedLogs as $i => $group)
                @php
                    $mountain  = $group['mountain'];
                    $booking   = $group['booking'];
                    $groupLogs = $group['logs'];
                    $firstScan = $group['first_scan'];
                    $lastScan  = $group['last_scan'];
                    $scanCount = $groupLogs->count();
                    $hasAnomaly = $groupLogs->contains('anomaly_flag', true);
                    $accordionId = 'acc-' . $i;
                @endphp

                {{-- Accordion header --}}
                <div style="border:1px solid {{ $hasAnomaly ? '#fecaca' : 'var(--color-border)' }};border-radius:10px;overflow:hidden;">
                    <button
                        type="button"
                        onclick="toggleAccordion('{{ $accordionId }}')"
                        style="width:100%;display:flex;align-items:center;gap:.875rem;padding:.875rem 1rem;background:{{ $hasAnomaly ? '#fef2f2' : '#f9fafb' }};border:none;cursor:pointer;text-align:left;"
                    >
                        {{-- Mountain icon --}}
                        <div style="width:40px;height:40px;border-radius:8px;background:var(--color-forest-100);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20l5-9 4 6 3-4 6 7H3z"/></svg>
                        </div>

                        {{-- Info --}}
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:.875rem;font-weight:700;color:var(--color-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $mountain?->name ?? 'Gunung tidak diketahui' }}
                                @if($hasAnomaly)
                                <span style="font-size:.65rem;background:#fee2e2;color:#dc2626;font-weight:700;padding:.1rem .4rem;border-radius:20px;margin-left:.3rem;">⚠ Anomali</span>
                                @endif
                            </div>
                            <div style="font-size:.72rem;color:var(--color-text-muted);margin-top:.15rem;">
                                {{ $firstScan ? $firstScan->format('d M Y') : '—' }}
                                @if($lastScan && $firstScan && $firstScan->format('d M Y') !== $lastScan->format('d M Y'))
                                    &ndash; {{ $lastScan->format('d M Y') }}
                                @endif
                                &middot; {{ $scanCount }} scan
                            </div>
                        </div>

                        {{-- Chevron --}}
                        <div id="{{ $accordionId }}-chevron" style="flex-shrink:0;transition:transform .2s;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6,9 12,15 18,9"/></svg>
                        </div>
                    </button>

                    {{-- Accordion body --}}
                    <div id="{{ $accordionId }}" style="display:none;">
                        <div style="padding:.5rem .75rem .75rem;display:flex;flex-direction:column;gap:.4rem;">
                            @foreach($groupLogs as $log)
                            @php
                                $cp     = $log->checkpoint;
                                $isUp   = $log->direction === 'up';
                                $typeColors = [
                                    'gate_in'  => ['#dcfce7','#16a34a'],
                                    'gate_out' => ['#fee2e2','#dc2626'],
                                    'pos'      => ['#dbeafe','#2563eb'],
                                    'summit'   => ['#fef3c7','#d97706'],
                                ];
                                [$typeBg, $typeCl] = $typeColors[$cp?->type ?? 'pos'] ?? ['#f3f4f6','#6b7280'];
                            @endphp
                            <div style="display:flex;align-items:center;gap:.75rem;padding:.625rem .75rem;background:{{ $log->anomaly_flag ? '#fef2f2' : 'white' }};border-radius:8px;border:1px solid {{ $log->anomaly_flag ? '#fecaca' : 'var(--color-border)' }};">
                                {{-- Direction icon --}}
                                <div style="width:32px;height:32px;border-radius:50%;background:{{ $isUp ? '#dcfce7' : '#fee2e2' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="{{ $isUp ? '#16a34a' : '#dc2626' }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        @if($isUp)
                                        <polyline points="18,15 12,9 6,15"/>
                                        @else
                                        <polyline points="6,9 12,15 18,9"/>
                                        @endif
                                    </svg>
                                </div>

                                {{-- Info --}}
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:.8rem;font-weight:600;color:var(--color-text);">{{ $cp?->name ?? 'Pos tidak diketahui' }}</div>
                                    @if($cp?->altitude)
                                    <div style="font-size:.7rem;color:var(--color-text-muted);">{{ number_format($cp->altitude) }} mdpl</div>
                                    @endif
                                    @if($log->anomaly_flag)
                                    <div style="font-size:.68rem;color:#dc2626;font-weight:600;">⚠ {{ $log->anomaly_reason }}</div>
                                    @endif
                                </div>

                                {{-- Right side --}}
                                <div style="text-align:right;flex-shrink:0;">
                                    <span style="font-size:.65rem;font-weight:600;padding:.15rem .45rem;border-radius:20px;background:{{ $typeBg }};color:{{ $typeCl }};">
                                        {{ ['gate_in'=>'Pintu Masuk','gate_out'=>'Pintu Keluar','pos'=>'Pos','summit'=>'Puncak'][$cp?->type ?? 'pos'] ?? $cp?->type }}
                                    </span>
                                    <div style="font-size:.68rem;color:var(--color-text-muted);margin-top:.2rem;">{{ $isUp ? 'Naik' : 'Turun' }}</div>
                                    <div style="font-size:.68rem;color:var(--color-text-muted);">{{ $log->scanned_at->format('d M Y, H:i') }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>

    <script>
    function toggleAccordion(id) {
        const body    = document.getElementById(id);
        const chevron = document.getElementById(id + '-chevron');
        const isOpen  = body.style.display !== 'none';
        body.style.display    = isOpen ? 'none' : 'block';
        chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
    }
    </script>

</x-layouts.web>
