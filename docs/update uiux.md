# SummitPass — UX Update Spec: Role Pendaki

> Resource prompt untuk implementasi perubahan UI/UX hasil analisis sesi desain.  
> Gunakan dokumen ini sebagai konteks saat meminta Claude mengerjakan task implementasi apapun yang berkaitan dengan role `pendaki`.

---

## 1. Konteks & Filosofi

SummitPass bukan sekadar platform SIMAKSI digital — ia adalah **companion keselamatan pendakian**. Digitalisasi saat ini (SiOra, SIAP GEPANG) berhenti di gerbang masuk; SummitPass mencatat pergerakan pendaki di **setiap pos** (naik & turun) untuk menyediakan data valid bagi tim SAR.

Implikasi ke UX: setiap titik kontak dengan pendaki harus memperkuat kepercayaan bahwa platform ini serius soal keselamatan — bukan hanya soal kemudahan booking.

---

## 2. Design System

```
Primary (Safety Green) : #16a34a  → hover #15803d
Alert (Anomali Amber)  : #f59e0b
Background             : #f9fafb  (Gray-50)
Forest palette         : --color-forest-{50..950}  (lihat resources/css/app.css)
Lake palette           : --color-lake-{50..950}
Stone palette          : --color-stone-{50..900}
```

**Style**: Mobile-first untuk pendaki, clean, earthy.  
**Stack**: Laravel Blade + Alpine.js + Tailwind CSS (via CDN di halaman standalone, via Vite di app utama).

---

## 3. Struktur Role & Layout

### 3.1 Dua layout yang ada

| File                                       | Digunakan oleh                         | Navigasi                               |
| ------------------------------------------ | -------------------------------------- | -------------------------------------- |
| `resources/views/layouts/web.blade.php`    | `admin`, `pengelola_tn`                | Sidebar kiri collapsible (dark forest) |
| `resources/views/layouts/mobile.blade.php` | `pendaki` ← **HARUS dipindah ke sini** | Top header + bottom tab bar            |

**Status saat ini**: Semua role masih di-route ke `web.blade.php`. Pendaki seharusnya menggunakan `mobile.blade.php`.

### 3.2 Bottom tab bar pendaki (5 tab)

```
Tab 1 — Jelajahi   → route: pendaki.explore (BARU)    icon: mountain
Tab 2 — Booking    → route: pendaki.bookings           icon: file-text
Tab 3 — QR Pass    → route: pendaki.my-pass            icon: qrcode
Tab 4 — Jejak      → route: pendaki.jejak-summit       icon: award
Tab 5 — Profil     → route: pendaki.profile            icon: user
```

### 3.3 Cara routing layout per role

Di `app/Http/Controllers` atau middleware, deteksi role dan redirect ke layout yang sesuai. Contoh paling sederhana via `AppServiceProvider` atau override di base controller pendaki:

```php
// Pendaki controller — semua view pendaki extend mobile.blade.php
// Admin/pengelola controller — semua view extend web.blade.php (tidak berubah)
```

---

## 4. Halaman Baru: Explore Gunung (`pendaki.explore`)

### 4.1 Tujuan halaman

Halaman utama post-login untuk pendaki. Menggantikan landing di dashboard sebagai **titik entry pemilihan gunung** sebelum masuk ke booking stepper.

### 4.2 Route yang perlu ditambahkan

```php
// routes/web.php — dalam group prefix('my')->name('pendaki.')
Route::get('/explore', [PendakiController::class, 'explore'])->name('explore');
Route::get('/api/mountains/{id}/detail', [PendakiController::class, 'mountainDetail'])->name('api.mountain.detail');
```

### 4.3 Controller method

```php
// app/Http/Controllers/Pendaki/PendakiController.php

public function explore()
{
    $mountains = Mountain::active()
        ->with(['regulation', 'trails' => fn($q) => $q->active()])
        ->orderBy('name')
        ->get();

    return view('pendaki.explore', compact('mountains'));
}

public function mountainDetail($id)
{
    $mountain = Mountain::with(['regulation', 'trails' => fn($q) => $q->active()])
        ->findOrFail($id);

    // Hitung slot tersisa minggu ini (contoh sederhana)
    $weekStart = now()->startOfWeek();
    $weekEnd   = now()->endOfWeek();
    $bookedThisWeek = Booking::where('mountain_id', $id)
        ->whereBetween('start_date', [$weekStart, $weekEnd])
        ->whereIn('status', ['paid', 'active'])
        ->sum('total_participants'); // atau count()

    $quotaPerDay = $mountain->regulation->quota_per_trail_per_day ?? 50;
    $slotRemaining = max(0, ($quotaPerDay * 7) - $bookedThisWeek);

    return response()->json([
        'mountain'       => $mountain,
        'slot_remaining' => $slotRemaining,
    ]);
}
```

