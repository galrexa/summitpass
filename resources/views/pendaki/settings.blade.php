<x-layouts.web>
    <x-slot:title>Pengaturan Akun</x-slot:title>
    <x-slot:breadcrumb>['SummitPass', 'Pengaturan Akun']</x-slot:breadcrumb>

    <div style="max-width:560px;">

        {{-- Change password --}}
        <div class="card" style="margin-bottom:1.25rem;">
            <h3 style="font-size:.9rem;font-weight:700;color:var(--color-text);margin-bottom:1.25rem;">Ubah Password</h3>

            @if($user->google_id && !$user->password)
            <div style="background:var(--color-forest-50);border:1px solid var(--color-forest-200);border-radius:8px;padding:.875rem 1rem;font-size:.825rem;color:var(--color-forest-800);">
                Akun ini terdaftar melalui Google. Password tidak diperlukan — masuk menggunakan Google Sign-In.
            </div>
            @else
            <form method="POST" action="#" x-data="{ showOld: false, showNew: false, showConfirm: false }">
                @csrf
                @method('PATCH')

                <div style="margin-bottom:1rem;">
                    <label class="form-label">Password saat ini</label>
                    <div style="position:relative;">
                        <input :type="showOld ? 'text' : 'password'" name="current_password"
                            placeholder="••••••••"
                            class="form-input" style="padding-right:2.75rem;">
                        <button type="button" @click="showOld=!showOld"
                            style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--color-text-muted);padding:0;">
                            <svg x-show="!showOld" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="showOld" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <div style="margin-bottom:1rem;">
                    <label class="form-label">Password baru</label>
                    <div style="position:relative;">
                        <input :type="showNew ? 'text' : 'password'" name="password"
                            placeholder="Minimal 8 karakter"
                            class="form-input" style="padding-right:2.75rem;">
                        <button type="button" @click="showNew=!showNew"
                            style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--color-text-muted);padding:0;">
                            <svg x-show="!showNew" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="showNew" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <div style="margin-bottom:1.25rem;">
                    <label class="form-label">Konfirmasi password baru</label>
                    <div style="position:relative;">
                        <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation"
                            placeholder="Ulangi password baru"
                            class="form-input" style="padding-right:2.75rem;">
                        <button type="button" @click="showConfirm=!showConfirm"
                            style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--color-text-muted);padding:0;">
                            <svg x-show="!showConfirm" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="showConfirm" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">Simpan Password</button>
            </form>
            @endif
        </div>

        {{-- Danger zone --}}
        <div class="card" style="border-color:#fca5a5;">
            <h3 style="font-size:.9rem;font-weight:700;color:#dc2626;margin-bottom:.5rem;">Zona Berbahaya</h3>
            <p style="font-size:.8rem;color:var(--color-text-muted);margin-bottom:1rem;">Tindakan ini tidak dapat dibatalkan.</p>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Keluar dari Akun
                </button>
            </form>
        </div>

    </div>

</x-layouts.web>
