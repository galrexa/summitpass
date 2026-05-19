# SummitPass — Prompt Update Prototype
**Target: MVP 1 | Mode Prototipe**
**Stack: Laravel 11 + Blade + Alpine.js + Tailwind CSS**

---

## KONTEKS PROYEK

Kamu melanjutkan prototype interaktif **SummitPass** — platform keselamatan pendakian digital nasional berbasis IoT. Proposal telah diperbarui secara signifikan. Tugasmu adalah mensinkronkan prototype dengan proposal terbaru.

**Filosofi yang TIDAK BOLEH berubah:**
> Digitalisasi SIMAKSI berhenti di gerbang masuk. SummitPass mencatat pergerakan setiap orang yang masuk kawasan (pendaki, guide, porter) di SETIAP POS via gelang RFID+GPS — tanpa tindakan apapun dari pemakainya.

---

## DESIGN SYSTEM (Wajib Konsisten)

```css
--color-forest-700: #15803d;
--color-forest-600: #16a34a;   /* Safety Green — CTA utama */
--color-lake-700:   #0369a1;
--color-text:       #111827;
--color-text-muted: #6b7280;
--color-border:     #e5e7eb;
--color-bg:         #f9fafb;
--amber:            #f59e0b;   /* Warning — anomali */
--red-alert:        #ef4444;   /* Kritis — SAR escalation */

font-family: 'Inter', sans-serif;
```

**Prinsip:** Mobile-first, clean, "earthy". Tidak ada animasi berlebihan. Badge dan chip untuk status. Tabel dan card untuk data kritis.

---

## ARSITEKTUR YANG SUDAH ADA

```
resources/views/
├── welcome.blade.php
├── auth/login.blade.php
├── auth/register.blade.php
├── components/layouts/web.blade.php
├── dashboard/index.blade.php
├── admin/
│   ├── dashboard-pengelola.blade.php
│   ├── monitoring/index.blade.php
│   └── settings/index.blade.php
├── pendaki/
│   ├── jejak-summit.blade.php
│   ├── my-pass.blade.php
│   └── bookings/create.blade.php
└── profile/setup.blade.php
```

**Model yang tersedia:** User, Mountain, Trail, TrailCheckpoint, Booking, BookingParticipant, QrPass, TrekkingLog, SystemSetting

**Role:** `admin`, `pengelola_tn`, `pendaki`

---

## SCOPE MVP 1 — YANG HARUS DIUPDATE

MVP 1 mencakup satu alur lurus:

```
SIMAKSI online
      ↓
Gelang RFID+GPS di-assign di gerbang
      ↓
Check-in (scan gelang otomatis di gerbang)
      ↓
GPS tracking real-time + Monitoring RFID per pos
      ↓
Alert anomali otomatis ke TN & SAR
      ↓
Checkout di gerbang
```

**Perbedaan utama dari prototype lama:**
1. Sebelumnya hanya ada pendaki — sekarang ada tiga peran: HIKER, GUIDE, PORTER
2. Sebelumnya monitoring hanya RFID per pos — sekarang + GPS tracking real-time
3. UMKM (guide, porter, sewa alat) masuk sebagai fitur, tapi di prototype cukup tampilkan UI dengan data dummy dan label "Segera Hadir" untuk fitur yang belum MVP

---

## UPDATE 1 — BookingParticipant: Tambah Field Role

**File target:** `database/migrations` + `app/Models/BookingParticipant.php`

**Konteks:** Model `BookingParticipant` saat ini sudah punya field `role` tapi belum mencakup nilai `guide` dan `porter`. Update enum dan UI-nya.

**Yang harus diupdate:**

**Migration baru:**
```php
// Tambahkan migration untuk update enum role di booking_participants
// Nilai baru: 'hiker' (default), 'guide', 'porter'
// Tambah field: certification_number (nullable, untuk nomor sertifikat APGI guide)
//               affiliation (nullable, nama komunitas/operator)

Schema::table('booking_participants', function (Blueprint $table) {
    $table->string('role')->default('hiker')->change();
    // role: hiker | guide | porter
    $table->string('certification_number')->nullable()->after('role')
          ->comment('Nomor sertifikat APGI untuk guide');
    $table->string('affiliation')->nullable()->after('certification_number')
          ->comment('Nama operator/komunitas');
});
```

**Model update:**
```php
// Di BookingParticipant.php
const ROLES = ['hiker', 'guide', 'porter'];

public function getRoleLabelAttribute(): string
{
    return match($this->role) {
        'guide'  => 'Guide',
        'porter' => 'Porter',
        default  => 'Pendaki',
    };
}

public function getRoleBadgeColorAttribute(): string
{
    return match($this->role) {
        'guide'  => 'badge-blue',
        'porter' => 'badge-amber',
        default  => 'badge-green',
    };
}
```