### 4.4 Mountain Card — struktur UI

Setiap card menampilkan:

- **Hero thumbnail** — SVG mountain silhouette dengan gradient warna forest (placeholder sampai `image_url` tersedia)
- **Badge ketersediaan** — dihitung dari kuota vs booking minggu ini:
    - `> 50% slot sisa` → hijau "✓ Tersedia"
    - `10–50% slot sisa` → amber "⚠ Hampir penuh"
    - `< 10% slot sisa` → merah "✕ Terbatas"
- **Ketinggian** (mdpl) — overlay di hero
- **Nama gunung** + lokasi/provinsi
- **Quota bar** — progress bar tipis (3px) menunjukkan persentase slot terpakai
- **Badge grade & durasi maks**
- **Hint klik** — "Klik untuk detail lengkap →"

### 4.5 Filter strip

Horizontal scrollable pill buttons:

```
[Semua] [Pemula] [Menengah] [Lanjut] [Jawa] [Sumatera] [NTB] [Sulawesi]
```

Filter aktif menggunakan warna `forest-100` background + `forest-800` text + border `forest-300`.

---

## 5. Mountain Detail Drawer

### 5.1 Trigger

Klik di mana saja pada mountain card membuka drawer dari bawah layar (bottom sheet pattern).

### 5.2 Implementasi Alpine.js

```html
<!-- Di explore.blade.php, bungkus konten dengan Alpine -->
<div x-data="exploreApp()" ...>
    <!-- Mountain cards -->
    <div @click="openDrawer(mountain.id)">...</div>

    <!-- Drawer overlay -->
    <div
        x-show="drawerOpen"
        @click="closeDrawer()"
        class="fixed inset-0 bg-black/40 z-40"
        x-transition:enter="..."
        x-transition:leave="..."
    ></div>

    <!-- Drawer panel -->
    <div
        x-show="drawerOpen"
        class="fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl z-50 max-h-[90vh] overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
    >
        <!-- Drag handle -->
        <div class="w-9 h-1 bg-gray-300 rounded-full mx-auto mt-3 mb-0"></div>

        <!-- Konten drawer (diisi via fetch) -->
        <div x-html="drawerContent" class="p-4 pb-8"></div>
    </div>
</div>
```

### 5.3 Konten drawer — semua section wajib

```
┌─────────────────────────────────────────┐
│ [Drag handle]                           │
│                                         │
│ Nama Gunung                  [Grade]    │
│ Sub: TN / Provinsi                      │
│                                         │
│ ■ Status jalur: Buka/Tutup/Siaga        │  ← colored banner
│                                         │
│ [mdpl] [maks hari] [slot minggu ini] [min usia]  │  ← stat chips 4 kolom
│                                         │
│ Tentang gunung ini                      │
│ [Artikel singkat 3-4 kalimat, dari      │
│  field description di tabel mountains]  │
│                                         │
│ 🌲 Vegetasi & tipe ekosistem            │  ← dari field description / metadata
│                                         │
│ Prakiraan cuaca di puncak               │
│ [Hari ini] [Besok] [Lusa]               │  ← 3 card weather
│ suhu, kondisi, kecepatan angin          │
│                                         │
│ Jalur tersedia                          │
│ [Senaru] [Sembalun] ...                 │  ← pill per trail aktif
│                                         │
│ [  Booking {nama gunung}  ]  ← CTA     │
└─────────────────────────────────────────┘
```

### 5.4 Weather data

Integrasi direkomendasikan: **OpenWeatherMap API** (endpoint `/forecast`, free tier cukup) atau **BMKG Open API**.

Sementara belum terintegrasi, gunakan placeholder data statis atau fetch dari controller dengan mock response. Endpoint internal:

```php
Route::get('/api/mountains/{id}/weather', [PendakiController::class, 'mountainWeather'])
     ->name('api.mountain.weather');
```

Response format yang diharapkan frontend:

```json
{
    "forecast": [
        {
            "day": "Hari ini",
            "temp_c": 14,
            "condition": "Hujan ringan",
            "wind_kmh": 22,
            "icon": "rain"
        },
        {
            "day": "Besok",
            "temp_c": 16,
            "condition": "Berawan",
            "wind_kmh": 18,
            "icon": "cloud"
        },
        {
            "day": "Lusa",
            "temp_c": 18,
            "condition": "Cerah",
            "wind_kmh": 12,
            "icon": "sun"
        }
    ]
}
```

### 5.5 Status jalur

Tambahkan field `trail_status` ke model `Trail` atau `Mountain` dengan enum:

```
'open' | 'closed' | 'caution'
```

- `open` → banner hijau "✓ Jalur terbuka"
- `caution` → banner amber "⚠ Perhatikan kondisi jalur"
- `closed` → banner merah "✕ Jalur ditutup sementara" + disable tombol booking

---

## 6. Perubahan Booking Flow

### 6.1 Masalah saat ini

Alur saat ini:

```
Dashboard → "Booking Baru" → Stepper Step 1 (dropdown pilih gunung) → Step 2 → Step 3 → Step 4
```

Masalah: Pendaki diminta memilih gunung dari **dropdown teks** tanpa konteks visual apapun.

### 6.2 Alur yang direkomendasikan

```
Explore (mountain card) → klik "Booking {gunung}" → Stepper (gunung sudah pre-filled, mulai step 2)
```

### 6.3 Implementasi pre-fill via URL param

Tambahkan dukungan `?mountain_id=X` di controller dan Alpine:

```php
// BookingController@create
public function create(Request $request)
{
    $user = Auth::user();
    if (!$user->nik && !$user->passport_number) {
        return redirect()->route('profile.setup')
            ->with('warning', 'Lengkapi identitas terlebih dahulu.');
    }

    $mountains  = Mountain::active()->with(['regulation', 'trails' => fn($q) => $q->active()])->get();
    $preselect  = $request->query('mountain_id'); // ← TAMBAHKAN INI

    return view('pendaki.bookings.create', compact('mountains', 'user', 'preselect'));
}
```

```javascript
// Di bookingForm() Alpine — tambahkan di init()
init() {
    // Pre-fill dari URL param (dikirim dari Explore drawer)
    const urlParams = new URLSearchParams(window.location.search);
    const preselectId = urlParams.get('mountain_id') || '{{ $preselect ?? "" }}';

    if (preselectId) {
        this.selectedMountainId = parseInt(preselectId);
        this.fetchTrails(preselectId); // method yang sudah ada
        this.step = 2; // Skip step pilih gunung
    }
},
```

### 6.4 Tombol CTA di drawer

```html
<!-- Di drawer detail gunung -->
<a
    href="{{ route('pendaki.bookings.create') }}?mountain_id={{ $mountain->id }}"
    class="btn btn-primary w-full"
>
    Booking {{ $mountain->name }}
</a>
```

Atau via Alpine jika drawer dirender dinamis:

```javascript
bookNow(mountainId, mountainName) {
    window.location.href = `/my/bookings/create?mountain_id=${mountainId}`;
}
```

---

## 7. Perubahan Landing Page (`welcome.blade.php`)

### 7.1 Section yang perlu ditambahkan

**Mountain Spotlight Section** — ditempatkan setelah section "Cara Kerja", sebelum CTA akhir.

```php
// Di welcome.blade.php controller atau route closure
// Tambahkan data mountains ke view
Route::get('/', function () {
    $spotlightMountains = Mountain::active()
        ->with('regulation')
        ->orderByDesc('height_mdpl') // atau by popularity
        ->limit(3)
        ->get();

    return view('welcome', compact('spotlightMountains'));
});
```

### 7.2 Layout section spotlight

- Grid 3 kolom (desktop) / 1 kolom scroll horizontal (mobile)
- Setiap card: hero thumbnail + nama + ketinggian + badge grade + badge kuota + tombol "Lihat detail"
- Tombol "Lihat detail" untuk guest → redirect ke `/register?intent=mountain&id={id}` (intent tersimpan di session, setelah register langsung buka drawer gunung tersebut)
- Judul section: **"Destinasi populer minggu ini"**

### 7.3 Availability teaser strip

Tambahkan horizontal marquee atau static strip di atas atau bawah hero:

```
🏔 Rinjani — 8 slot tersisa  ·  🏔 Semeru — 34 slot tersisa  ·  🏔 Arjuno — buka 20 Jun  ·  ...
```

Data diambil dari query kuota real-time. Gunakan `cache()->remember('landing_quota', 300, fn() => ...)` agar tidak hit DB setiap request.

---

