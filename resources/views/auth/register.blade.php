<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar — SummitPass</title>
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
            background:
                linear-gradient(160deg, rgba(5,46,22,0.72) 0%, rgba(12,74,110,0.55) 60%, rgba(20,83,45,0.65) 100%),
                url("https://bromotenggersemeru.id/asset/template/assets/img/1920x800/img1.jpg") center/cover no-repeat;
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
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            backdrop-filter: blur(1px);
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
<body>

    {{-- Left: Illustration panel --}}
    <div class="auth-illustration">
        <a href="/" style="display:inline-flex;align-items:center;gap:0.625rem;text-decoration:none;color:white;">
            <div style="width:36px;height:36px;background:var(--color-forest-600);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <img src="{{ asset('logo.png') }}" alt="SummitPass" style="width:100%;height:100%;object-fit:contain;">
            </div>
            <span style="font-weight:700;font-size:1.1rem;">SummitPass</span>
        </a>

        <div style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:0.5rem;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);border-radius:20px;padding:0.35rem 1rem;margin-bottom:1.5rem;">
                <span style="width:7px;height:7px;border-radius:50%;background:var(--color-forest-400);display:inline-block;"></span>
                <span style="font-size:0.75rem;color:rgba(255,255,255,0.7);font-weight:500;">Platform Keselamatan Pendakian Indonesia</span>
            </div>
            <h2 style="font-size:2rem;font-weight:800;color:#fff;line-height:1.2;letter-spacing:-0.025em;margin-bottom:1rem;">
                Mulai perjalananmu<br>dengan aman.
            </h2>
            <p style="color:rgba(255,255,255,0.6);font-size:0.95rem;line-height:1.7;max-width:380px;">
                Daftar sekarang dan dapatkan akses ke SIMAKSI digital, QR SummitPass, dan monitoring real-time untuk keselamatan pendakianmu.
            </p>
        </div>

        <div style="position:relative;z-index:1;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:1.25rem 1.5rem;">
            <p style="font-size:0.875rem;color:rgba(255,255,255,0.8);line-height:1.6;font-style:italic;margin-bottom:0.875rem;">
                "Registrasi mudah dan cepat. Semua data pendaki tersimpan aman sehingga proses check-in di pos lebih lancar."
            </p>
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <div class="avatar" style="width:32px;height:32px;font-size:0.7rem;background:var(--color-forest-600);color:white;">SA</div>
                <div>
                    <div style="font-size:0.8rem;font-weight:600;color:#fff;">Siti Aminah</div>
                    <div style="font-size:0.72rem;color:rgba(255,255,255,0.45);">Pendaki, Komunitas Anak Gunung</div>
                </div>
            </div>
        </div>

        <svg class="mountain-silhouette" viewBox="0 0 1200 300" fill="white" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0 300 L0 200 L150 120 L250 180 L400 60 L550 160 L650 40 L800 140 L900 80 L1050 160 L1200 100 L1200 300 Z"/>
        </svg>
    </div>

    {{-- Right: Prototype notice --}}
    <div style="display:flex;flex-direction:column;justify-content:center;align-items:center;padding:2rem 1.5rem;min-height:100dvh;">
        <div style="width:100%;max-width:440px;">

            {{-- Mobile logo --}}
            <a href="/" style="display:inline-flex;align-items:center;gap:0.5rem;text-decoration:none;color:var(--color-text);margin-bottom:2rem;" class="lg:hidden">
                <div style="width:30px;height:30px;background:var(--color-forest-700);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <img src="{{ asset('logo.png') }}" alt="SummitPass" style="width:100%;height:100%;object-fit:contain;">
                </div>
                <span style="font-weight:700;font-size:1rem;">SummitPass</span>
            </a>

            {{-- Prototype badge --}}
            <div style="display:inline-flex;align-items:center;gap:0.5rem;background:#fef9c3;border:1px solid #fde047;border-radius:20px;padding:0.3rem 0.875rem;margin-bottom:1.75rem;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <span style="font-size:0.72rem;font-weight:700;color:#92400e;letter-spacing:0.04em;text-transform:uppercase;">Mode Prototipe</span>
            </div>

            <h1 style="font-size:1.75rem;font-weight:800;letter-spacing:-0.025em;color:var(--color-text);margin-bottom:0.75rem;">Pendaftaran ditutup</h1>
            <p style="font-size:0.925rem;color:var(--color-text-muted);line-height:1.7;margin-bottom:2rem;">
                SummitPass saat ini berjalan sebagai <strong style="color:var(--color-text);">prototipe demo</strong>. Pendaftaran akun baru belum dibuka untuk umum.
            </p>

            {{-- Info box --}}
            <div style="background:var(--color-forest-50);border:1px solid var(--color-forest-200);border-radius:12px;padding:1.25rem 1.5rem;margin-bottom:1.75rem;">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span style="font-size:0.78rem;font-weight:700;color:var(--color-forest-700);text-transform:uppercase;letter-spacing:0.04em;">Gunakan akun demo</span>
                </div>
                <p style="font-size:0.825rem;color:var(--color-text-muted);line-height:1.6;margin-bottom:1rem;">
                    Untuk menjelajahi fitur SummitPass, gunakan salah satu akun demo berikut di halaman <strong style="color:var(--color-text);">Login</strong>.
                </p>

                {{-- Pendaki --}}
                <div style="background:#fff;border:1px solid var(--color-border);border-radius:8px;padding:0.875rem 1rem;margin-bottom:0.625rem;">
                    <div style="font-size:0.7rem;font-weight:700;color:var(--color-forest-600);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Pendaki</div>
                    <div style="display:flex;flex-direction:column;gap:0.25rem;">
                        <div style="font-size:0.8rem;color:var(--color-text-muted);">Email: <span style="font-weight:600;color:var(--color-text);font-family:monospace;">budi@example.com</span></div>
                        <div style="font-size:0.8rem;color:var(--color-text-muted);">Password: <span style="font-weight:600;color:var(--color-text);font-family:monospace;">pendaki123</span></div>
                    </div>
                </div>

                {{-- Pengelola --}}
                <div style="background:#fff;border:1px solid var(--color-border);border-radius:8px;padding:0.875rem 1rem;">
                    <div style="font-size:0.7rem;font-weight:700;color:var(--color-forest-600);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Pengelola Taman Nasional</div>
                    <div style="display:flex;flex-direction:column;gap:0.25rem;">
                        <div style="font-size:0.8rem;color:var(--color-text-muted);">Email: <span style="font-weight:600;color:var(--color-text);font-family:monospace;">pengelola@mail.com</span></div>
                        <div style="font-size:0.8rem;color:var(--color-text-muted);">Password: <span style="font-weight:600;color:var(--color-text);font-family:monospace;">pengelola123</span></div>
                    </div>
                </div>
            </div>

            <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-full" style="display:flex;align-items:center;justify-content:center;gap:0.5rem;text-decoration:none;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                Masuk dengan akun demo
            </a>

            <p style="text-align:center;font-size:0.8rem;color:var(--color-text-muted);margin-top:1rem;">
                Kembali ke <a href="{{ url('/') }}" style="color:var(--color-forest-700);font-weight:600;text-decoration:none;">halaman utama</a>
            </p>

        </div>

        <p style="margin-top:2.5rem;font-size:0.72rem;color:var(--color-text-muted);text-align:center;">
            &copy; {{ date('Y') }} SummitPass &mdash; Platform Keselamatan Pendakian Indonesia
        </p>
    </div>

</body>
</html>