---

## UPDATE 2 — TrekkingLog: Tambah Support GPS Koordinat

**Konteks:** Field `latitude` dan `longitude` sudah ada di `trekking_logs` tapi belum diekspos di dashboard monitoring. GPS tracking adalah lapisan kedua monitoring selain RFID.

**Yang harus diupdate di `resources/views/admin/monitoring/index.blade.php`:**

Pada setiap baris pendaki aktif, tampilkan koordinat GPS terakhir jika tersedia:

```blade
{{-- Di dalam card pendaki aktif, setelah info pos terakhir --}}
@if($lastLog?->latitude && $lastLog?->longitude)
<div class="flex items-center gap-1 mt-1">
    <svg width="10" height="10" ...><!-- pin icon --></svg>
    <span class="text-xs" style="color:var(--color-text-muted);">
        GPS: {{ number_format($lastLog->latitude, 5) }}, {{ number_format($lastLog->longitude, 5) }}
    </span>
    <a href="https://maps.google.com/?q={{ $lastLog->latitude }},{{ $lastLog->longitude }}"
       target="_blank"
       class="text-xs"
       style="color:var(--color-forest-600);text-decoration:none;">
        Buka Maps →
    </a>
</div>
@else
<div class="text-xs mt-1" style="color:var(--color-text-muted);">GPS: Menunggu data...</div>
@endif
```

**Untuk SimulateScanController:** Saat record scan, generate koordinat GPS simulasi otomatis jika tidak ada input koordinat dari request — gunakan koordinat random di sekitar koordinat gunung sebagai simulasi:

```php
// Di SimulateScanController@record
// Jika latitude/longitude tidak diinput, generate simulasi
$latitude  = $request->latitude  ?? ($booking->mountain->latitude  + (rand(-100, 100) / 10000));
$longitude = $request->longitude ?? ($booking->mountain->longitude + (rand(-100, 100) / 10000));
```

**Tambah field koordinat di Mountain jika belum ada:**
```php
// Migration: tambah latitude, longitude ke mountains
$table->decimal('latitude', 10, 7)->nullable()->comment('Koordinat pusat gunung untuk simulasi GPS');
$table->decimal('longitude', 10, 7)->nullable();
```

**Seed koordinat gunung di DatabaseSeeder/MountainSeeder:**
```php
// Contoh data dummy koordinat:
// Gunung Rinjani:   -8.4119,  116.4648
// Gunung Semeru:    -8.1083,  112.9225
// Gunung Merbabu:   -7.4554,  110.4358
// Gunung Gede:      -6.7814,  106.9834
// Gunung Kerinci:   -1.6970,  101.2636
```

---

## UPDATE 3 — Dashboard Monitoring: Tag Peran + GPS

**File target:** `resources/views/admin/monitoring/index.blade.php`

**Yang harus diperbarui:**

**A. Stat cards — tambah breakdown per peran:**

```blade
{{-- Ganti stat card "Pendaki Aktif" menjadi breakdown 3 peran --}}
<div class="stat-card">
    <div class="stat-card-icon" style="background:var(--color-forest-100);">
        <!-- users icon -->
    </div>
    <div>
        <div class="stat-card-value">{{ $stats['active_now'] }}</div>
        <div class="stat-card-label">Total di Jalur</div>
        <div class="flex gap-2 mt-1">
            <span class="badge badge-green text-xs">{{ $stats['active_hikers'] ?? 0 }} Pendaki</span>
            <span class="badge badge-blue text-xs">{{ $stats['active_guides'] ?? 0 }} Guide</span>
            <span class="badge badge-amber text-xs">{{ $stats['active_porters'] ?? 0 }} Porter</span>
        </div>
    </div>
</div>
```

**B. List pendaki aktif — tampilkan tag peran:**

```blade
{{-- Di setiap row pendaki aktif, tambahkan badge role --}}
@php $role = $pass->participant->role ?? 'hiker'; @endphp
<span class="badge {{ $pass->participant->role_badge_color ?? 'badge-green' }}">
    {{ $pass->participant->role_label ?? 'Pendaki' }}
</span>
```

**C. Update MonitoringWebController untuk hitung per peran:**

```php
// Di MonitoringWebController@index, tambahkan:
$stats['active_hikers']  = $activePasses->filter(fn($p) => ($p->participant->role ?? 'hiker') === 'hiker')->count();
$stats['active_guides']  = $activePasses->filter(fn($p) => $p->participant->role === 'guide')->count();
$stats['active_porters'] = $activePasses->filter(fn($p) => $p->participant->role === 'porter')->count();
```

