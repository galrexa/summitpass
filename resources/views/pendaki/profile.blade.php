<x-layouts.web>
    <x-slot:title>Profil Saya</x-slot:title>
    <x-slot:breadcrumb>['SummitPass', 'Profil Saya']</x-slot:breadcrumb>

    <div style="max-width:640px;">

        {{-- Profile incomplete banner --}}
        @if(!$user->nik && !$user->passport_number)
        <div class="alert" style="background:#fef3c7;border:1px solid #f59e0b;border-radius:10px;display:flex;align-items:flex-start;gap:.75rem;padding:.875rem 1.25rem;margin-bottom:1.5rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#b45309" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:.1rem;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <div>
                <div style="font-size:.875rem;font-weight:600;color:#92400e;">Profil belum lengkap</div>
                <div style="font-size:.8rem;color:#92400e;margin-top:.15rem;">NIK atau nomor paspor belum diisi. Kamu perlu melengkapinya sebelum bisa booking SIMAKSI.</div>
                <a href="{{ route('profile.setup') }}" class="btn btn-sm" style="margin-top:.625rem;background:#f59e0b;color:#fff;border:none;">Lengkapi Identitas</a>
            </div>
        </div>
        @endif

        {{-- Profile card --}}
        <div class="card" style="margin-bottom:1.25rem;">
            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
                <div class="avatar" style="width:52px;height:52px;font-size:1.1rem;background:var(--color-forest-600);color:white;flex-shrink:0;">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <div style="font-size:1rem;font-weight:700;color:var(--color-text);">{{ $user->name }}</div>
                    <div style="font-size:.8rem;color:var(--color-text-muted);">{{ $user->email }}</div>
                    <span class="badge badge-green" style="margin-top:.25rem;">Pendaki</span>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                @foreach([
                    ['No. HP', $user->phone ?? '—'],
                    ['NIK', $user->nik ? '••••••••••••' . substr($user->nik, -4) : '—'],
                    ['No. Paspor', $user->passport_number ?? '—'],
                    ['Login via', $user->google_id ? 'Google' : 'Email'],
                ] as [$label, $value])
                <div>
                    <div style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);margin-bottom:.25rem;">{{ $label }}</div>
                    <div style="font-size:.875rem;color:var(--color-text);">{{ $value }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
            <a href="{{ route('profile.setup') }}" class="btn btn-outline btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit Identitas
            </a>
            <a href="{{ route('pendaki.settings') }}" class="btn btn-outline btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.42 1.42M4.93 4.93l1.42 1.42M12 2v2M12 20v2M20 12h2M2 12h2M19.07 19.07l-1.42-1.42M4.93 19.07l1.42-1.42"/></svg>
                Pengaturan Akun
            </a>
        </div>

    </div>

</x-layouts.web>
