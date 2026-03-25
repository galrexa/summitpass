<x-layouts.web>
    <x-slot:title>Tambah Gunung</x-slot:title>
    <x-slot:breadcrumb>['Admin', 'Gunung & Jalur', 'Tambah']</x-slot:breadcrumb>

    <div style="max-width:760px;">
        <form method="POST" action="{{ route('admin.mountains.store') }}">
            @csrf

            {{-- Info Dasar --}}
            <div class="card mb-4">
                <h3 class="font-semibold text-sm mb-4" style="color:var(--color-text);">Informasi Gunung</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div class="sm:col-span-2">
                        <label class="form-label">Nama Gunung <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-input @error('name') border-red-400 @enderror" placeholder="cth. Gunung Semeru">
                        @error('name')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Lokasi <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="location" value="{{ old('location') }}" class="form-input @error('location') border-red-400 @enderror" placeholder="cth. Lumajang, Jawa Timur">
                        @error('location')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Provinsi</label>
                        <input type="text" name="province" value="{{ old('province') }}" class="form-input" placeholder="cth. Jawa Timur">
                    </div>

                    <div>
                        <label class="form-label">Ketinggian (mdpl) <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="height_mdpl" value="{{ old('height_mdpl') }}" class="form-input @error('height_mdpl') border-red-400 @enderror" placeholder="3676">
                        @error('height_mdpl')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Tingkat Kesulitan <span style="color:#dc2626;">*</span></label>
                        <select name="difficulty" class="form-input @error('difficulty') border-red-400 @enderror">
                            <option value="">Pilih...</option>
                            <option value="Easy" {{ old('difficulty') === 'Easy' ? 'selected' : '' }}>Easy</option>
                            <option value="Moderate" {{ old('difficulty') === 'Moderate' ? 'selected' : '' }}>Moderate</option>
                            <option value="Hard" {{ old('difficulty') === 'Hard' ? 'selected' : '' }}>Hard</option>
                        </select>
                        @error('difficulty')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label">URL Foto</label>
                        <input type="url" name="image_url" value="{{ old('image_url') }}" class="form-input" placeholder="https://...">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" rows="3" class="form-input" placeholder="Deskripsi singkat gunung...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Regulasi --}}
            <div class="card mb-5">
                <h3 class="font-semibold text-sm mb-4" style="color:var(--color-text);">Regulasi Pendakian</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="form-label">Harga Dasar per Orang (Rp) <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="base_price" value="{{ old('base_price', 0) }}" class="form-input @error('base_price') border-red-400 @enderror" min="0">
                        @error('base_price')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Kuota per Jalur per Hari <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="quota_per_trail_per_day" value="{{ old('quota_per_trail_per_day', 50) }}" class="form-input @error('quota_per_trail_per_day') border-red-400 @enderror" min="1">
                        @error('quota_per_trail_per_day')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Maks. Hari Pendakian <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="max_hiking_days" value="{{ old('max_hiking_days', 3) }}" class="form-input @error('max_hiking_days') border-red-400 @enderror" min="1">
                        @error('max_hiking_days')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Maks. Peserta per Booking <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="max_participants_per_account" value="{{ old('max_participants_per_account', 10) }}" class="form-input @error('max_participants_per_account') border-red-400 @enderror" min="1">
                        @error('max_participants_per_account')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Batas Jam Checkout <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="checkout_deadline_hour" value="{{ old('checkout_deadline_hour', 14) }}" class="form-input @error('checkout_deadline_hour') border-red-400 @enderror" min="0" max="23">
                        <p class="text-xs mt-1" style="color:var(--color-text-muted);">Jam checkout terakhir (format 24 jam, cth. 14 = pukul 14:00)</p>
                        @error('checkout_deadline_hour')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center gap-3 pt-5">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="guide_required" value="1" {{ old('guide_required') ? 'checked' : '' }}
                                   style="width:16px;height:16px;accent-color:var(--color-forest-600);">
                            <span class="text-sm font-medium" style="color:var(--color-text);">Guide wajib</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn btn-primary">Simpan Gunung</button>
                <a href="{{ route('admin.mountains.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>

</x-layouts.web>