**D. Panel "Status SummitPost Unit" (SIMULASI) — tambahkan section baru:**

Tambahkan section ini setelah stat cards, sebelum list pendaki aktif:

```blade
{{-- STATUS SUMMITPOST UNIT — Simulasi Hardware IoT --}}
<div class="card" style="padding:0;overflow:hidden;margin-bottom:1.5rem;">
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid var(--color-border);">
        <div class="flex items-center gap-2">
            <div style="width:8px;height:8px;border-radius:50%;background:#22c55e;box-shadow:0 0 0 3px rgba(34,197,94,0.2);"></div>
            <h3 class="font-semibold text-sm">Status SummitPost Unit</h3>
        </div>
        <span class="badge badge-green">{{ $checkpoints->count() }} Unit Online</span>
    </div>

    <div class="divide-y" style="border-color:var(--color-border);">
        @foreach($checkpoints as $cp)
        @php
            // Data simulasi per pos
            $battery   = rand(60, 98);
            $signalBar = rand(2, 3); // 1-3
            $lastScan  = $cp->trekkingLogs->last();
            $isOnline  = true; // simulasi semua online
        @endphp
        <div class="flex items-center gap-3 px-5 py-3">
            {{-- Status indicator --}}
            <div style="width:8px;height:8px;border-radius:50%;flex-shrink:0;background:{{ $isOnline ? '#22c55e' : '#ef4444' }};"></div>

            {{-- Info pos --}}
            <div class="flex-1 min-w-0">
                <div class="text-sm font-semibold">{{ $cp->name }}</div>
                <div class="text-xs" style="color:var(--color-text-muted);">
                    Scan terakhir:
                    @if($lastScan)
                        {{ $lastScan->participant->name ?? '—' }} &middot; {{ $lastScan->scanned_at?->diffForHumans() }}
                    @else
                        Belum ada scan hari ini
                    @endif
                </div>
            </div>

            {{-- Battery --}}
            <div class="text-right flex-shrink-0">
                <div class="text-xs font-semibold" style="color:{{ $battery < 20 ? '#ef4444' : 'var(--color-text)' }};">
                    🔋 {{ $battery }}%
                </div>
                <div class="text-xs" style="color:var(--color-text-muted);">
                    LoRa: {{ str_repeat('▮', $signalBar) }}{{ str_repeat('▯', 3 - $signalBar) }}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="px-5 py-3" style="background:var(--color-bg);border-top:1px solid var(--color-border);">
        <p class="text-xs" style="color:var(--color-text-muted);">
            ⚡ Data simulasi prototipe — infrastruktur IoT fisik dalam tahap spesifikasi teknis menuju pilot.
            <span style="color:var(--color-forest-600);font-weight:600;">Mode: HF RFID + GPS + LoRa Mesh</span>
        </p>
    </div>
</div>
```

**Update MonitoringWebController — tambah data checkpoints:**
```php
// Ambil checkpoints dari gunung yang dikelola
$checkpoints = TrailCheckpoint::with(['trekkingLogs' => fn($q) => $q->latest('scanned_at')->limit(1)->with('qrPass.participant')])
    ->whereHas('trail.mountain', fn($q) => $q->where('managed_by_user_id', auth()->id()))
    ->orderBy('order_seq')
    ->get();

return view('admin.monitoring.index', compact('activePasses', 'anomalyLogs', 'expiredPasses', 'stats', 'checkpoints'));
```

---

## UPDATE 4 — Booking Form: Tambah Pilihan Peran Peserta + UMKM Placeholder

**File target:** `resources/views/pendaki/bookings/create.blade.php`

**A. Tambah field role per peserta:**

Saat pendaki menambahkan anggota rombongan, tambahkan dropdown role:

```blade
{{-- Di dalam loop peserta (Alpine.js) --}}
<template x-for="(peserta, index) in pesertaList" :key="index">
    <div class="card mb-3">
        <!-- field nama, NIK existing... -->

        {{-- TAMBAHAN: Role peserta --}}
        <div class="form-group mt-3">
            <label class="form-label">Peran dalam Pendakian</label>
            <select x-model="peserta.role" class="form-select">
                <option value="hiker">🟢 Pendaki</option>
                <option value="guide">🔵 Guide (Pemandu)</option>
                <option value="porter">🟠 Porter</option>
            </select>
        </div>

        {{-- Jika guide: tampilkan field nomor sertifikat APGI --}}
        <div class="form-group mt-2" x-show="peserta.role === 'guide'" x-transition>
            <label class="form-label">Nomor Sertifikat APGI <span style="color:#ef4444;">*</span></label>
            <input type="text" x-model="peserta.certification_number"
                   class="form-input"
                   placeholder="Contoh: APGI-2024-12345">
            <p class="text-xs mt-1" style="color:var(--color-text-muted);">
                Guide wajib memiliki sertifikasi APGI + BNSP yang masih berlaku.
            </p>
        </div>
    </div>
</template>
```

