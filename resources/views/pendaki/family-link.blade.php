<x-layouts.mobile>
    <x-slot:title>Family Link</x-slot:title>

    <div style="max-width:680px;">

        <div class="card mb-5">
            <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;">
                <div style="width:40px;height:40px;border-radius:10px;background:#f0fdf4;border:1px solid #bbf7d0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:.9rem;font-weight:700;color:var(--color-text);">Family Link</div>
                    <div style="font-size:.78rem;color:var(--color-text-muted);">Bagikan link ini ke keluarga agar mereka bisa memantau perjalananmu secara real-time — tanpa perlu login.</div>
                </div>
            </div>
        </div>

        @forelse($participants as $participant)
        @php
            $booking  = $participant->booking;
            $qrPass   = $participant->qrPass;
            $hasToken = $qrPass && $qrPass->family_token;
            $trackUrl = $hasToken ? route('public.family-tracking', $qrPass->family_token) : null;
        @endphp
        <div class="card mb-4">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.875rem;">
                <div>
                    <div style="font-size:.875rem;font-weight:700;color:var(--color-text);">{{ $booking?->mountain?->name ?? '—' }}</div>
                    <div style="font-size:.75rem;color:var(--color-text-muted);">
                        {{ $booking?->trail?->name }}
                        &middot; {{ $booking?->start_date?->format('d M') }} – {{ $booking?->end_date?->format('d M Y') }}
                    </div>
                </div>
                @if($booking?->status === 'active')
                <span class="badge badge-green">Aktif</span>
                @elseif($booking?->status === 'paid')
                <span class="badge" style="background:#f0fdf4;color:#15803d;">Dibayar</span>
                @endif
            </div>

            @if($hasToken)
            <div style="background:var(--color-bg);border:1px solid var(--color-border);border-radius:8px;padding:.75rem 1rem;margin-bottom:.75rem;">
                <div style="font-size:.68rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);margin-bottom:.3rem;">Link untuk Keluarga</div>
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <div style="font-size:.78rem;color:var(--color-text);word-break:break-all;flex:1;font-family:monospace;">{{ $trackUrl }}</div>
                    <button
                        onclick="navigator.clipboard.writeText('{{ $trackUrl }}').then(() => { this.textContent = 'Tersalin!'; setTimeout(() => this.textContent = 'Salin', 2000); })"
                        class="btn btn-outline btn-sm"
                        style="flex-shrink:0;font-size:.72rem;"
                    >Salin</button>
                </div>
            </div>

            <div style="display:flex;gap:.75rem;align-items:center;">
                <a href="{{ $trackUrl }}" target="_blank" class="btn btn-ghost btn-sm" style="font-size:.78rem;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15,3 21,3 21,9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    Preview halaman keluarga
                </a>
                <div style="font-size:.72rem;color:var(--color-text-muted);">Halaman diperbarui otomatis setiap 60 detik</div>
            </div>
            @else
            <div style="font-size:.8rem;color:var(--color-text-muted);text-align:center;padding:.75rem;background:var(--color-bg);border-radius:8px;">
                Family token belum tersedia untuk pass ini. Hubungi admin jika diperlukan.
            </div>
            @endif
        </div>
        @empty
        <div class="card" style="text-align:center;padding:3rem 2rem;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto .75rem;display:block;"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            <div style="font-size:.875rem;color:var(--color-text-muted);">Belum ada pendakian aktif. Family link tersedia setelah booking dibayar.</div>
            <a href="{{ route('pendaki.bookings.create') }}" class="btn btn-primary btn-sm mt-4" style="display:inline-flex;">+ Buat Booking Baru</a>
        </div>
        @endforelse

        <div class="card" style="background:var(--color-bg);border-style:dashed;">
            <div style="font-size:.78rem;color:var(--color-text-muted);line-height:1.6;">
                <strong style="color:var(--color-text);">Tentang Family Link:</strong>
                Link ini dapat diakses siapa pun tanpa login. Identitas pendaki ditampilkan dengan nama depan + inisial saja.
                Koordinat GPS tidak ditampilkan di halaman publik — hanya nama pos terakhir yang terdeteksi.
            </div>
        </div>
    </div>
</x-layouts.mobile>
