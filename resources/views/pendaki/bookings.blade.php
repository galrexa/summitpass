<x-layouts.web>
    <x-slot:title>Booking Saya</x-slot:title>
    <x-slot:breadcrumb>['SummitPass', 'Booking Saya']</x-slot:breadcrumb>

    {{-- Profile incomplete warning --}}
    @if(!Auth::user()->nik && !Auth::user()->passport_number)
    <div class="alert" style="background:#fef3c7;border:1px solid #f59e0b;border-radius:10px;display:flex;align-items:center;gap:.75rem;padding:.875rem 1.25rem;margin-bottom:1.5rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#b45309" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <span style="font-size:.875rem;color:#92400e;">
            Lengkapi identitas dirimu (NIK/Paspor) sebelum bisa booking.
            <a href="{{ route('profile.setup') }}" style="font-weight:600;color:#78350f;text-decoration:underline;">Lengkapi sekarang</a>
        </span>
    </div>
    @endif

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:.75rem;">
        <div>
            <h2 style="font-size:1rem;font-weight:700;color:var(--color-text);">Booking Saya</h2>
            <p style="font-size:.8rem;color:var(--color-text-muted);margin-top:.15rem;">Riwayat dan jadwal pendakianmu.</p>
        </div>
        <a href="{{ route('pendaki.bookings.create') }}"
           class="btn btn-primary btn-sm"
           @if(!Auth::user()->nik && !Auth::user()->passport_number) style="opacity:.5;pointer-events:none;" @endif>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Booking Baru
        </a>
    </div>

    @if($bookings->isEmpty())
    {{-- Empty state --}}
    <div style="text-align:center;padding:4rem 2rem;">
        <div style="width:56px;height:56px;background:var(--color-forest-100);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
        </div>
        <h3 style="font-size:.95rem;font-weight:600;color:var(--color-text);margin-bottom:.5rem;">Belum ada booking</h3>
        <p style="font-size:.825rem;color:var(--color-text-muted);max-width:320px;margin:0 auto 1.5rem;">Mulai perjalananmu dengan booking SIMAKSI digital.</p>
        <a href="{{ route('pendaki.bookings.create') }}" class="btn btn-primary btn-sm">Booking Sekarang</a>
    </div>

    @else
    {{-- Booking list --}}
    <div style="display:flex;flex-direction:column;gap:.875rem;">
        @foreach($bookings as $booking)
        @php
            $statusMap = [
                'pending_payment' => ['label'=>'Menunggu Bayar', 'class'=>'badge-yellow'],
                'paid'            => ['label'=>'Dibayar',        'class'=>'badge-green'],
                'active'          => ['label'=>'Aktif',          'class'=>'badge-blue'],
                'completed'       => ['label'=>'Selesai',        'class'=>'badge-gray'],
                'cancelled'       => ['label'=>'Dibatalkan',     'class'=>'badge-red'],
            ];
            $s = $statusMap[$booking->status] ?? ['label'=>$booking->status,'class'=>'badge-gray'];
        @endphp
        <a href="{{ route('pendaki.bookings.show', $booking->id) }}"
           style="display:block;text-decoration:none;">
            <div class="card" style="transition:box-shadow .15s;cursor:pointer;"
                 onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'"
                 onmouseout="this.style.boxShadow=''">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                    <div style="flex:1;min-width:200px;">
                        <div style="display:flex;align-items:center;gap:.625rem;margin-bottom:.375rem;">
                            <span style="font-size:.9rem;font-weight:700;color:var(--color-text);">{{ $booking->mountain->name ?? '-' }}</span>
                            <span style="font-size:.72rem;color:var(--color-text-muted);">·</span>
                            <span style="font-size:.8rem;color:var(--color-text-muted);">{{ $booking->trail->name ?? '-' }}</span>
                        </div>
                        <div style="font-size:.78rem;color:var(--color-text-muted);">
                            {{ $booking->start_date->format('d M Y') }} — {{ $booking->end_date->format('d M Y') }}
                            · {{ $booking->participants->count() }} peserta
                        </div>
                        @if($booking->booking_code)
                        <div style="margin-top:.5rem;font-size:.72rem;font-family:monospace;font-weight:700;color:var(--color-forest-700);">
                            {{ $booking->booking_code }}
                        </div>
                        @endif
                    </div>
                    <div style="display:flex;align-items:center;gap:.75rem;flex-shrink:0;">
                        <div style="text-align:right;">
                            <div style="font-size:.875rem;font-weight:700;color:var(--color-forest-700);">
                                Rp{{ number_format($booking->total_price, 0, ',', '.') }}
                            </div>
                            <span class="badge {{ $s['class'] }}" style="font-size:.65rem;">{{ $s['label'] }}</span>
                        </div>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9,18 15,12 9,6"/></svg>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif

</x-layouts.web>