**B. Validasi rasio guide:pendaki (Alpine.js client-side):**

```blade
{{-- Tambahkan computed property di x-data --}}
<div x-data="{
    pesertaList: [{ name: '', nik: '', role: 'hiker', certification_number: '' }],

    get hikerCount() { return this.pesertaList.filter(p => p.role === 'hiker').length },
    get guideCount() { return this.pesertaList.filter(p => p.role === 'guide').length },
    get porterCount() { return this.pesertaList.filter(p => p.role === 'porter').length },

    get ratioWarning() {
        if (this.guideCount === 0) return null;
        if (this.hikerCount / this.guideCount > 5)
            return `⚠️ Rasio guide:pendaki melebihi batas (1:5). Tambah ${Math.ceil(this.hikerCount/5) - this.guideCount} guide lagi.`;
        return null;
    },

    get porterRatioWarning() {
        if (this.porterCount === 0) return null;
        if (this.hikerCount / this.porterCount > 3)
            return `⚠️ Rasio porter:pendaki melebihi batas (1:3). Tambah ${Math.ceil(this.hikerCount/3) - this.porterCount} porter lagi.`;
        return null;
    }
}">

    {{-- Tampilkan warning di atas tombol submit --}}
    <div x-show="ratioWarning" x-transition
         class="rounded-lg px-4 py-3 mb-4"
         style="background:#fef9c3;border:1px solid #fde047;">
        <p class="text-sm font-medium" style="color:#92400e;" x-text="ratioWarning"></p>
        <p class="text-xs mt-1" style="color:#a16207;">Berdasarkan SOP Pendakian TNGR 2025</p>
    </div>

    <div x-show="porterRatioWarning" x-transition
         class="rounded-lg px-4 py-3 mb-4"
         style="background:#fef9c3;border:1px solid #fde047;">
        <p class="text-sm font-medium" style="color:#92400e;" x-text="porterRatioWarning"></p>
    </div>
```

**C. Section UMKM — placeholder "Segera Hadir":**

Tambahkan section ini setelah form peserta, sebelum tombol submit:

```blade
{{-- UMKM MARKETPLACE — Coming Soon --}}
<div class="card mt-4" style="border:1px dashed var(--color-border);background:var(--color-bg);">
    <div class="flex items-center gap-2 mb-3">
        <h3 class="font-semibold text-sm">Tambah Layanan (Opsional)</h3>
        <span class="badge" style="background:#f3f4f6;color:var(--color-text-muted);font-size:0.65rem;">Segera Hadir</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        {{-- Guide APGI --}}
        <div class="rounded-lg p-3 text-center" style="background:white;border:1px solid var(--color-border);opacity:0.6;">
            <div class="text-2xl mb-1">🔵</div>
            <div class="text-xs font-semibold mb-1">Guide Bersertifikat</div>
            <div class="text-xs" style="color:var(--color-text-muted);">Pemandu APGI terverifikasi per kawasan</div>
            <div class="mt-2 text-xs" style="color:var(--color-forest-600);font-weight:600;">Segera Hadir</div>
        </div>

        {{-- Porter --}}
        <div class="rounded-lg p-3 text-center" style="background:white;border:1px solid var(--color-border);opacity:0.6;">
            <div class="text-2xl mb-1">🟠</div>
            <div class="text-xs font-semibold mb-1">Porter Lokal</div>
            <div class="text-xs" style="color:var(--color-text-muted);">Porter terdata dari masyarakat sekitar kawasan</div>
            <div class="mt-2 text-xs" style="color:var(--color-forest-600);font-weight:600;">Segera Hadir</div>
        </div>

        {{-- Sewa Alat --}}
        <div class="rounded-lg p-3 text-center" style="background:white;border:1px solid var(--color-border);opacity:0.6;">
            <div class="text-2xl mb-1">🎒</div>
            <div class="text-xs font-semibold mb-1">Sewa Alat</div>
            <div class="text-xs" style="color:var(--color-text-muted);">Direktori penyewa alat lokal per kawasan</div>
            <div class="mt-2 text-xs" style="color:var(--color-forest-600);font-weight:600;">Segera Hadir</div>
        </div>
    </div>

    <p class="text-xs mt-3" style="color:var(--color-text-muted);">
        Di fase berikutnya, kamu bisa memilih guide, porter, dan menyewa alat langsung dari sini — semua terverifikasi, semua tercatat dalam sistem monitoring keselamatan.
    </p>
</div>
```

