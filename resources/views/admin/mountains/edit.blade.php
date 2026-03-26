<x-layouts.web>
    <x-slot:title>Edit Gunung — {{ $mountain->name }}</x-slot:title>
    <x-slot:breadcrumb>['Admin', 'Gunung & Jalur', $mountain->name, 'Edit']</x-slot:breadcrumb>

    <div style="max-width:760px;">
        <form method="POST" action="{{ route('admin.mountains.update', $mountain->id) }}">
            @csrf @method('PUT')

            {{-- Info Dasar --}}
            <div class="card mb-4">
                <h3 class="font-semibold text-sm mb-4" style="color:var(--color-text);">Informasi Gunung</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div class="sm:col-span-2">
                        <label class="form-label">Nama Gunung <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $mountain->name) }}" class="form-input @error('name') border-red-400 @enderror">
                        @error('name')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Lokasi <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="location" value="{{ old('location', $mountain->location) }}" class="form-input @error('location') border-red-400 @enderror">
                        @error('location')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Provinsi</label>
                        <input type="text" name="province" value="{{ old('province', $mountain->province) }}" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Ketinggian (mdpl) <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="height_mdpl" value="{{ old('height_mdpl', $mountain->height_mdpl) }}" class="form-input @error('height_mdpl') border-red-400 @enderror">
                        @error('height_mdpl')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Grade Jalur (Permen LHK 13/2024) <span style="color:#dc2626;">*</span></label>
                        <select name="grade" class="form-input @error('grade') border-red-400 @enderror">
                            <option value="">— Pilih Grade —</option>
                            @foreach(['I','II','III','IV','V'] as $g)
                            <option value="{{ $g }}" {{ old('grade', $mountain->grade) === $g ? 'selected' : '' }}>Grade {{ $g }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs mt-1" style="color:var(--color-text-muted);">Grade I–II: mudah, III: sedang, IV: sulit (wajib pemandu bersertifikat), V: sangat sulit (wajib tenaga ahli).</p>
                        @error('grade')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label">URL Foto</label>
                        <input type="url" name="image_url" value="{{ old('image_url', $mountain->image_url) }}" class="form-input">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" rows="3" class="form-input">{{ old('description', $mountain->description) }}</textarea>
                    </div>

                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $mountain->is_active) ? 'checked' : '' }}
                                   style="width:16px;height:16px;accent-color:var(--color-forest-600);">
                            <span class="text-sm font-medium" style="color:var(--color-text);">Gunung aktif (bisa dipesan)</span>
                        </label>
                    </div>

                    @if(auth()->user()->role === 'admin')
                    <div class="sm:col-span-2">
                        <label class="form-label">Pengelola Taman Nasional</label>
                        <select name="pengelola_id" class="form-input">
                            <option value="">— Belum ditugaskan —</option>
                            @foreach($pengelolaList as $p)
                            <option value="{{ $p->id }}" {{ old('pengelola_id', $mountain->pengelola_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} &lt;{{ $p->email }}&gt;
                            </option>
                            @endforeach
                        </select>
                        <p class="text-xs mt-1" style="color:var(--color-text-muted);">Pengelola yang bertugas mengelola gunung ini.</p>
                        @error('pengelola_id')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>
                    @else
                    <div class="sm:col-span-2">
                        <label class="form-label">Pengelola</label>
                        <p class="text-sm" style="color:var(--color-text);">{{ $mountain->pengelola?->name ?? '—' }}</p>
                        <p class="text-xs" style="color:var(--color-text-muted);">Hanya admin yang dapat mengubah penugasan pengelola.</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Regulasi --}}
            <div class="card mb-5">
                <h3 class="font-semibold text-sm mb-4" style="color:var(--color-text);">Regulasi Pendakian</h3>
                @php $reg = $mountain->regulation; @endphp

                {{-- Harga --}}
                <p class="text-xs font-semibold mb-2 mt-1" style="color:var(--color-text-muted);text-transform:uppercase;letter-spacing:.05em;">Harga Tiket per Orang (Rp)</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5" style="padding:1rem;background:var(--color-forest-50);border-radius:0.5rem;border:1px solid var(--color-border);">
                    <div>
                        <label class="form-label">Lokal — Weekday (Senin–Jumat) <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="base_price" value="{{ old('base_price', $reg?->base_price ?? 0) }}" class="form-input @error('base_price') border-red-400 @enderror" min="0">
                        @error('base_price')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Lokal — Weekend (Sabtu/Minggu)</label>
                        <input type="number" name="price_weekend" value="{{ old('price_weekend', $reg?->price_weekend) }}" class="form-input" min="0" placeholder="Kosongkan = sama dengan weekday">
                        <p class="text-xs mt-1" style="color:var(--color-text-muted);">Jika kosong, menggunakan harga weekday lokal.</p>
                        @error('price_weekend')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Mancanegara — Weekday</label>
                        <input type="number" name="price_foreign_weekday" value="{{ old('price_foreign_weekday', $reg?->price_foreign_weekday) }}" class="form-input" min="0" placeholder="Kosongkan = sama dengan lokal">
                        <p class="text-xs mt-1" style="color:var(--color-text-muted);">Jika kosong, menggunakan harga weekday lokal.</p>
                        @error('price_foreign_weekday')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Mancanegara — Weekend</label>
                        <input type="number" name="price_foreign_weekend" value="{{ old('price_foreign_weekend', $reg?->price_foreign_weekend) }}" class="form-input" min="0" placeholder="Kosongkan = sama dengan mancanegara weekday">
                        <p class="text-xs mt-1" style="color:var(--color-text-muted);">Jika kosong, menggunakan harga weekday mancanegara.</p>
                        @error('price_foreign_weekend')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Harga Pelajar --}}
                <p class="text-xs font-semibold mb-2" style="color:var(--color-text-muted);text-transform:uppercase;letter-spacing:.05em;">Harga Pelajar / Anak (&lt; 17 Tahun)</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5" style="padding:1rem;background:#fefce8;border-radius:0.5rem;border:1px solid #fde68a;">
                    <div>
                        <label class="form-label">Harga Pelajar — Weekday</label>
                        <input type="number" name="price_student" value="{{ old('price_student', $reg?->price_student) }}" class="form-input" min="0" placeholder="Kosongkan = sama dengan lokal weekday">
                        <p class="text-xs mt-1" style="color:var(--color-text-muted);">Untuk peserta WNI berumur di bawah 17 tahun. Jika kosong, menggunakan harga lokal weekday.</p>
                        @error('price_student')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Harga Pelajar — Weekend</label>
                        <input type="number" name="price_student_weekend" value="{{ old('price_student_weekend', $reg?->price_student_weekend) }}" class="form-input" min="0" placeholder="Kosongkan = sama dengan pelajar weekday">
                        <p class="text-xs mt-1" style="color:var(--color-text-muted);">Jika kosong, menggunakan harga pelajar weekday.</p>
                        @error('price_student_weekend')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="minor_must_be_accompanied" value="1"
                                   {{ old('minor_must_be_accompanied', $reg?->minor_must_be_accompanied ?? true) ? 'checked' : '' }}
                                   style="width:16px;height:16px;accent-color:var(--color-forest-600);">
                            <span class="text-sm font-medium" style="color:var(--color-text);">Peserta &lt; 17 tahun wajib didampingi minimal 1 pendaki dewasa dalam satu booking</span>
                        </label>
                    </div>
                </div>

                {{-- Kuota --}}
                <p class="text-xs font-semibold mb-2" style="color:var(--color-text-muted);text-transform:uppercase;letter-spacing:.05em;">Kuota Pendaki</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5" style="padding:1rem;background:var(--color-forest-50);border-radius:0.5rem;border:1px solid var(--color-border);">
                    <div>
                        <label class="form-label">Kuota Total per Hari (semua jalur)</label>
                        <input type="number" name="quota_total_per_day" value="{{ old('quota_total_per_day', $reg?->quota_total_per_day) }}" class="form-input" min="1" placeholder="Kosongkan = tidak ada batas total">
                        <p class="text-xs mt-1" style="color:var(--color-text-muted);">Batas maksimum pendaki per hari dari seluruh jalur gabungan.</p>
                        @error('quota_total_per_day')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Kuota Default per Jalur per Hari <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="quota_per_trail_per_day" value="{{ old('quota_per_trail_per_day', $reg?->quota_per_trail_per_day ?? 50) }}" class="form-input" min="1">
                        <p class="text-xs mt-1" style="color:var(--color-text-muted);">Berlaku untuk jalur yang tidak punya kuota khusus.</p>
                        @error('quota_per_trail_per_day')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Aturan lain --}}
                <p class="text-xs font-semibold mb-2" style="color:var(--color-text-muted);text-transform:uppercase;letter-spacing:.05em;">Aturan Pendakian</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Maks. Hari Pendakian <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="max_hiking_days" value="{{ old('max_hiking_days', $reg?->max_hiking_days ?? 3) }}" class="form-input" min="1">
                    </div>

                    <div>
                        <label class="form-label">Maks. Peserta per Booking <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="max_participants_per_account" value="{{ old('max_participants_per_account', $reg?->max_participants_per_account ?? 10) }}" class="form-input" min="1">
                    </div>

                    <div>
                        <label class="form-label">Batas Jam Checkout <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="checkout_deadline_hour" value="{{ old('checkout_deadline_hour', $reg?->checkout_deadline_hour ?? 14) }}" class="form-input" min="0" max="23">
                    </div>

                    <div>
                        <label class="form-label">Biaya Guide per Hari (Rp)</label>
                        <input type="number" name="guide_price_per_day" value="{{ old('guide_price_per_day', $reg?->guide_price_per_day) }}" class="form-input" min="0" placeholder="Kosongkan = gratis / tidak ada guide">
                        <p class="text-xs mt-1" style="color:var(--color-text-muted);">Flat rate per hari (bukan per orang). Ditambahkan ke total harga jika pendaki memilih pakai guide.</p>
                        @error('guide_price_per_day')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Level Kewajiban Pemandu (Permen LHK 13/2024)</label>
                        <select name="guide_requirement_level" class="form-input">
                            @php $currentLevel = old('guide_requirement_level', $reg?->guide_requirement_level ?? 'none'); @endphp
                            <option value="none" {{ $currentLevel === 'none' ? 'selected' : '' }}>Tidak Wajib (Grade I & II)</option>
                            <option value="recommended" {{ $currentLevel === 'recommended' ? 'selected' : '' }}>Sangat Disarankan (Grade III)</option>
                            <option value="mandatory" {{ $currentLevel === 'mandatory' ? 'selected' : '' }}>WAJIB Bersertifikat (Grade IV)</option>
                            <option value="expert_only" {{ $currentLevel === 'expert_only' ? 'selected' : '' }}>WAJIB Tenaga Ahli (Grade V)</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Rasio Maks. Pendaki per Pemandu</label>
                        <input type="number" name="guide_ratio_max_hikers" value="{{ old('guide_ratio_max_hikers', $reg?->guide_ratio_max_hikers ?? 7) }}" class="form-input" min="1" placeholder="cth. 7">
                        <p class="text-xs mt-1" style="color:var(--color-text-muted);">Maks. jumlah pendaki per 1 pemandu. Kosongkan = tidak ada rasio.</p>
                    </div>

                    <div class="flex items-center gap-3 pt-5">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="guide_required" value="1" {{ old('guide_required', $reg?->guide_required) ? 'checked' : '' }}
                                   style="width:16px;height:16px;accent-color:var(--color-forest-600);">
                            <span class="text-sm font-medium" style="color:var(--color-text);">Guide wajib (legacy)</span>
                        </label>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label">Syarat Pengalaman Minimum (mdpl)</label>
                        <input type="number" name="min_elevation_experience" value="{{ old('min_elevation_experience', $reg?->min_elevation_experience) }}" class="form-input" min="0" placeholder="Kosongkan jika tidak ada syarat">
                        <p class="text-xs mt-1" style="color:var(--color-text-muted);">Pendaki harus pernah menyelesaikan pendakian di gunung dengan ketinggian minimal ini (contoh: 3000 untuk syarat pernah mendaki gunung 3000 mdpl).</p>
                        @error('min_elevation_experience')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('admin.mountains.show', $mountain->id) }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>

</x-layouts.web>
