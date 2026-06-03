<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — SummitPass</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100dvh;
            display: grid;
            grid-template-columns: 1fr;
            background: linear-gradient(135deg, #f8faf9 0%, #e8f5e9 100%);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        @media (min-width: 1024px) {
            body {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        .auth-illustration {
            background:
                linear-gradient(135deg,
                    rgba(13, 71, 38, 0.95) 0%,
                    rgba(20, 83, 45, 0.92) 35%,
                    rgba(27, 94, 32, 0.88) 70%,
                    rgba(46, 125, 50, 0.85) 100%
                ),
                url("https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&q=80") center/cover no-repeat;
            display: none;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        
        @media (min-width: 1024px) {
            .auth-illustration {
                display: flex;
            }
        }
        
        .auth-illustration::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 80%, rgba(76, 175, 80, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(129, 199, 132, 0.12) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .auth-illustration::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }
        
        .floating-badge {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .form-container {
            background: white;
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow:
                0 4px 6px -1px rgba(0, 0, 0, 0.05),
                0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        
        @media (max-width: 640px) {
            .form-container {
                background: transparent;
                box-shadow: none;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body x-data="{ showPass: false, selectedRole: null, demoEmail: '', demoPassword: '', loading: false }">

    {{-- Left: Illustration panel --}}
    <div class="auth-illustration">
        {{-- Logo --}}
        <a href="/" style="display:inline-flex;align-items:center;gap:0.75rem;text-decoration:none;color:white;position:relative;z-index:2;">
            <div style="width:40px;height:40px;background:rgba(255,255,255,0.15);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                <img src="{{ asset('logo.png') }}" alt="SummitPass" style="width:24px;height:24px;object-fit:contain;">
            </div>
            <span style="font-weight:700;font-size:1.25rem;letter-spacing:-0.02em;">SummitPass</span>
        </a>

        {{-- Center content --}}
        <div style="position:relative;z-index:2;">
            <div class="floating-badge" style="display:inline-flex;align-items:center;gap:0.625rem;background:rgba(255,255,255,0.1);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.15);border-radius:24px;padding:0.5rem 1.25rem;margin-bottom:2rem;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                <span style="width:8px;height:8px;border-radius:50%;background:#4caf50;display:inline-block;box-shadow:0 0 8px rgba(76,175,80,0.6);"></span>
                <span style="font-size:0.8rem;color:rgba(255,255,255,0.95);font-weight:600;letter-spacing:0.02em;">Platform Keselamatan Pendakian</span>
            </div>
            
            <h2 style="font-size:2.5rem;font-weight:800;color:#fff;line-height:1.15;letter-spacing:-0.03em;margin-bottom:1.25rem;text-shadow:0 2px 12px rgba(0,0,0,0.15);">
                Setiap Langkah<br>Tercatat Aman
            </h2>
            
            <p style="color:rgba(255,255,255,0.85);font-size:1rem;line-height:1.7;max-width:420px;text-shadow:0 1px 4px rgba(0,0,0,0.1);">
                Sistem monitoring real-time untuk keselamatan pendakian. Dari SIMAKSI digital hingga QR Pass di setiap checkpoint.
            </p>

            {{-- Stats --}}
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;margin-top:3rem;">
                @foreach([
                    ['15+', 'Gunung Terdaftar'],
                    ['10k+', 'Pendaki Aktif'],
                    ['100%', 'Real-time Track']
                ] as $stat)
                <div style="background:rgba(255,255,255,0.08);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.12);border-radius:16px;padding:1.25rem 1rem;text-align:center;">
                    <div style="font-size:1.75rem;font-weight:800;color:#fff;letter-spacing:-0.03em;margin-bottom:0.25rem;">{{ $stat[0] }}</div>
                    <div style="font-size:0.8rem;color:rgba(255,255,255,0.7);font-weight:500;">{{ $stat[1] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Testimonial --}}
        <div style="position:relative;z-index:2;background:rgba(255,255,255,0.08);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.12);border-radius:16px;padding:1.5rem;box-shadow:0 4px 16px rgba(0,0,0,0.1);">
            <div style="display:flex;gap:0.5rem;margin-bottom:1rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="rgba(255,255,255,0.3)">
                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                </svg>
            </div>
            <p style="font-size:0.95rem;color:rgba(255,255,255,0.9);line-height:1.65;margin-bottom:1.25rem;font-style:italic;">
                Dengan SummitPass, kami bisa memantau posisi pendaki secara real-time. Sistem ini sangat membantu dalam operasi SAR dan pencegahan kecelakaan.
            </p>
            <div style="display:flex;align-items:center;gap:0.875rem;">
                <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#4caf50,#66bb6a);display:flex;align-items:center;justify-content:center;font-size:0.875rem;font-weight:700;color:white;box-shadow:0 2px 8px rgba(76,175,80,0.3);">RH</div>
                <div>
                    <div style="font-size:0.875rem;font-weight:700;color:#fff;margin-bottom:0.125rem;">Rudi Hartono</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.6);font-weight:500;">Pengelola Taman Nasional Bromo</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Login form --}}
    <div style="display:flex;flex-direction:column;justify-content:center;align-items:center;padding:2rem 1.5rem;min-height:100dvh;">
        <div class="form-container" style="width:100%;max-width:440px;">

            {{-- Mobile logo --}}
            <a href="/" style="display:inline-flex;align-items:center;gap:0.625rem;text-decoration:none;color:var(--color-text);margin-bottom:2.5rem;" class="lg:hidden">
                <div style="width:36px;height:36px;background:linear-gradient(135deg,#1b5e20,#2e7d32);border-radius:10px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(27,94,32,0.2);">
                    <img src="{{ asset('logo.png') }}" alt="SummitPass" style="width:22px;height:22px;object-fit:contain;">
                </div>
                <span style="font-weight:700;font-size:1.125rem;letter-spacing:-0.02em;">SummitPass</span>
            </a>

            {{-- Heading --}}
            <div style="margin-bottom:2rem;">
                <h1 style="font-size:2rem;font-weight:800;letter-spacing:-0.03em;color:var(--color-text);margin-bottom:0.5rem;">Selamat Datang Kembali</h1>
                <p style="font-size:0.95rem;color:var(--color-text-muted);line-height:1.5;">Masuk ke akun Anda untuk melanjutkan perjalanan pendakian.</p>
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
            <form method="POST" action="{{ route('login') }}" @submit="loading = true">
                @csrf

                {{-- Email --}}
                <div style="margin-bottom:1.25rem;">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        x-model="demoEmail"
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
                            x-model="demoPassword"
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

            {{-- Register link --}}
            <p style="text-align:center;font-size:0.9rem;color:var(--color-text-muted);margin-top:1.75rem;">
                Belum punya akun?
                @if(Route::has('register'))
                <a href="{{ route('register') }}" style="color:#1b5e20;font-weight:700;text-decoration:none;margin-left:0.25rem;transition:color 0.2s;">
                    Daftar Sekarang →
                </a>
                @endif
            </p>

            {{-- Demo Family Tracking --}}
            <div style="margin-top:2rem;padding:1.25rem;background:#f0fdf4;border:2px solid #bbf7d0;border-radius:16px;">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.875rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span style="font-size:0.85rem;font-weight:700;color:#15803d;letter-spacing:0.01em;">Demo Family Tracking</span>
                </div>
                <p style="font-size:0.82rem;color:#166534;margin-bottom:1rem;line-height:1.5;">
                    Lihat bagaimana keluarga bisa memantau pendakian secara real-time tanpa perlu login:
                </p>
                <a
                    href="{{ route('public.family-tracking', 'demo-keluarga-001') }}"
                    target="_blank"
                    style="display:flex;align-items:center;justify-content:center;gap:0.625rem;background:#16a34a;color:white;border:none;border-radius:10px;padding:0.75rem 1.25rem;font-size:0.85rem;font-weight:600;text-decoration:none;transition:all 0.2s;box-shadow:0 2px 8px rgba(22,163,74,0.25);"
                    onmouseover="this.style.background='#15803d';this.style.transform='translateY(-1px)';this.style.boxShadow='0 4px 12px rgba(22,163,74,0.35)'"
                    onmouseout="this.style.background='#16a34a';this.style.transform='translateY(0)';this.style.boxShadow='0 2px 8px rgba(22,163,74,0.25)'"
                >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Buka Halaman Family Tracking Demo
                </a>
                <div style="margin-top:0.75rem;font-size:0.72rem;color:#166534;text-align:center;">
                    💡 Halaman ini bisa diakses siapa saja tanpa login
                </div>
            </div>

            {{-- Demo credentials --}}
            <div style="margin-top:1.5rem;padding:1.5rem;background:#fef3c7;border:2px solid #fde047;border-radius:16px;">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1rem;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span style="font-size:0.72rem;font-weight:700;color:#92400e;letter-spacing:0.04em;text-transform:uppercase;">Akun Demo Tersedia</span>
                </div>
                
                <p style="font-size:0.85rem;color:#78716c;margin-bottom:1.25rem;line-height:1.5;">
                    Klik role untuk mengisi form login otomatis:
                </p>
                
                {{-- Pills Buttons --}}
                <div style="display:flex;flex-wrap:wrap;gap:0.625rem;">
                    {{-- Pendaki Button --}}
                    <button
                        @click="selectedRole = 'pendaki'; demoEmail = 'budi@example.com'; demoPassword = 'pendaki123';"
                        type="button"
                        style="position:relative;overflow:hidden;transition:all 0.2s;cursor:pointer;"
                        :style="selectedRole === 'pendaki' ?
                            'background:linear-gradient(135deg,#16a34a,#22c55e);color:white;border:none;box-shadow:0 4px 12px rgba(22,163,74,0.3);transform:translateY(-2px);' :
                            'background:white;color:#16a34a;border:2px solid #bbf7d0;box-shadow:0 2px 6px rgba(0,0,0,0.05);'"
                    >
                        <div style="position:relative;z-index:1;padding:0.625rem 1.25rem;display:flex;align-items:center;gap:0.625rem;">
                            <div :style="selectedRole === 'pendaki' ?
                                'width:28px;height:28px;background:rgba(255,255,255,0.25);backdrop-filter:blur(10px);border-radius:50%;display:flex;align-items:center;justify-content:center;' :
                                'width:28px;height:28px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;'">
                                <span style="font-size:0.75rem;font-weight:800;" :style="selectedRole === 'pendaki' ? 'color:white;' : 'color:#16a34a;'">P</span>
                            </div>
                            <span style="font-size:0.875rem;font-weight:700;letter-spacing:0.01em;">Pendaki</span>
                        </div>
                    </button>
                    
                    {{-- Pengelola Button --}}
                    <button
                        @click="selectedRole = 'pengelola'; demoEmail = 'pengelola@tngr.id'; demoPassword = 'pengelola123';"
                        type="button"
                        style="position:relative;overflow:hidden;transition:all 0.2s;cursor:pointer;"
                        :style="selectedRole === 'pengelola' ?
                            'background:linear-gradient(135deg,#f59e0b,#fbbf24);color:white;border:none;box-shadow:0 4px 12px rgba(245,158,11,0.3);transform:translateY(-2px);' :
                            'background:white;color:#f59e0b;border:2px solid #fde047;box-shadow:0 2px 6px rgba(0,0,0,0.05);'"
                    >
                        <div style="position:relative;z-index:1;padding:0.625rem 1.25rem;display:flex;align-items:center;gap:0.625rem;">
                            <div :style="selectedRole === 'pengelola' ?
                                'width:28px;height:28px;background:rgba(255,255,255,0.25);backdrop-filter:blur(10px);border-radius:50%;display:flex;align-items:center;justify-content:center;' :
                                'width:28px;height:28px;background:#fef3c7;border-radius:50%;display:flex;align-items:center;justify-content:center;'">
                                <span style="font-size:0.7rem;font-weight:800;" :style="selectedRole === 'pengelola' ? 'color:white;' : 'color:#f59e0b;'">TN</span>
                            </div>
                            <span style="font-size:0.875rem;font-weight:700;letter-spacing:0.01em;">Pengelola</span>
                        </div>
                    </button>
                    
                    {{-- Admin Button --}}
                    <button
                        @click="selectedRole = 'admin'; demoEmail = 'admin@summitpass.id'; demoPassword = 'admin123456';"
                        type="button"
                        style="position:relative;overflow:hidden;transition:all 0.2s;cursor:pointer;"
                        :style="selectedRole === 'admin' ?
                            'background:linear-gradient(135deg,#1c1917,#44403c);color:white;border:none;box-shadow:0 4px 12px rgba(28,25,23,0.3);transform:translateY(-2px);' :
                            'background:white;color:#1c1917;border:2px solid #e5e5e5;box-shadow:0 2px 6px rgba(0,0,0,0.05);'"
                    >
                        <div style="position:relative;z-index:1;padding:0.625rem 1.25rem;display:flex;align-items:center;gap:0.625rem;">
                            <div :style="selectedRole === 'admin' ?
                                'width:28px;height:28px;background:rgba(255,255,255,0.25);backdrop-filter:blur(10px);border-radius:50%;display:flex;align-items:center;justify-content:center;' :
                                'width:28px;height:28px;background:#f5f5f4;border-radius:50%;display:flex;align-items:center;justify-content:center;'">
                                <span style="font-size:0.75rem;font-weight:800;" :style="selectedRole === 'admin' ? 'color:white;' : 'color:#1c1917;'">A</span>
                            </div>
                            <span style="font-size:0.875rem;font-weight:700;letter-spacing:0.01em;">Admin</span>
                        </div>
                    </button>
                </div>
                
                {{-- Success message --}}
                <div
                    x-show="selectedRole !== null"
                    x-transition
                    style="margin-top:1rem;padding:0.875rem;background:white;border-radius:10px;display:flex;align-items:center;gap:0.625rem;border:1px solid #e5e5e5;"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span style="font-size:0.8rem;color:#14532d;font-weight:600;">
                        Kredensial <span x-text="selectedRole === 'pendaki' ? 'Pendaki' : selectedRole === 'pengelola' ? 'Pengelola' : 'Admin'"></span> berhasil diisi ke form
                    </span>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <p style="margin-top:3rem;font-size:0.75rem;color:var(--color-text-muted);text-align:center;font-weight:500;">
            &copy; {{ date('Y') }} SummitPass &mdash; Platform Keselamatan Pendakian Indonesia
        </p>
    </div>

</body>
</html>