## 8. Model & Database — Tambahan yang Dibutuhkan

### 8.1 Field yang perlu ditambahkan ke `mountains` table

```php
// Migration baru
Schema::table('mountains', function (Blueprint $table) {
    $table->enum('trail_status', ['open', 'closed', 'caution'])->default('open')->after('is_active');
    $table->string('ecosystem_type')->nullable()->after('description'); // Contoh: "Hutan hujan tropis, savana alpine"
    $table->string('thumbnail_url')->nullable()->after('image_url');   // Opsional, bisa reuse image_url
});
```

### 8.2 Model scope yang berguna

```php
// Mountain.php — tambahkan scope
public function scopeWithQuotaSummary($query)
{
    return $query->withCount([
        'bookings as booked_this_week' => fn($q) => $q
            ->whereBetween('start_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereIn('status', ['paid', 'active'])
    ]);
}
```

---

## 9. File yang Perlu Dibuat / Diubah

### Dibuat baru

```
resources/views/pendaki/explore.blade.php       ← Halaman explore gunung
resources/views/components/mountain-card.blade.php  ← Reusable card component
resources/views/components/mountain-drawer.blade.php ← Drawer detail
app/Http/Controllers/Pendaki/ExploreController.php  ← (opsional, bisa di PendakiController)
```

### Dimodifikasi

```
resources/views/layouts/mobile.blade.php        ← Update bottom nav (5 tab, lihat section 3.2)
resources/views/welcome.blade.php               ← Tambah Mountain Spotlight section
app/Http/Controllers/Pendaki/PendakiController.php  ← Tambah explore() dan mountainDetail()
app/Http/Controllers/Pendaki/BookingController.php  ← Tambah dukungan ?mountain_id param
resources/views/pendaki/bookings/create.blade.php   ← Alpine init() baca preselect
routes/web.php                                  ← Tambah route explore + api detail
```

### Tidak perlu diubah (admin/pengelola tidak terdampak)

```
resources/views/layouts/web.blade.php           ← Tetap untuk admin & pengelola
resources/views/admin/*                         ← Tidak berubah
app/Http/Controllers/Admin/*                    ← Tidak berubah
```

---

## 10. Prioritas Implementasi

### Fase 1 — Fondasi layout (kerjakan dulu)

1. Pastikan route pendaki menggunakan `mobile.blade.php`
2. Update bottom nav `mobile.blade.php` sesuai 5 tab di section 3.2
3. Buat halaman `pendaki.explore` dengan mountain cards statis (tanpa drawer dulu)

### Fase 2 — Mountain drawer

4. Implementasi bottom sheet drawer (Alpine.js)
5. Fetch data gunung via `mountainDetail()` endpoint
6. Tampilkan semua section drawer (stat chips, artikel, jalur)

### Fase 3 — Booking flow improvement

7. Tambahkan `?mountain_id` pre-fill di booking stepper
8. Drawer: tombol "Booking {gunung}" diarahkan ke stepper dengan pre-fill

### Fase 4 — Landing page & data enrichment

9. Mountain Spotlight section di `welcome.blade.php`
10. Availability teaser strip
11. Integrasi weather API (OpenWeatherMap / BMKG)
12. Field `trail_status` & `ecosystem_type` di DB

---

## 11. Catatan Penting untuk Claude

Saat mengerjakan task implementasi berdasarkan dokumen ini:

- **Jangan ubah apapun di admin/pengelola** — layout sidebar dan semua fitur admin tidak terdampak perubahan ini.
- **Gunakan Alpine.js** untuk interaksi drawer, filter, dan pre-fill — konsisten dengan stack yang sudah ada di `bookings/create.blade.php`.
- **Mountain grade** di DB menggunakan format `I, II, III, IV, V` (Roman numeral), bukan `Easy/Moderate/Hard`. Map ke label display: I-II → Pemula, III → Menengah, IV-V → Lanjut.
- **`quota_per_trail_per_day`** ada di relasi `regulation` (hasOne dari Mountain), bukan langsung di tabel mountains. Selalu `->with('regulation')` saat query mountains untuk pendaki.
- **`mobile.blade.php` sudah ada bottom nav** — cukup update item-itemnya, jangan rebuild dari nol.
- **Booking stepper sudah punya `selectedMountainId` di Alpine state** — tinggal set dari URL param di `init()`.
- Route prefix pendaki adalah `/my/...` dengan name prefix `pendaki.` — contoh: `/my/explore` → `pendaki.explore`.
