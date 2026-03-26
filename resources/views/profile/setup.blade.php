<x-layouts.web>
    <x-slot:title>Lengkapi Profil</x-slot:title>
    <x-slot:breadcrumb>['Profil', 'Lengkapi Identitas']</x-slot:breadcrumb>

    <div style="max-width:560px;" x-data="{ nationality: '{{ old('nationality', $user->passport_number && !$user->nik ? 'wna' : 'wni') }}' }">

        <div class="mb-5">
            <h2 class="text-lg font-semibold" style="color:var(--color-text);">Lengkapi Identitas</h2>
            <p class="text-sm mt-0.5" style="color:var(--color-text-muted);">
                Halo, <strong style="color:var(--color-text);">{{ Auth::user()->name }}</strong>. Tambahkan data identitas untuk bisa melakukan booking SIMAKSI.
            </p>
        </div>

        @if(session('success'))
        <div class="alert alert-success mb-5">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><polyline points="20 6 9 17 4 12"/></svg>
            <span class="text-sm">{{ session('success') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-error mb-5">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span class="text-sm">{{ $errors->first() }}</span>
        </div>
        @endif

        <div class="card">
            <form method="POST" action="{{ route('profile.setup.save') }}" x-data="{ loading: false }" @submit="loading = true">
                @csrf

                {{-- Nama (readonly) --}}
                <div class="mb-4">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" value="{{ Auth::user()->name }}" class="form-input" disabled style="background:var(--color-surface-alt);color:var(--color-text-muted);">
                </div>

                {{-- Email (readonly) --}}
                <div class="mb-4">
                    <label class="form-label">Email</label>
                    <input type="email" value="{{ Auth::user()->email }}" class="form-input" disabled style="background:var(--color-surface-alt);color:var(--color-text-muted);">
                </div>

                {{-- No. HP --}}
                <div class="mb-4">
                    <label for="phone" class="form-label">
                        No. HP
                        <span style="color:var(--color-text-muted);font-weight:400;">(opsional)</span>
                    </label>
                    <input type="tel" id="phone" name="phone"
                        value="{{ old('phone', Auth::user()->phone) }}"
                        placeholder="08xxxxxxxxxx"
                        class="form-input {{ $errors->has('phone') ? 'border-red-400' : '' }}">
                    @error('phone')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                {{-- Kewarganegaraan --}}
                <div class="mb-4">
                    <label class="form-label">Kewarganegaraan <span style="color:#dc2626;">*</span></label>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="nationality_radio" value="wni"
                                x-model="nationality"
                                style="accent-color:var(--color-forest-600);">
                            <span class="text-sm font-medium">WNI (Warga Negara Indonesia)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="nationality_radio" value="wna"
                                x-model="nationality"
                                style="accent-color:var(--color-forest-600);">
                            <span class="text-sm font-medium">WNA (Warga Negara Asing)</span>
                        </label>
                    </div>
                    <input type="hidden" name="nationality" :value="nationality">
                </div>

                {{-- NIK --}}
                <div class="mb-4" x-show="nationality === 'wni'">
                    <label for="nik" class="form-label">NIK <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="nik" name="nik"
                        value="{{ old('nik', $user->nik) }}"
                        placeholder="16 digit Nomor Induk Kependudukan"
                        maxlength="16" inputmode="numeric"
                        class="form-input {{ $errors->has('nik') ? 'border-red-400' : '' }}">
                    <p class="text-xs mt-1" style="color:var(--color-text-muted);">NIK tercantum pada KTP atau KK Anda.</p>
                    @error('nik')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                {{-- Passport --}}
                <div class="mb-6" x-show="nationality === 'wna'" style="display:none;">
                    <label for="passport_number" class="form-label">Nomor Paspor <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="passport_number" name="passport_number"
                        value="{{ old('passport_number', $user->passport_number) }}"
                        placeholder="Nomor paspor aktif"
                        class="form-input {{ $errors->has('passport_number') ? 'border-red-400' : '' }}">
                    @error('passport_number')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="btn btn-primary"
                        :disabled="loading"
                        :style="loading ? 'opacity:0.7;cursor:not-allowed;' : ''">
                        <template x-if="loading">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 1s linear infinite;"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                        </template>
                        <span x-text="loading ? 'Menyimpan...' : 'Simpan & Lanjutkan'"></span>
                    </button>
                    <a href="{{ route('pendaki.bookings') }}" class="btn btn-ghost" style="color:var(--color-text-muted);">
                        Lewati untuk sekarang
                    </a>
                </div>

            </form>
        </div>

    </div>

    <style>
    @keyframes spin { to { transform: rotate(360deg); } }
    </style>

</x-layouts.web>