---

## UPDATE 5 — Halaman Publik: Family Tracking

**File target:** Buat `resources/views/public/family-tracking.blade.php`
**Route:** `GET /track/{token}` — tanpa auth

**Deskripsi:** Halaman ini diakses keluarga pendaki via link unik tanpa login. Tampilkan timeline perjalanan dan status terkini.

**Tambah route di `routes/web.php`:**
```php
Route::get('/track/{token}', [FamilyTrackingController::class, 'show'])
     ->name('public.family-tracking');
```

**Buat controller `app/Http/Controllers/FamilyTrackingController.php`:**
```php
public function show($token)
{
    $qrPass = QrPass::with([
        'participant.booking.mountain',
        'participant.booking.trail',
        'trekkingLogs.checkpoint',
    ])->where('family_token', $token)->first();

    // Jika token tidak ditemukan, tampilkan 404 yang ramah
    if (!$qrPass) {
        return view('public.family-tracking-notfound');
    }

    return view('public.family-tracking', compact('qrPass'));
}
```

**Tambah field `family_token` ke `qr_passes`:**
```php
// Migration baru
$table->string('family_token', 64)->nullable()->unique()
      ->comment('Token unik untuk akses publik keluarga');
```

**Isi `family-tracking.blade.php`:**

Layout tanpa sidebar, tanpa auth. Komponen yang harus ada:

1. **Header minimal:**
   - Logo SummitPass kecil
   - Badge status pendakian: `AKTIF` (hijau) / `SELESAI` (abu) / `ANOMALI` (merah berkedip)
   - Nama gunung + tanggal

2. **Info pendaki (privacy-safe):**
   - Nama: tampilkan hanya nama depan + inisial belakang (contoh: `Galih R.`)
   - Jalur pendakian
   - Tanggal izin berlaku

3. **Timeline pos (komponen utama):**
```blade
@php
    $checkpoints = $qrPass->participant->booking->trail->checkpoints->sortBy('order_seq');
    $scannedLogs = $qrPass->trekkingLogs->keyBy('trail_checkpoint_id');
@endphp

<div class="timeline">
    @foreach($checkpoints as $cp)
    @php
        $log = $scannedLogs[$cp->id] ?? null;
        $status = $log ? 'passed' : 'pending';
        $isLast = $checkpoints->last()->id === $cp->id;
    @endphp
    <div class="timeline-item {{ $status }}">
        <div class="timeline-dot {{ $status === 'passed' ? 'dot-green' : 'dot-gray' }}"></div>
        <div class="timeline-content">
            <div class="font-semibold text-sm">{{ $cp->name }}</div>
            @if($log)
            <div class="text-xs" style="color:var(--color-text-muted);">
                ✓ {{ $log->scanned_at?->format('H:i') }} WIB &middot; {{ $log->scanned_at?->diffForHumans() }}
            </div>
            @else
            <div class="text-xs" style="color:var(--color-text-muted);">Belum dilewati</div>
            @endif
        </div>
    </div>
    @endforeach
</div>
```

4. **Status card kontekstual:**
```blade
@if($qrPass->status === 'used')
<div class="status-card success">
    ✅ <strong>Pendaki telah kembali dengan selamat.</strong><br>
    <small>Checkout: {{ $qrPass->trekkingLogs->last()?->scanned_at?->format('d M Y, H:i') }} WIB</small>
</div>

@elseif($qrPass->participant->booking->status === 'active')
<div class="status-card active">
    🟢 <strong>Pendakian sedang berlangsung.</strong><br>
    <small>Posisi terakhir diketahui: {{ $qrPass->trekkingLogs->last()?->checkpoint?->name ?? 'Gerbang Masuk' }}
    &middot; {{ $qrPass->trekkingLogs->last()?->scanned_at?->diffForHumans() ?? '' }}</small>
</div>

@elseif($qrPass->trekkingLogs->where('anomaly_flag', true)->count() > 0)
<div class="status-card warning">
    ⚠️ <strong>Tim pengelola telah diberitahu.</strong><br>
    <small>Tidak perlu panik — pantau terus halaman ini. Petugas sedang menangani.</small>
</div>
@endif
```

