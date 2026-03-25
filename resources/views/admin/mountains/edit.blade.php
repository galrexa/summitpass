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
                        <label class="form-label">Tingkat Kesulitan <span style="color:#dc2626;">*</span></label>
                        <select name="difficulty" class="form-input">
                            @foreach(['Easy','Moderate','Hard'] as $d)
                            <option value="{{ $d }}" {{ old('difficulty', $mountain->difficulty) === $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
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
                </div>
            </div>

            {{-- Regulasi --}}
            <div class="card mb-5">
                <h3 class="font-semibold text-sm mb-4" style="color:var(--color-text);">Regulasi Pendakian</h3>
                @php $reg = $mountain->regulation; @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="form-label">Harga Dasar per Orang (Rp) <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="base_price" value="{{ old('base_price', $reg?->base_price ?? 0) }}" class="form-input" min="0">
                        @error('base_price')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Kuota per Jalur per Hari <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="quota_per_trail_per_day" value="{{ old('quota_per_trail_per_day', $reg?->quota_per_trail_per_day ?? 50) }}" class="form-input" min="1">
                        @error('quota_per_trail_per_day')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

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

                    <div class="flex items-center gap-3 pt-5">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="guide_required" value="1" {{ old('guide_required', $reg?->guide_required) ? 'checked' : '' }}
                                   style="width:16px;height:16px;accent-color:var(--color-forest-600);">
                            <span class="text-sm font-medium" style="color:var(--color-text);">Guide wajib</span>
                        </label>
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
