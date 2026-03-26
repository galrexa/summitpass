<x-layouts.web>
    <x-slot:title>Jejak Summit</x-slot:title>
    <x-slot:breadcrumb>['SummitPass', 'Jejak Summit']</x-slot:breadcrumb>

    <div style="max-width:640px;">

        {{-- Summit Stats --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;">
            <div class="card" style="text-align:center;padding:1rem .75rem;">
                <div style="font-size:1.75rem;font-weight:800;color:var(--color-forest-600);line-height:1;">{{ $uniqueMountains->count() }}</div>
                <div style="font-size:.72rem;color:var(--color-text-muted);margin-top:.4rem;text-transform:uppercase;letter-spacing:.04em;">Gunung Didaki</div>
            </div>
            <div class="card" style="text-align:center;padding:1rem .75rem;">
                <div style="font-size:1.75rem;font-weight:800;color:var(--color-lake-700);line-height:1;">{{ $highestMdpl > 0 ? number_format($highestMdpl) : '—' }}</div>
                <div style="font-size:.72rem;color:var(--color-text-muted);margin-top:.4rem;text-transform:uppercase;letter-spacing:.04em;">Tertinggi (mdpl)</div>
            </div>
            <div class="card" style="text-align:center;padding:1rem .75rem;">
                <div style="font-size:1.75rem;font-weight:800;color:#d97706;line-height:1;">{{ number_format($summitPoints) }}</div>
                <div style="font-size:.72rem;color:var(--color-text-muted);margin-top:.4rem;text-transform:uppercase;letter-spacing:.04em;">Summit Points</div>
            </div>
        </div>

        {{-- Badges --}}
        <div class="card" style="margin-bottom:1.25rem;">
            <h3 style="font-size:.875rem;font-weight:700;color:var(--color-text);margin-bottom:.875rem;">Badge & Pencapaian</h3>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.625rem;">
                @foreach($allBadges as [$id, $label, $desc, $earned, $color])
                <div style="text-align:center;padding:.75rem .5rem;border-radius:10px;background:{{ $earned ? 'white' : '#f9fafb' }};border:1.5px solid {{ $earned ? $color.'33' : 'var(--color-border)' }};position:relative;opacity:{{ $earned ? '1' : '.55' }};">
                    {{-- Icon circle --}}
                    <div style="width:40px;height:40px;border-radius:50%;background:{{ $earned ? $color.'1a' : '#f3f4f6' }};display:flex;align-items:center;justify-content:center;margin:0 auto .5rem;">
                        @if($earned)
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $color }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        @else
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        @endif
                    </div>
                    <div style="font-size:.72rem;font-weight:700;color:{{ $earned ? $color : '#9ca3af' }};line-height:1.2;">{{ $label }}</div>
                    <div style="font-size:.65rem;color:var(--color-text-muted);margin-top:.2rem;line-height:1.3;">{{ $desc }}</div>
                    @if($earned)
                    <div style="position:absolute;top:.35rem;right:.35rem;width:8px;height:8px;border-radius:50%;background:{{ $color }};"></div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Riwayat Pendakian --}}
        <div class="card">
            <h3 style="font-size:.875rem;font-weight:700;color:var(--color-text);margin-bottom:1rem;">Riwayat Pendakian</h3>

            @if($completedBookings->isEmpty())
            <div style="text-align:center;padding:2.5rem 0;">
                <div style="width:48px;height:48px;background:var(--color-forest-100);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto .875rem;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20l5-9 4 6 3-4 6 7H3z"/></svg>
                </div>
                <h3 style="font-size:.9rem;font-weight:600;color:var(--color-text);margin-bottom:.4rem;">Belum ada pendakian selesai</h3>
                <p style="font-size:.8rem;color:var(--color-text-muted);max-width:300px;margin:0 auto .75rem;">Selesaikan pendakianmu dan rekam jejakmu di sini.</p>
                <a href="{{ route('pendaki.bookings.create') }}" class="btn btn-outline btn-sm">Booking Sekarang</a>
            </div>
            @else
            <div style="display:flex;flex-direction:column;gap:.625rem;">
                @foreach($completedBookings as $booking)
                @php $mt = $booking->mountain; @endphp
                <div style="display:flex;align-items:center;gap:.875rem;padding:.75rem;background:var(--color-bg-subtle,#f9fafb);border-radius:8px;">
                    <div style="width:40px;height:40px;background:var(--color-forest-100);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20l5-9 4 6 3-4 6 7H3z"/></svg>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:.875rem;font-weight:600;color:var(--color-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $mt?->name ?? 'Gunung' }}</div>
                        <div style="font-size:.75rem;color:var(--color-text-muted);">{{ $mt?->location }} &middot; {{ number_format($mt?->height_mdpl ?? 0) }} mdpl</div>
                        <div style="font-size:.72rem;color:var(--color-text-muted);margin-top:.1rem;">{{ $booking->end_date->format('d M Y') }}</div>
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        @php
                            $gradeBadge = ['I'=>['#dcfce7','#16a34a'],'II'=>['#dcfce7','#16a34a'],'III'=>['#fef3c7','#d97706'],'IV'=>['#fee2e2','#dc2626'],'V'=>['#fef2f2','#991b1b']];
                            [$bg,$cl] = $gradeBadge[$mt?->grade ?? 'I'] ?? ['#f3f4f6','#6b7280'];
                        @endphp
                        <span style="font-size:.7rem;font-weight:600;padding:.2rem .5rem;border-radius:20px;background:{{ $bg }};color:{{ $cl }};">Grade {{ $mt?->grade }}</span>
                        <div style="font-size:.72rem;color:#d97706;font-weight:700;margin-top:.25rem;">+{{ number_format($mt?->height_mdpl ?? 0) }} pts</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>

</x-layouts.web>
