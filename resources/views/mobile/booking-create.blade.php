<x-layouts.mobile :showBack="true" :hideNav="true">
    <x-slot:title>Booking Pendakian</x-slot:title>
    <x-slot:subtitle>Langkah {{ $step ?? 1 }} dari 5</x-slot:subtitle>

    {{-- Step indicator --}}
    <div class="px-4 py-3" style="background:var(--color-surface);border-bottom:1px solid var(--color-border);">
        <div class="step-indicator">
            @for($i = 1; $i <= 5; $i++)
                <div class="step-dot {{ $i < ($step ?? 1) ? 'done' : ($i == ($step ?? 1) ? 'active' : '') }}">
                    @if($i < ($step ?? 1))
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg>
                    @else
                        {{ $i }}
                    @endif
                </div>
                @if($i < 5)
                <div class="step-line {{ $i < ($step ?? 1) ? 'done' : '' }}"></div>
                @endif
            @endfor
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:0.5rem;">
            @foreach(['Gunung', 'Tanggal', 'Peserta', 'Guide', 'Bayar'] as $i => $label)
            <span style="font-size:0.62rem;color:{{ ($i+1) == ($step ?? 1) ? 'var(--color-forest-700)' : 'var(--color-text-muted)' }};font-weight:{{ ($i+1) == ($step ?? 1) ? '600' : '400' }};text-align:center;flex:1;">{{ $label }}</span>
            @endforeach
        </div>
    </div>

    {{-- Step 1: Pilih Gunung (example) --}}
    <div class="px-4 pt-5">
        <h2 style="font-size:1.1rem;font-weight:700;color:var(--color-text);margin-bottom:0.375rem;">Pilih Gunung</h2>
        <p style="font-size:0.825rem;color:var(--color-text-muted);margin-bottom:1.5rem;">Sistem akan menampilkan regulasi dan sisa kuota secara otomatis.</p>

        {{-- Search --}}
        <div style="position:relative;margin-bottom:1rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 style="position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);pointer-events:none;">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" placeholder="Cari gunung..." class="form-input" style="padding-left:2.5rem;">
        </div>

        {{-- Mountain list --}}
        @php
        $mountains = [
            ['name' => 'Semeru', 'province' => 'Jawa Timur', 'elevation' => '3.676 mdpl', 'quota' => 300, 'remaining' => 28, 'trails' => 1],
            ['name' => 'Rinjani', 'province' => 'Nusa Tenggara Barat', 'elevation' => '3.726 mdpl', 'quota' => 80, 'remaining' => 25, 'trails' => 3],
            ['name' => 'Gede', 'province' => 'Jawa Barat', 'elevation' => '2.958 mdpl', 'quota' => 100, 'remaining' => 60, 'trails' => 2],
            ['name' => 'Merbabu', 'province' => 'Jawa Tengah', 'elevation' => '3.145 mdpl', 'quota' => 60, 'remaining' => 40, 'trails' => 3],
        ];
        @endphp

        <div style="display:flex;flex-direction:column;gap:0.75rem;">
            @foreach($mountains as $m)
            @php $pct = round(($m['quota'] - $m['remaining']) / $m['quota'] * 100); $full = $m['remaining'] <= 5; @endphp
            <label style="cursor:{{ $full ? 'not-allowed' : 'pointer' }};opacity:{{ $full ? '0.6' : '1' }};">
                <input type="radio" name="mountain_id" value="{{ $m['name'] }}" {{ $full ? 'disabled' : '' }} style="display:none;" class="peer">
                <div class="card card-sm" style="border-color:var(--color-border);transition:border-color 0.15s,box-shadow 0.15s;"
                     x-data
                     @click="$el.closest('label').querySelector('input').checked = true; document.querySelectorAll('.mountain-card').forEach(c=>c.style.borderColor='var(--color-border)'); $el.style.borderColor='var(--color-forest-600)';"
                     class="mountain-card">
                    <div style="display:flex;align-items:flex-start;gap:0.75rem;">
                        <div style="width:40px;height:40px;border-radius:10px;background:var(--color-forest-100);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 20l5-9 4 6 3-4 6 7H3z"/>
                            </svg>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:0.5rem;">
                                <span style="font-size:0.9rem;font-weight:700;color:var(--color-text);">{{ $m['name'] }}</span>
                                @if($full)
                                <span class="badge badge-red">Penuh</span>
                                @else
                                <span class="badge badge-green">{{ $m['remaining'] }} sisa</span>
                                @endif
                            </div>
                            <div style="font-size:0.75rem;color:var(--color-text-muted);margin-top:0.15rem;">{{ $m['province'] }} &middot; {{ $m['elevation'] }}</div>
                            <div style="margin-top:0.625rem;height:4px;background:var(--color-forest-100);border-radius:2px;overflow:hidden;">
                                <div style="height:100%;width:{{ $pct }}%;background:{{ $pct >= 90 ? '#ef4444' : ($pct >= 70 ? '#f59e0b' : 'var(--color-forest-500)') }};border-radius:2px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </label>
            @endforeach
        </div>

        {{-- Next button --}}
        <div style="margin-top:2rem;padding-bottom:1rem;">
            <button class="btn btn-primary w-full btn-lg">
                Lanjut — Pilih Tanggal
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/>
                </svg>
            </button>
        </div>
    </div>

</x-layouts.mobile>