5. **Footer keluarga:**
```blade
<footer class="family-footer">
    <div class="text-xs text-center" style="color:var(--color-text-muted);">
        Halaman ini diperbarui otomatis setiap 60 detik.
        Butuh bantuan darurat? Hubungi <strong>Basarnas: 115</strong>
    </div>
    <div class="text-xs text-center mt-2" style="color:var(--color-text-muted);">
        Powered by SummitPass &middot; Sistem Keselamatan Pendakian Nasional
    </div>
</footer>

{{-- Auto refresh setiap 60 detik --}}
<script>
    setTimeout(() => location.reload(), 60000);
</script>
```

**Data dummy untuk demo:**
Jika `family_token` belum di-seed, gunakan token hardcode `demo-keluarga-001` yang terhubung ke booking aktif di DatabaseSeeder.

---

## UPDATE 6 — Sidebar: Tambah Menu Family Link & Coming Soon Items

**File target:** `resources/views/components/layouts/web.blade.php`

**A. Untuk role `pendaki` — tambah menu "Family Link":**

```blade
{{-- Setelah menu My Pass --}}
<a href="{{ route('pendaki.family-link') }}"
   class="sidebar-link {{ request()->routeIs('pendaki.family-link') ? 'active' : '' }}"
   data-tooltip="Family Link">
    <svg class="sidebar-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
    </svg>
    <span class="sidebar-link-label">Family Link</span>
</a>
```

**B. Untuk role `admin`/`pengelola_tn` — tambah badge anomali di sidebar monitoring:**

```blade
{{-- Di link monitoring, tambahkan badge count --}}
<a href="{{ route('admin.monitoring.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.monitoring.*') ? 'active' : '' }}"
   data-tooltip="Monitoring">
    <!-- icon existing -->
    <span class="sidebar-link-label">Monitoring Real-time</span>
    @if(($activeAnomalies ?? 0) > 0)
    <span class="sidebar-badge" style="background:#ef4444;">{{ $activeAnomalies }}</span>
    @endif
</a>
```

**Tambah `$activeAnomalies` ke web.blade.php via View Composer:**
```php
// Di AppServiceProvider@boot
View::composer('components.layouts.web', function ($view) {
    if (auth()->check() && in_array(auth()->user()->role, ['admin', 'pengelola_tn'])) {
        $view->with('activeAnomalies', \App\Models\TrekkingLog::where('anomaly_flag', true)->count());
    }
});
```

**C. Section "Pengembangan Lanjutan" di sidebar — untuk semua role:**

```blade
{{-- Di bagian bawah sidebar, sebelum logout --}}
<div class="sidebar-section-label mt-4">Segera Hadir</div>

<div class="sidebar-link disabled" style="opacity:0.5;cursor:not-allowed;" data-tooltip="Operator & Komunitas">
    <svg class="sidebar-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
    </svg>
    <span class="sidebar-link-label">Operator / Komunitas</span>
    <span class="sidebar-badge" style="background:var(--color-text-muted);font-size:0.6rem;">Fase 2</span>
</div>

<div class="sidebar-link disabled" style="opacity:0.5;cursor:not-allowed;" data-tooltip="UMKM Lokal">
    <svg class="sidebar-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
        <polyline points="9,22 9,12 15,12 15,22"/>
    </svg>
    <span class="sidebar-link-label">UMKM Lokal</span>
    <span class="sidebar-badge" style="background:var(--color-text-muted);font-size:0.6rem;">Fase 3</span>
</div>
```

---

## UPDATE 7 — Landing Page: Narasi IoT + Ekosistem Tiga Peran

**File target:** `resources/views/welcome.blade.php`

**A. Update comparison table — tambah 3 baris baru:**

```blade
{{-- Tambahkan di tabel perbandingan setelah baris existing --}}
<div class="comp-row">
    <div class="comp-feat">GPS tracking real-time di seluruh jalur</div>
    <div style="text-align:center"><span class="chip-no">✗</span></div>
    <div style="text-align:center"><span class="chip-yes">✓</span></div>
</div>
<div class="comp-row">
    <div class="comp-feat">Guide & porter masuk sistem monitoring</div>
    <div style="text-align:center"><span class="chip-no">✗</span></div>
    <div style="text-align:center"><span class="chip-yes">✓</span></div>
</div>
<div class="comp-row">
    <div class="comp-feat">Infrastruktur IoT mandiri (tanpa sinyal seluler)</div>
    <div style="text-align:center"><span class="chip-no">✗</span></div>
    <div style="text-align:center"><span class="chip-yes">✓</span></div>
</div>
```

**B. Tambah section "Siapa yang Dilindungi" setelah section hero:**

