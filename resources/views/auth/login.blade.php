<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — SummitPass</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            min-height: 100dvh;
            display: grid;
            grid-template-columns: 1fr;
            background: var(--color-surface-alt);
        }
        @media (min-width: 1024px) {
            body { grid-template-columns: 1fr 1fr; }
        }
        .auth-illustration {
            background: linear-gradient(160deg, var(--color-forest-900) 0%, var(--color-lake-800) 60%, var(--color-forest-800) 100%);
            display: none;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        @media (min-width: 1024px) { .auth-illustration { display: flex; } }
        .auth-illustration::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .mountain-silhouette {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            opacity: 0.12;
        }
    </style>
</head>
<body x-data="{ showPass: false }">

    {{-- Left: Illustration panel --}}
    <div class="auth-illustration">
        {{-- Logo --}}
        <a href="/" style="display:inline-flex;align-items:center;gap:0.625rem;text-decoration:none;color:white;">
            <div style="width:36px;height:36px;background:var(--color-forest-600);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 20l5-9 4 6 3-4 6 7H3z"/>
                </svg>
            </div>
            <span style="font-weight:700;font-size:1.1rem;">SummitPass</span>
        </a>

        {{-- Center content --}}
        <div style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:0.5rem;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);border-radius:20px;padding:0.35rem 1rem;margin-bottom:1.5rem;">
                <span style="width:7px;height:7px;border-radius:50%;background:var(--color-forest-400);display:inline-block;"></span>
                <span style="font-size:0.75rem;color:rgba(255,255,255,0.7);font-weight:500;">Platform Keselamatan Pendakian Indonesia</span>
            </div>
            <h2 style="font-size:2rem;font-weight:800;color:#fff;line-height:1.2;letter-spacing:-0.025em;margin-bottom:1rem;">
                Catat setiap langkah,<br>selamat sampai tujuan.
            </h2>
            <p style="color:rgba(255,255,255,0.6);font-size:0.95rem;line-height:1.7;max-width:380px;">
                Dari SIMAKSI digital hingga QR SummitPass di setiap pos — jejak perjalanan Anda tercatat untuk keselamatan Anda.
            </p>

            {{-- Stats --}}
            <div style="display:flex;gap:2rem;margin-top:2.5rem;">
                @foreach([['15+', 'Gunung'], ['10k+', 'Pendaki'], ['100%', 'Termonitor']] as $stat)
                <div>
                    <div style="font-size:1.5rem;font-weight:800;color:#fff;letter-spacing:-0.025em;">{{ $stat[0] }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.45);margin-top:0.1rem;">{{ $stat[1] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Testimonial / quote --}}
        <div style="position:relative;z-index:1;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:1.25rem 1.5rem;">
            <p style="font-size:0.875rem;color:rgba(255,255,255,0.8);line-height:1.6;font-style:italic;margin-bottom:0.875rem;">
                "Dengan SummitPass, pengelola kawasan bisa tahu posisi terakhir pendaki secara real-time. Ini yang selama ini kami butuhkan."
            </p>
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <div class="avatar" style="width:32px;height:32px;font-size:0.7rem;background:var(--color-forest-600);color:white;">RH</div>
                <div>
                    <div style="font-size:0.8rem;font-weight:600;color:#fff;">Rudi Hartono</div>
                    <div style="font-size:0.72rem;color:rgba(255,255,255,0.45);">Pengelola TN Semeru</div>
                </div>
            </div>
        </div>

        {{-- Mountain silhouette SVG --}}
        <svg class="mountain-silhouette" viewBox="0 0 1200 300" fill="white" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0 300 L0 200 L150 120 L250 180 L400 60 L550 160 L650 40 L800 140 L900 80 L1050 160 L1200 100 L1200 300 Z"/>
        </svg>
    </div>

    {{-- Right: Login form --}}
    <div style="display:flex;flex-direction:column;justify-content:center;align-items:center;padding:2rem 1.5rem;min-height:100dvh;">
        <div style="width:100%;max-width:400px;">

            {{-- Mobile logo --}}
            <a href="/" style="display:inline-flex;align-items:center;gap:0.5rem;text-decoration:none;color:var(--color-text);margin-bottom:2.5rem;" class="lg:hidden">
                <div style="width:30px;height:30px;background:var(--color-forest-700);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 20l5-9 4 6 3-4 6 7H3z"/>
                    </svg>
                </div>
                <span style="font-weight:700;font-size:1rem;">SummitPass</span>
            </a>

            {{-- Heading --}}
            <div style="margin-bottom:2rem;">
                <h1 style="font-size:1.75rem;font-weight:800;letter-spacing:-0.025em;color:var(--color-text);margin-bottom:0.375rem;">Selamat datang</h1>
                <p style="font-size:0.875rem;color:var(--color-text-muted);">Masuk ke akun SummitPass Anda untuk melanjutkan.</p>
            </div>

            {{-- Error alert --}}
            @if($errors->any() || session('error'))
            <div class="alert alert-error mb-5">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span class="text-sm">
                    @if(session('error'))
                        {{ session('error') }}
                    @else
                        {{ $errors->first() }}
                    @endif
                </span>
            </div>
            @endif

            {{-- Success alert --}}
            @if(session('success'))
            <div class="alert alert-success mb-5">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                    <polyline points="20,6 9,17 4,12"/>
                </svg>
                <span class="text-sm">{{ session('success') }}</span>
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" x-data="{ loading: false }" @submit="loading = true">
                @csrf

                {{-- Email --}}
                <div style="margin-bottom:1.25rem;">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="nama@email.com"
                        autocomplete="email"
                        autofocus
                        class="form-input {{ $errors->has('email') ? 'border-red-400' : '' }}"
                        required
                    >
                    @error('email')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div style="margin-bottom:1.75rem;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.375rem;">
                        <label for="password" class="form-label" style="margin-bottom:0;">Password</label>
                        @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="font-size:0.78rem;color:var(--color-forest-700);text-decoration:none;font-weight:500;">
                            Lupa password?
                        </a>
                        @endif
                    </div>
                    <div style="position:relative;">
                        <input
                            :type="showPass ? 'text' : 'password'"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            class="form-input {{ $errors->has('password') ? 'border-red-400' : '' }}"
                            style="padding-right:2.75rem;"
                            required
                        >
                        <button
                            type="button"
                            @click="showPass = !showPass"
                            style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--color-text-muted);padding:0;"
                            :title="showPass ? 'Sembunyikan password' : 'Tampilkan password'"
                        >
                            <svg x-show="!showPass" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg x-show="showPass" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div style="display:flex;align-items:center;gap:0.625rem;margin-bottom:1.5rem;">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        style="width:16px;height:16px;accent-color:var(--color-forest-700);cursor:pointer;"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <label for="remember" style="font-size:0.85rem;color:var(--color-text-muted);cursor:pointer;">Ingat saya di perangkat ini</label>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="btn btn-primary btn-lg w-full"
                    :disabled="loading"
                    :style="loading ? 'opacity:0.7;cursor:not-allowed;' : ''"
                >
                    <span x-show="loading" class="animate-spin" style="display:inline-block;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                        </svg>
                    </span>
                    <span x-show="!loading">Masuk</span>
                    <span x-show="loading" style="display:none;">Memproses...</span>
                </button>

            </form>

            {{-- Divider --}}
            <div style="display:flex;align-items:center;gap:1rem;margin:1.75rem 0 1.25rem;">
                <div style="flex:1;height:1px;background:var(--color-border);"></div>
                <span style="font-size:0.75rem;color:var(--color-text-muted);">atau</span>
                <div style="flex:1;height:1px;background:var(--color-border);"></div>
            </div>

            {{-- Google Sign-In --}}
            <a
                href="{{ route('auth.google') }}"
                style="display:flex;align-items:center;justify-content:center;gap:0.75rem;width:100%;padding:0.7rem 1rem;border:1px solid var(--color-border);border-radius:10px;background:#fff;color:#3c4043;font-size:0.875rem;font-weight:500;text-decoration:none;transition:box-shadow 0.15s;margin-bottom:1.5rem;"
                onmouseover="this.style.boxShadow='0 1px 6px rgba(0,0,0,0.12)'"
                onmouseout="this.style.boxShadow='none'"
            >
                <svg width="18" height="18" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                Masuk dengan Google
            </a>

            {{-- Register link --}}
            <p style="text-align:center;font-size:0.875rem;color:var(--color-text-muted);">
                Belum punya akun?
                @if(Route::has('register'))
                <a href="{{ route('register') }}" style="color:var(--color-forest-700);font-weight:600;text-decoration:none;">
                    Daftar sekarang
                </a>
                @endif
            </p>

            {{-- Role info --}}
            <div style="margin-top:2rem;padding:1rem;background:var(--color-forest-50);border-radius:10px;border:1px solid var(--color-forest-200);">
                <div style="font-size:0.72rem;font-weight:600;color:var(--color-forest-700);margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.05em;">Akses berdasarkan role</div>
                <div style="display:flex;flex-direction:column;gap:0.35rem;">
                    @foreach([
                        ['Pendaki', 'Booking SIMAKSI & QR Pass'],
                        ['Pengelola TN', 'Dashboard monitoring kawasan'],
                        ['Admin', 'Kelola seluruh sistem'],
                    ] as [$role, $desc])
                    <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.78rem;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg>
                        <span style="font-weight:600;color:var(--color-text);">{{ $role }}</span>
                        <span style="color:var(--color-text-muted);">&mdash; {{ $desc }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <p style="margin-top:2.5rem;font-size:0.72rem;color:var(--color-text-muted);text-align:center;">
            &copy; {{ date('Y') }} SummitPass &mdash; Platform Keselamatan Pendakian Indonesia
        </p>
    </div>

</body>
</html>
