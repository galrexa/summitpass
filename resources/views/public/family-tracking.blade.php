<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Family Tracking — SummitPass</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background: #f9fafb; color: #111827; margin: 0; }
        .family-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 10;
        }
        .family-logo { display: flex; align-items: center; gap: .5rem; font-weight: 700; font-size: .9rem; color: #111827; }
        .badge-status-active   { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-status-done     { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
        .badge-status-anomaly  { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; animation: blink 1.5s ease-in-out infinite; }
        @keyframes blink { 0%,100% { opacity:1; } 50% { opacity:.5; } }
        .status-badge { font-size: .7rem; font-weight: 700; padding: .25rem .75rem; border-radius: 20px; text-transform: uppercase; letter-spacing: .05em; }
        .main-content { max-width: 480px; margin: 0 auto; padding: 1.5rem; }
        .info-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem; }
        .info-label { font-size: .68rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; margin-bottom: .15rem; }
        .info-value { font-size: .9rem; font-weight: 600; color: #111827; }
        .timeline { position: relative; padding-left: 1.5rem; }
        .timeline::before { content: ''; position: absolute; left: 7px; top: 8px; bottom: 8px; width: 2px; background: #e5e7eb; }
        .timeline-item { position: relative; margin-bottom: 1rem; display: flex; gap: .75rem; }
        .timeline-dot { width: 16px; height: 16px; border-radius: 50%; flex-shrink: 0; margin-top: 2px; border: 2px solid white; box-shadow: 0 0 0 2px #e5e7eb; position: relative; z-index: 1; margin-left: -1.5rem; }
        .dot-green { background: #22c55e; box-shadow: 0 0 0 2px #bbf7d0; }
        .dot-gray  { background: #d1d5db; box-shadow: 0 0 0 2px #e5e7eb; }
        .timeline-content { flex: 1; padding-top: .1rem; }
        .status-card { border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1rem; }
        .status-card.active  { background: #f0fdf4; border: 1px solid #bbf7d0; }
        .status-card.success { background: #f0fdf4; border: 1px solid #86efac; }
        .status-card.warning { background: #fef9c3; border: 1px solid #fde047; }
        .family-footer { text-align: center; padding: 2rem 1.5rem; border-top: 1px solid #e5e7eb; margin-top: 1rem; }
    </style>
</head>
<body>

<div class="family-header">
    <div class="family-logo">
        <img src="{{ asset('logo.png') }}" alt="SummitPass" style="width:28px;height:28px;object-fit:contain;">
        SummitPass
    </div>
    @php
        $isActive  = $qrPass->status === 'active';
        $isDone    = $qrPass->status === 'used';
        $hasAnomaly = $qrPass->trekkingLogs->where('anomaly_flag', true)->count() > 0;
        $statusClass = $hasAnomaly ? 'badge-status-anomaly' : ($isDone ? 'badge-status-done' : 'badge-status-active');
        $statusText  = $hasAnomaly ? 'ANOMALI' : ($isDone ? 'SELESAI' : 'AKTIF');
        $booking = $qrPass->participant?->booking;
        $mountain = $booking?->mountain;
        $participant = $qrPass->participant;
        // Privacy-safe name: first name + initial
        $nameParts = explode(' ', $participant?->name ?? '');
        $displayName = $nameParts[0] . (count($nameParts) > 1 ? ' ' . strtoupper(substr(end($nameParts), 0, 1)) . '.' : '');
    @endphp
    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
</div>

<div class="main-content">

    {{-- Info gunung --}}
    <div class="info-card" style="margin-bottom:1rem;">
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;">
            <div style="width:40px;height:40px;border-radius:10px;background:#f0fdf4;border:1px solid #bbf7d0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20l5-9 4 6 3-4 6 7H3z"/></svg>
            </div>
            <div>
                <div class="info-value">{{ $mountain?->name ?? '—' }}</div>
                <div style="font-size:.75rem;color:#6b7280;">{{ $booking?->trail?->name }}</div>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
            <div>
                <div class="info-label">Pendaki</div>
                <div class="info-value">{{ $displayName }}</div>
            </div>
            <div>
                <div class="info-label">Izin Berlaku</div>
                <div class="info-value" style="font-size:.8rem;">
                    {{ $qrPass->valid_from?->format('d M') }} – {{ $qrPass->valid_until?->format('d M Y') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Status card --}}
    @if($isDone)
    <div class="status-card success">
        ✅ <strong>Pendaki telah kembali dengan selamat.</strong><br>
        <small style="color:#6b7280;">Checkout: {{ $qrPass->trekkingLogs->last()?->scanned_at?->format('d M Y, H:i') }} WIB</small>
    </div>
    @elseif($hasAnomaly)
    <div class="status-card warning">
        ⚠️ <strong>Tim pengelola telah diberitahu.</strong><br>
        <small style="color:#a16207;">Tidak perlu panik — pantau terus halaman ini. Petugas sedang menangani.</small>
    </div>
    @elseif($isActive)
    <div class="status-card active">
        🟢 <strong>Pendakian sedang berlangsung.</strong><br>
        <small style="color:#6b7280;">
            Pos terakhir diketahui: {{ $qrPass->trekkingLogs->last()?->checkpoint?->name ?? 'Gerbang Masuk' }}
            @if($qrPass->trekkingLogs->last())
            &middot; {{ $qrPass->trekkingLogs->last()->scanned_at?->diffForHumans() }}
            @endif
        </small>
    </div>
    @endif

    {{-- Timeline pos --}}
    <div class="info-card">
        <div style="font-size:.8rem;font-weight:700;color:#111827;margin-bottom:1rem;">Rekam Jejak Pos</div>
        @php
            $checkpoints = $booking?->trail?->checkpoints?->sortBy('order_seq') ?? collect();
            $scannedLogs = $qrPass->trekkingLogs->keyBy('trail_checkpoint_id');
        @endphp
        <div class="timeline">
            @forelse($checkpoints as $cp)
            @php
                $log = $scannedLogs[$cp->id] ?? null;
                $status = $log ? 'passed' : 'pending';
            @endphp
            <div class="timeline-item">
                <div class="timeline-dot {{ $status === 'passed' ? 'dot-green' : 'dot-gray' }}"></div>
                <div class="timeline-content">
                    <div style="font-size:.85rem;font-weight:600;color:{{ $status === 'passed' ? '#111827' : '#9ca3af' }};">
                        {{ $cp->name }}
                    </div>
                    @if($log)
                    <div style="font-size:.72rem;color:#6b7280;margin-top:.1rem;">
                        ✓ {{ $log->scanned_at?->format('H:i') }} WIB &middot; {{ $log->scanned_at?->diffForHumans() }}
                    </div>
                    @else
                    <div style="font-size:.72rem;color:#9ca3af;margin-top:.1rem;">Belum dilewati</div>
                    @endif
                </div>
            </div>
            @empty
            <div style="font-size:.82rem;color:#9ca3af;text-align:center;padding:1rem 0;">Data jalur tidak tersedia.</div>
            @endforelse
        </div>
    </div>

</div>

<div class="family-footer">
    <div style="font-size:.72rem;color:#6b7280;">
        Halaman ini diperbarui otomatis setiap 60 detik.<br>
        Butuh bantuan darurat? Hubungi <strong>Basarnas: 115</strong>
    </div>
    <div style="font-size:.72rem;color:#9ca3af;margin-top:.5rem;">
        Powered by SummitPass &middot; Sistem Keselamatan Pendakian Nasional
    </div>
</div>

<script>
    setTimeout(() => location.reload(), 60000);
</script>
</body>
</html>