```blade
{{-- Section: Tiga Peran, Satu Sistem --}}
<section id="ekosistem" style="padding:4rem 0;background:white;">
    <div style="max-width:1100px;margin:0 auto;padding:0 1.5rem;">
        <div class="text-center mb-10">
            <div class="feat-tag">Ekosistem monitoring inklusif</div>
            <h2 class="feat-title">Semua yang masuk kawasan, terlindungi.</h2>
            <p class="feat-sub">Bukan hanya pendaki. Setiap orang yang masuk kawasan tercatat identitas dan posisi GPS-nya.</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.5rem;">
            {{-- Pendaki --}}
            <div class="feat-card" style="border-left:4px solid #16a34a;">
                <div style="font-size:2rem;margin-bottom:0.75rem;">🟢</div>
                <div class="feat-card-title">Pendaki (HIKER)</div>
                <div class="feat-card-desc">
                    Identitas terverifikasi via NIK. Gelang RFID+GPS aktif sepanjang pendakian.
                    Keluarga pantau posisi real-time tanpa install app.
                </div>
            </div>

            {{-- Guide --}}
            <div class="feat-card" style="border-left:4px solid #0369a1;">
                <div style="font-size:2rem;margin-bottom:0.75rem;">🔵</div>
                <div class="feat-card-title">Guide (GUIDE)</div>
                <div class="feat-card-desc">
                    Wajib bersertifikat APGI + BNSP. Terima notifikasi anomali pertama sebagai PIC lapangan.
                    Rasio 1 guide : maks 5 pendaki divalidasi otomatis saat booking.
                </div>
            </div>

            {{-- Porter --}}
            <div class="feat-card" style="border-left:4px solid #f59e0b;">
                <div style="font-size:2rem;margin-bottom:0.75rem;">🟠</div>
                <div class="feat-card-title">Porter (PORTER)</div>
                <div class="feat-card-desc">
                    Masyarakat lokal terdata dengan verifikasi KTP domisili.
                    Mendapat gelang RFID+GPS dan masuk radar monitoring — naik kelas dari informal ke mitra resmi.
                </div>
            </div>
        </div>
    </div>
</section>
```

**C. Tambah step "SummitPost Unit" di section cara kerja:**

Di antara step "Terima gelang RFID+GPS" dan "Scan di setiap pos", tambahkan step baru:

```blade
<div class="step-card" style="border:1px dashed var(--color-forest-300);background:var(--color-forest-50);">
    <div class="step-number" style="background:var(--color-forest-700);">IoT</div>
    <div>
        <div class="step-title">SummitPost Unit bekerja diam-diam</div>
        <div class="step-desc">
            Di setiap pos, SummitPost Unit — perangkat mandiri bertenaga surya dengan RFID reader
            dan modul LoRa — membaca gelang pendaki secara otomatis. Tanpa sinyal seluler.
            Tanpa tindakan dari pendaki. Data dikirim via jaringan LoRa mesh ke dashboard pengelola dan SAR.
        </div>
        <div class="mt-2">
            <span class="badge" style="background:var(--color-forest-100);color:var(--color-forest-700);">HF RFID — MVP</span>
            <span class="badge ml-1" style="background:#f3f4f6;color:var(--color-text-muted);">UHF Otomatis — Roadmap</span>
        </div>
    </div>
</div>
```

---

## UPDATE 8 — DatabaseSeeder: Data Dummy Lengkap

**File target:** `database/seeders/DatabaseSeeder.php` dan seeder terkait

**Yang harus ditambahkan/diupdate di seed data:**

**A. Koordinat gunung:**
```php
// Update gunung-gunung yang sudah ada dengan koordinat
Mountain::where('name', 'like', '%Rinjani%')->update(['latitude' => -8.4119, 'longitude' => 116.4648]);
Mountain::where('name', 'like', '%Semeru%')->update(['latitude' => -8.1083, 'longitude' => 112.9225]);
Mountain::where('name', 'like', '%Merbabu%')->update(['latitude' => -7.4554, 'longitude' => 110.4358]);
Mountain::where('name', 'like', '%Gede%')->update(['latitude' => -6.7814, 'longitude' => 106.9834]);
```

**B. Peserta rombongan dengan peran guide dan porter:**
```php
// Update seeder BookingParticipant untuk menyertakan role guide & porter
// Contoh satu booking lengkap:
$booking = Booking::where('booking_code', 'SP-2026-001')->first();
if ($booking) {
    // Peserta 1: Pendaki (leader)
    BookingParticipant::create([
        'booking_id' => $booking->id,
        'name' => 'Galih Reksa Lingga',
        'nik' => '3201234567890001',
        'role' => 'hiker',
    ]);
    // Peserta 2: Guide
    BookingParticipant::create([
        'booking_id' => $booking->id,
        'name' => 'Pak Rudi Santoso',
        'nik' => '3201234567890002',
        'role' => 'guide',
        'certification_number' => 'APGI-2024-00456',
    ]);
    // Peserta 3: Porter
    BookingParticipant::create([
        'booking_id' => $booking->id,
        'name' => 'Asep Supriatna',
        'nik' => '3201234567890003',
        'role' => 'porter',
    ]);
}
```

