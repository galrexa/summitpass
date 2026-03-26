<x-layouts.web>
    <x-slot:title>QR SummitPass</x-slot:title>
    <x-slot:breadcrumb>['SummitPass', 'QR Pass Saya']</x-slot:breadcrumb>

    <x-slot:head>
        <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    </x-slot:head>

    <div style="max-width:460px;">

        @if(!$user->nik && !$user->passport_number)
        {{-- Profile incomplete --}}
        <div style="text-align:center;padding:3rem 1rem;">
            <div style="width:64px;height:64px;background:#fef3c7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <h3 style="font-size:1rem;font-weight:700;color:var(--color-text);margin-bottom:.5rem;">QR belum tersedia</h3>
            <p style="font-size:.825rem;color:var(--color-text-muted);margin-bottom:1.5rem;line-height:1.6;">QR SummitPass dihasilkan setelah kamu melengkapi identitas (NIK/Paspor) dan memiliki booking aktif.</p>
            <a href="{{ route('profile.setup') }}" class="btn btn-primary btn-sm">Lengkapi Profil</a>
        </div>

        @elseif($bookings->isEmpty())
        {{-- No paid/active bookings --}}
        <div style="text-align:center;padding:3rem 1rem;">
            <div style="width:64px;height:64px;background:var(--color-forest-100);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="3" height="3" rx=".5"/></svg>
            </div>
            <h3 style="font-size:1rem;font-weight:700;color:var(--color-text);margin-bottom:.5rem;">Belum ada booking aktif</h3>
            <p style="font-size:.825rem;color:var(--color-text-muted);margin-bottom:1.5rem;line-height:1.6;">QR SummitPass akan muncul setelah kamu memiliki booking yang sudah dibayar.</p>
            <a href="{{ route('pendaki.bookings') }}" class="btn btn-primary btn-sm">Lihat Booking Saya</a>
        </div>

        @else
        {{-- QR Pass cards --}}
        @foreach($bookings as $booking)
        @php
            $participant = $booking->participants->first();
            $qrPass      = $participant?->qrPass;
            $isActive    = $qrPass && now()->between($booking->start_date, $booking->end_date->endOfDay());
            $isPending   = $booking->start_date->isFuture();
        @endphp

        <div class="card" style="margin-bottom:1.25rem;overflow:hidden;">
            {{-- Header strip --}}
            <div style="background:var(--color-forest-700);padding:.875rem 1.125rem;display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:.7rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--color-forest-300);">SummitPass</div>
                    <div style="font-size:.95rem;font-weight:700;color:white;margin-top:.1rem;">{{ $booking->mountain->name }}</div>
                    <div style="font-size:.75rem;color:var(--color-forest-300);margin-top:.1rem;">{{ $booking->trail->name }}</div>
                </div>
                @if($isActive)
                <span style="background:#22c55e;color:white;font-size:.7rem;font-weight:700;padding:.3rem .65rem;border-radius:20px;">AKTIF</span>
                @elseif($isPending)
                <span style="background:#f59e0b;color:white;font-size:.7rem;font-weight:700;padding:.3rem .65rem;border-radius:20px;">BELUM AKTIF</span>
                @else
                <span style="background:#6b7280;color:white;font-size:.7rem;font-weight:700;padding:.3rem .65rem;border-radius:20px;">KADALUARSA</span>
                @endif
            </div>

            {{-- Body --}}
            <div style="padding:1.25rem;display:flex;gap:1.25rem;align-items:flex-start;flex-wrap:wrap;">

                {{-- QR Code --}}
                <div style="flex-shrink:0;">
                    @if($qrPass)
                    <div id="qr-{{ $qrPass->id }}"
                         style="width:160px;height:160px;background:#f9fafb;border-radius:8px;display:flex;align-items:center;justify-content:center;overflow:hidden;"
                         data-token="{{ $qrPass->qr_token }}">
                    </div>
                    @else
                    <div style="width:160px;height:160px;background:#f3f4f6;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:.75rem;text-align:center;padding:.5rem;">
                        QR tidak tersedia
                    </div>
                    @endif
                </div>

                {{-- Info --}}
                <div style="flex:1;min-width:0;">
                    <div style="margin-bottom:.875rem;">
                        <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);">Nama Pendaki</div>
                        <div style="font-size:.9rem;font-weight:600;color:var(--color-text);margin-top:.15rem;">{{ $user->name }}</div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem 1rem;">
                        <div>
                            <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);">Tanggal Masuk</div>
                            <div style="font-size:.82rem;color:var(--color-text);margin-top:.1rem;">{{ $booking->start_date->format('d M Y') }}</div>
                        </div>
                        <div>
                            <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);">Tanggal Keluar</div>
                            <div style="font-size:.82rem;color:var(--color-text);margin-top:.1rem;">{{ $booking->end_date->format('d M Y') }}</div>
                        </div>
                        <div>
                            <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);">Kode Booking</div>
                            <div style="font-size:.82rem;font-weight:700;color:var(--color-forest-700);font-family:monospace;margin-top:.1rem;">{{ $booking->booking_code }}</div>
                        </div>
                        <div>
                            <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);">Peserta</div>
                            <div style="font-size:.82rem;color:var(--color-text);margin-top:.1rem;">{{ $booking->participants->count() }} orang</div>
                        </div>
                    </div>

                    @if($isPending)
                    <div style="margin-top:.875rem;padding:.625rem .75rem;background:#fef3c7;border-radius:6px;font-size:.75rem;color:#92400e;line-height:1.5;">
                        QR aktif otomatis pada <strong>{{ $booking->start_date->format('d M Y') }}</strong>. Tunjukkan ke petugas pos saat masuk.
                    </div>
                    @endif
                </div>
            </div>

            {{-- Token strip --}}
            @if($qrPass)
            <div style="border-top:1px dashed var(--color-border);padding:.5rem 1.125rem;background:#f9fafb;">
                <div style="font-size:.65rem;font-family:monospace;color:var(--color-text-muted);word-break:break-all;">{{ $qrPass->qr_token }}</div>
            </div>
            @endif
        </div>
        @endforeach
        @endif

    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-token]').forEach(function (el) {
            new QRCode(el, {
                text: el.dataset.token,
                width: 160,
                height: 160,
                colorDark: '#1a2e1a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M,
            });
        });
    });
    </script>

</x-layouts.web>
