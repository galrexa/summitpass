<x-layouts.web>
    <x-slot:title>Booking {{ $booking->booking_code ?? '#'.$booking->id }}</x-slot:title>
    <x-slot:breadcrumb>['Admin', 'Booking / SIMAKSI', $booking->booking_code ?? '#'.$booking->id]</x-slot:breadcrumb>

    @php
    $statusMap = [
        'pending_payment' => ['class' => 'badge-yellow', 'label' => 'Menunggu Pembayaran'],
        'paid'            => ['class' => 'badge-blue',   'label' => 'Dibayar'],
        'active'          => ['class' => 'badge-green',  'label' => 'Aktif di Jalur'],
        'completed'       => ['class' => 'badge-gray',   'label' => 'Selesai'],
        'cancelled'       => ['class' => 'badge-red',    'label' => 'Dibatalkan'],
    ];
    $s = $statusMap[$booking->status] ?? ['class'=>'badge-gray','label'=>$booking->status];
    @endphp

    {{-- Header --}}
    <div class="flex items-start justify-between mb-5 gap-4 flex-wrap">
        <div>
            <div class="flex items-center gap-3 mb-1 flex-wrap">
                <h2 class="text-xl font-bold font-mono" style="color:var(--color-text);">
                    {{ $booking->booking_code ?? 'Belum ada kode' }}
                </h2>
                <span class="badge {{ $s['class'] }}">{{ $s['label'] }}</span>
            </div>
            <p class="text-sm" style="color:var(--color-text-muted);">
                Dibuat {{ $booking->created_at->diffForHumans() }}
                &middot; {{ $booking->created_at->format('d M Y, H:i') }}
            </p>
        </div>
        <div class="flex gap-2 flex-wrap">
            {{-- Konfirmasi pembayaran (jika pending) --}}
            @if($booking->status === 'pending_payment' && $booking->payment?->status === 'pending')
            <form method="POST" action="{{ route('admin.bookings.confirm-payment', $booking->id) }}"
                  onsubmit="return confirm('Konfirmasi pembayaran booking ini?')">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">Konfirmasi Pembayaran</button>
            </form>
            @endif

            {{-- Batalkan --}}
            @if(in_array($booking->status, ['pending_payment', 'paid']))
            <form method="POST" action="{{ route('admin.bookings.cancel', $booking->id) }}"
                  onsubmit="return confirm('Batalkan booking ini? Tindakan ini tidak dapat dibatalkan.')">
                @csrf
                <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;">
                    Batalkan Booking
                </button>
            </form>
            @endif

            <a href="{{ route('admin.bookings.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Kolom kiri: Info utama --}}
        <div class="lg:col-span-2 flex flex-col gap-5">

            {{-- Info Pendakian --}}
            <div class="card">
                <h3 class="font-semibold text-sm mb-4" style="color:var(--color-text);">Informasi Pendakian</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <div class="text-xs mb-1" style="color:var(--color-text-muted);">Gunung</div>
                        <div class="text-sm font-semibold">{{ $booking->mountain?->name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs mb-1" style="color:var(--color-text-muted);">Jalur</div>
                        <div class="text-sm font-semibold">{{ $booking->trail?->name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs mb-1" style="color:var(--color-text-muted);">Tanggal Mulai</div>
                        <div class="text-sm font-semibold">{{ $booking->start_date?->format('d M Y') }}</div>
                    </div>
                    <div>
                        <div class="text-xs mb-1" style="color:var(--color-text-muted);">Tanggal Selesai</div>
                        <div class="text-sm font-semibold">{{ $booking->end_date?->format('d M Y') }}</div>
                    </div>
                    <div>
                        <div class="text-xs mb-1" style="color:var(--color-text-muted);">Durasi</div>
                        <div class="text-sm font-semibold">
                            {{ $booking->start_date->diffInDays($booking->end_date) + 1 }} hari
                        </div>
                    </div>
                    <div>
                        <div class="text-xs mb-1" style="color:var(--color-text-muted);">Guide</div>
                        <div class="text-sm font-semibold">
                            {{ $booking->guide_requested ? 'Diminta' : 'Tidak' }}
                        </div>
                    </div>
                    @if($booking->notes)
                    <div class="col-span-2 sm:col-span-3">
                        <div class="text-xs mb-1" style="color:var(--color-text-muted);">Catatan</div>
                        <div class="text-sm">{{ $booking->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Daftar Peserta --}}
            <div class="card" style="padding:0;overflow:hidden;">
                <div class="px-5 py-4" style="border-bottom:1px solid var(--color-border);">
                    <h3 class="font-semibold text-sm" style="color:var(--color-text);">
                        Peserta ({{ $booking->participants->count() }})
                    </h3>
                </div>
                <div style="overflow-x:auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>Peran</th>
                                <th>QR Pass</th>
                                <th>Status QR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking->participants as $i => $participant)
                            <tr>
                                <td class="text-center text-xs font-mono">{{ $i + 1 }}</td>
                                <td class="font-medium text-sm">{{ $participant->name }}</td>
                                <td class="font-mono text-xs" style="color:var(--color-text-muted);">
                                    {{ $participant->masked_nik }}
                                </td>
                                <td>
                                    @if($participant->role === 'leader')
                                    <span class="badge badge-blue">Leader</span>
                                    @else
                                    <span class="badge badge-gray">Member</span>
                                    @endif
                                </td>
                                <td class="font-mono text-xs">
                                    @if($participant->qrPass)
                                    {{ Str::limit($participant->qrPass->qr_token, 20) }}
                                    @else
                                    <span style="color:var(--color-text-muted);">Belum diterbitkan</span>
                                    @endif
                                </td>
                                <td>
                                    @if($participant->qrPass)
                                    @php
                                    $qrStatusMap = ['inactive'=>['badge-gray','Belum Aktif'],'active'=>['badge-green','Aktif'],'used'=>['badge-blue','Digunakan'],'expired'=>['badge-red','Kadaluarsa'],'revoked'=>['badge-red','Dicabut']];
                                    [$qrClass, $qrLabel] = $qrStatusMap[$participant->qrPass->status] ?? ['badge-gray', $participant->qrPass->status];
                                    @endphp
                                    <span class="badge {{ $qrClass }}">{{ $qrLabel }}</span>
                                    @else
                                    <span style="color:var(--color-text-muted);">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- Kolom kanan: Leader & Pembayaran --}}
        <div class="flex flex-col gap-5">

            {{-- Info Leader --}}
            <div class="card">
                <h3 class="font-semibold text-sm mb-3" style="color:var(--color-text);">Leader / Pemesan</h3>
                @if($booking->leader)
                <div class="flex items-center gap-3 mb-3">
                    <div class="avatar" style="width:38px;height:38px;font-size:0.85rem;">
                        {{ strtoupper(substr($booking->leader->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="font-semibold text-sm">{{ $booking->leader->name }}</div>
                        <div class="text-xs" style="color:var(--color-text-muted);">{{ $booking->leader->email }}</div>
                    </div>
                </div>
                @if($booking->leader->phone)
                <div class="text-sm flex justify-between">
                    <span style="color:var(--color-text-muted);">Telepon</span>
                    <span>{{ $booking->leader->phone }}</span>
                </div>
                @endif
                <div class="text-sm flex justify-between mt-1">
                    <span style="color:var(--color-text-muted);">Role Akun</span>
                    <span class="badge badge-green" style="font-size:0.65rem;">{{ $booking->leader->role }}</span>
                </div>
                @endif
            </div>

            {{-- Pembayaran --}}
            <div class="card">
                <h3 class="font-semibold text-sm mb-3" style="color:var(--color-text);">Pembayaran</h3>
                <div class="flex flex-col gap-2">
                    <div class="flex justify-between text-sm">
                        <span style="color:var(--color-text-muted);">Total Tagihan</span>
                        <span class="font-bold text-base">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </div>
                    @if($booking->payment)
                    <div class="flex justify-between text-sm">
                        <span style="color:var(--color-text-muted);">Status</span>
                        <span class="badge {{ $booking->payment->status === 'paid' ? 'badge-green' : 'badge-yellow' }}">
                            {{ $booking->payment->status === 'paid' ? 'Lunas' : 'Pending' }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span style="color:var(--color-text-muted);">Gateway</span>
                        <span class="font-mono text-xs">{{ strtoupper($booking->payment->gateway) }}</span>
                    </div>
                    @if($booking->payment->paid_at)
                    <div class="flex justify-between text-sm">
                        <span style="color:var(--color-text-muted);">Dibayar pada</span>
                        <span>{{ $booking->payment->paid_at->format('d M Y, H:i') }}</span>
                    </div>
                    @endif
                    @else
                    <div class="text-sm" style="color:var(--color-text-muted);">Belum ada record pembayaran.</div>
                    @endif
                </div>

                {{-- Regulasi ringkas --}}
                @if($booking->mountain?->regulation)
                @php $reg = $booking->mountain->regulation; @endphp
                <div class="mt-3 pt-3" style="border-top:1px solid var(--color-border);">
                    <div class="text-xs font-semibold mb-2" style="color:var(--color-text-muted);">Rincian Harga</div>
                    <div class="flex justify-between text-xs">
                        <span style="color:var(--color-text-muted);">Harga dasar/orang</span>
                        <span>Rp {{ number_format($reg->base_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs mt-1">
                        <span style="color:var(--color-text-muted);">× {{ $booking->participants->count() }} peserta</span>
                        <span class="font-semibold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endif
            </div>

            {{-- TOS --}}
            <div class="card">
                <h3 class="font-semibold text-sm mb-2" style="color:var(--color-text);">Persetujuan</h3>
                <div class="text-sm flex justify-between">
                    <span style="color:var(--color-text-muted);">TOS Disetujui</span>
                    @if($booking->tos_accepted_at)
                    <span class="badge badge-green">{{ $booking->tos_accepted_at->format('d M Y') }}</span>
                    @else
                    <span class="badge badge-red">Belum</span>
                    @endif
                </div>
            </div>

        </div>
    </div>

</x-layouts.web>