**C. Family token untuk QrPass demo:**
```php
// Tambah family_token ke setiap QrPass saat create
QrPass::all()->each(function ($pass) {
    if (!$pass->family_token) {
        $pass->update(['family_token' => \Illuminate\Support\Str::random(32)]);
    }
});

// Satu token hardcode untuk demo:
QrPass::first()?->update(['family_token' => 'demo-keluarga-001']);
```

**D. TrekkingLog dengan koordinat GPS:**
```php
// Update trekking log yang ada dengan koordinat GPS simulasi
TrekkingLog::all()->each(function ($log) {
    $checkpoint = $log->checkpoint;
    $mountain   = $checkpoint?->trail?->mountain;
    if ($mountain?->latitude && !$log->latitude) {
        $log->update([
            'latitude'  => $mountain->latitude  + (rand(-200, 200) / 10000),
            'longitude' => $mountain->longitude + (rand(-200, 200) / 10000),
        ]);
    }
});
```

---

## CHECKLIST OUTPUT PER UPDATE

Untuk setiap update di atas, output yang diharapkan:

- [ ] **Migration** jika ada perubahan schema — nama file: `YYYY_MM_DD_HHMMSS_[deskripsi].php`
- [ ] **Model** yang diupdate — tambahkan `$fillable`, cast, dan helper method yang diperlukan
- [ ] **Controller** yang diupdate atau dibuat baru
- [ ] **Blade view** lengkap — siap paste ke project
- [ ] **Alpine.js** untuk interaktivitas — tidak pakai jQuery
- [ ] **Tailwind classes** konsisten dengan design system yang ada
- [ ] **Data simulasi** hardcode di blade atau via seeder — tidak perlu API eksternal
- [ ] **Responsif mobile** — semua layout works di layar 375px
- [ ] **Badge "Mode Prototipe"** di halaman baru yang bukan bagian MVP 1

---

## CONSTRAINT & LARANGAN

1. **Jangan ubah** auth flow, login/register, atau middleware yang ada
2. **Jangan ubah** struktur layout sidebar kecuali menambahkan item menu
3. **Jangan gunakan** JavaScript framework selain Alpine.js
4. **Jangan hapus** fitur yang sudah berjalan — hanya tambah atau perbarui
5. **Tetap gunakan** class naming yang ada: `.stat-card`, `.card`, `.btn`, `.btn-primary`, `.badge`, dll.
6. **Data NIK** selalu masked di view publik dan keluarga: tampilkan `****` + 4 digit terakhir
7. **Fitur non-MVP** cukup tampilkan sebagai UI placeholder dengan label "Segera Hadir" atau "Fase 2/3"
8. **Koordinat GPS** di halaman publik keluarga — tidak perlu map interaktif, cukup teks "Pos terakhir: [nama pos]"
9. **SummitPost Unit status** adalah simulasi murni — tidak ada koneksi ke hardware nyata

---

## SKENARIO DEMO (Panduan Urutan Demonstrasi)

Prototype ini didemonstrasikan dalam 4 skenario berurutan:

**Skenario 1 — Pengelola TN (3 menit):**
Login pengelola → Dashboard monitoring → Lihat breakdown HIKER/GUIDE/PORTER → Lihat panel Status SummitPost Unit → Klik pendaki yang anomali → Lihat koordinat GPS terakhir + link Google Maps

**Skenario 2 — Pendaki (2 menit):**
Login pendaki → Buat booking baru → Tambah peserta dengan role guide + porter → Lihat warning rasio → Submit → Lihat My Pass → Salin Family Link

**Skenario 3 — Keluarga (1 menit):**
Buka `/track/demo-keluarga-001` tanpa login → Lihat timeline pos → Status card aktif → Footer dengan nomor darurat

**Skenario 4 — Fitur Roadmap (1 menit):**
Tunjukkan sidebar items "Segera Hadir" (Operator, UMKM) → Tunjukkan section UMKM di booking form → Jelaskan roadmap Fase 2 dan 3

---

*SummitPass — Dari Gerbang Hingga Puncak, Setiap Langkah Tercatat. Setiap Pencapaian Tersertifikasi.*
*Team Bravopala — P0823 — Digdaya × Hackathon 2026*
