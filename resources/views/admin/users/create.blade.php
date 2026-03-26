<x-layouts.web>
    <x-slot:title>Tambah Akun</x-slot:title>
    <x-slot:breadcrumb>['Admin', 'Pengguna', 'Tambah Akun']</x-slot:breadcrumb>

    <div style="max-width:560px;">
        <div class="card">
            <h3 class="font-semibold text-sm mb-1" style="color:var(--color-text);">Buat Akun Baru</h3>
            <p class="text-xs mb-5" style="color:var(--color-text-muted);">
                Hanya untuk akun <strong>Pengelola TN</strong> dan <strong>Officer</strong>.
                Akun pendaki dibuat sendiri melalui halaman registrasi.
            </p>

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="flex flex-col gap-4">

                    <div>
                        <label class="form-label">Nama Lengkap <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-input @error('name') border-red-400 @enderror" placeholder="cth. Budi Santoso">
                        @error('name')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Email <span style="color:#dc2626;">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="form-input @error('email') border-red-400 @enderror" placeholder="email@contoh.com">
                        @error('email')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="form-input" placeholder="cth. 0812-3456-7890">
                    </div>

                    <div>
                        <label class="form-label">Role <span style="color:#dc2626;">*</span></label>
                        <select name="role" id="roleSelect" class="form-input @error('role') border-red-400 @enderror"
                                onchange="document.getElementById('mountainField').style.display = this.value === 'pengelola_tn' ? 'block' : 'none'">
                            <option value="">Pilih role...</option>
                            <option value="pengelola_tn" {{ old('role') === 'pengelola_tn' ? 'selected' : '' }}>
                                Pengelola Taman Nasional
                            </option>
                            <option value="officer" {{ old('role') === 'officer' ? 'selected' : '' }}>
                                Officer (Petugas Lapangan)
                            </option>
                        </select>
                        @error('role')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div id="mountainField" style="display:{{ old('role') === 'pengelola_tn' ? 'block' : 'none' }};">
                        <label class="form-label">Tugaskan ke Gunung</label>
                        <select name="mountain_id" class="form-input">
                            <option value="">— Pilih gunung —</option>
                            @foreach($pengelolaList as $m)
                            <option value="{{ $m->id }}"
                                    {{ old('mountain_id') == $m->id ? 'selected' : '' }}
                                    {{ $m->pengelola_id ? 'style=color:#999' : '' }}>
                                {{ $m->name }}
                                @if($m->pengelola_id) (sudah ada pengelola) @endif
                            </option>
                            @endforeach
                        </select>
                        <p class="text-xs mt-1" style="color:var(--color-text-muted);">Opsional. Gunung yang akan dikelola oleh pengelola ini.</p>
                        @error('mountain_id')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Password <span style="color:#dc2626;">*</span></label>
                        <input type="password" name="password"
                               class="form-input @error('password') border-red-400 @enderror"
                               placeholder="Minimal 8 karakter">
                        @error('password')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" class="btn btn-primary">Buat Akun</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

</x-layouts.web>
