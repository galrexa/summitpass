<x-layouts.web>
    <x-slot:title>Detail Booking</x-slot:title>
    <x-slot:breadcrumb>['SummitPass', 'Booking Saya', 'Detail']</x-slot:breadcrumb>

    <div style="max-width:640px;">

        {{-- Status banner --}}
        @if($booking->status === 'pending_payment')
        <div style="background:#fef3c7;border:1px solid #f59e0b;border-radius:12px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
            <div>
                <div style="font-size:.875rem;font-weight:700;color:#92400e;margin-bottom:.25rem;">Menunggu Pembayaran</div>
                <div style="font-size:.8rem;color:#b45309;">Selesaikan pembayaran untuk mendapatkan kode booking & QR SummitPass.</div>
            </div>
            <form method="POST" action="{{ route('pendaki.bookings.pay', $booking->id) }}">
                @csrf
                <button type="submit" class="btn btn-sm" style="background:#f59e0b;color:#fff;border:none;white-space:nowrap;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    Bayar Sekarang
                </button>
            </form>
        </div>

        @elseif($booking->status === 'paid' || $booking->status === 'active')
        <div style="background:#dcfce7;border:1px solid #86efac;border-radius:12px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1rem;">
            <div style="width:36px;height:36px;background:var(--color-forest-600);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg>
            </div>
            <div>
                <div style="font-size:.875rem;font-weight:700;color:#15803d;">Booking Dikonfirmasi</div>
                <div style="font-size:.8rem;color:#16a34a;">Kode booking aktif. QR SummitPass siap digunakan.</div>
            </div>
        </div>

        @elseif($booking->status === 'completed')
        <div style="background:var(--color-surface-alt);border:1px solid var(--color-border);border-radius:12px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;">
            <div style="font-size:.875rem;font-weight:600;color:var(--color-text-muted);">Pendakian selesai</div>
        </div>
        @endif

        {{-- Booking code --}}
        @if($booking->booking_code)
        <div style="background:var(--color-forest-700);border-radius:12px;padding:1.5rem;text-align:center;margin-bottom:1.25rem;">
            <div style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.6);margin-bottom:.5rem;">Kode Booking</div>
            <div style="font-size:1.75rem;font-weight:800;font-family:monospace;color:#fff;letter-spacing:.1em;">{{ $booking->booking_code }}</div>
            <div style="font-size:.72rem;color:rgba(255,255,255,.5);margin-top:.5rem;">Tunjukkan kode ini kepada petugas pos</div>
        </div>
        @endif

        {{-- Detail card --}}
        <div class="card" style="margin-bottom:1.25rem;">
            <div style="font-size:.825rem;font-weight:700;color:var(--color-text);margin-bottom:1rem;padding-bottom:.75rem;border-bottom:1px solid var(--color-border);">
                Rincian Pendakian
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                @foreach([
                    ['Gunung', $booking->mountain->name ?? '-'],
                    ['Tanggal Naik', $booking->start_date->format('d M Y')],
                    ['Tanggal Turun', $booking->end_date->format('d M Y')],
                    ['Durasi', $booking->start_date->diffInDays($booking->end_date) + 1 . ' hari'],
                    ['Pemandu', $booking->guide_requested ? 'Ya' : 'Tidak'],
                ] as [$label, $value])
                <div>
                    <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);margin-bottom:.2rem;">{{ $label }}</div>
                    <div style="font-size:.875rem;color:var(--color-text);">{{ $value }}</div>
                </div>
                @endforeach

                {{-- ← BAGIAN JALUR: DIPISAH agar bisa menampilkan dua jalur untuk lintas jalur --}}
                <div @if($booking->is_cross_trail) style="grid-column: span 2;" @endif>
                    <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);margin-bottom:.4rem;">
                        Jalur Pendakian
                        @if($booking->is_cross_trail)
                            <span style="margin-left:.4rem;padding:.1rem .5rem;border-radius:99px;background:#fef3c7;color:#92400e;font-size:.65rem;font-weight:700;">LINTAS JALUR</span>
                        @endif
                    </div>
                    @if($booking->is_cross_trail)
                        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
                            <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .7rem;border-radius:6px;background:#dcfce7;color:#166534;font-size:.8rem;font-weight:600;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
                                {{ $booking->trail->name ?? '-' }}
                            </span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                            <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .7rem;border-radius:6px;background:#ede9fe;color:#5b21b6;font-size:.8rem;font-weight:600;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                                {{ $booking->effectiveTrailOut()->name ?? '-' }}
                            </span>
                        </div>
                    @else
                        <div style="font-size:.875rem;color:var(--color-text);">{{ $booking->trail->name ?? '-' }}</div>
                    @endif
                </div>
            </div>

            <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--color-border);display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);margin-bottom:.2rem;">Total Biaya SIMAKSI</div>
                    <div style="font-size:1.1rem;font-weight:800;color:var(--color-forest-700);">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</div>
                </div>
                <span class="badge
                    @if($booking->status === 'pending_payment') badge-yellow
                    @elseif($booking->status === 'paid') badge-green
                    @elseif($booking->status === 'active') badge-blue
                    @elseif($booking->status === 'completed') badge-gray
                    @else badge-red @endif"
                >
                    {{ ['pending_payment'=>'Menunggu Bayar','paid'=>'Dibayar','active'=>'Aktif','completed'=>'Selesai','cancelled'=>'Dibatalkan'][$booking->status] ?? $booking->status }}
                </span>
            </div>
        </div>

        {{-- Participants --}}
        <div class="card" style="margin-bottom:1.25rem;">
            <div style="font-size:.825rem;font-weight:700;color:var(--color-text);margin-bottom:1rem;">
                Peserta ({{ $booking->participants->count() }} orang)
            </div>
            @foreach($booking->participants as $pax)
            <div style="display:flex;align-items:center;gap:.75rem;padding:.625rem 0;border-bottom:1px solid var(--color-border);"
                 style="{{ $loop->last ? 'border-bottom:none' : '' }}">
                <div class="avatar" style="width:32px;height:32px;font-size:.7rem;flex-shrink:0;">
                    {{ strtoupper(substr($pax->name, 0, 2)) }}
                </div>
                <div style="flex:1;">
                    <div style="font-size:.875rem;font-weight:600;color:var(--color-text);">{{ $pax->name }}</div>
                    <div style="font-size:.72rem;color:var(--color-text-muted);">NIK: {{ substr($pax->nik, 0, 4) }} •••• •••• {{ substr($pax->nik, -4) }}</div>
                </div>
                <span class="badge {{ $pax->role === 'leader' ? 'badge-green' : 'badge-gray' }}" style="font-size:.65rem;">
                    {{ $pax->role === 'leader' ? 'Leader' : 'Member' }}
                </span>
            </div>
            @endforeach
        </div>

        {{-- Actions --}}
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
            <a href="{{ route('pendaki.bookings') }}" class="btn btn-ghost btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15,18 9,12 15,6"/></svg>
                Kembali
            </a>
            @if(in_array($booking->status, ['paid', 'active']))
            <a href="{{ route('pendaki.my-pass') }}" class="btn btn-primary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="3" height="3" rx=".5"/></svg>
                Lihat QR Pass
            </a>
            @endif
        </div>

    </div>

</x-layouts.web>
