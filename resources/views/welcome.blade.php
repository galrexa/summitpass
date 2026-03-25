<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SummitPass — Platform Keselamatan Pendakian Indonesia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .hero-bg {
            background: linear-gradient(160deg, var(--color-forest-900) 0%, var(--color-lake-800) 50%, var(--color-forest-800) 100%);
            position: relative;
            overflow: hidden;
        }
        .hero-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .nav-link { color: rgba(255,255,255,0.8); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.15s; }
        .nav-link:hover { color: #fff; }
        .feature-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 16px;
            padding: 1.75rem;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .feature-card:hover {
            box-shadow: 0 8px 32px rgba(45,106,79,0.12);
            transform: translateY(-2px);
        }
        .flow-step { display: flex; align-items: flex-start; gap: 1.25rem; }
        .flow-step-num {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--color-forest-700); color: #fff;
            font-size: 0.85rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
    </style>
</head>
<body style="background:#fff;">

    {{-- Navbar --}}
    <nav style="position:sticky;top:0;z-index:50;background:var(--color-forest-900);border-bottom:1px solid rgba(255,255,255,0.08);">
        <div style="max-width:1200px;margin:0 auto;padding:0 1.5rem;height:60px;display:flex;align-items:center;gap:2rem;">
            <a href="/" style="display:flex;align-items:center;gap:0.625rem;text-decoration:none;color:white;font-weight:700;font-size:1.05rem;">
                <div style="width:32px;height:32px;background:var(--color-forest-600);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 20l5-9 4 6 3-4 6 7H3z"/>
                    </svg>
                </div>
                SummitPass
            </a>
            <div style="flex:1;"></div>
            <div style="display:flex;align-items:center;gap:1.5rem;">
                <a href="#fitur" class="nav-link" style="display:none;" id="nav-fitur">Fitur</a>
                <a href="#alur" class="nav-link" style="display:none;" id="nav-alur">Cara Kerja</a>
            </div>
            <div style="display:flex;align-items:center;gap:0.75rem;">
                @if(Route::has('login'))
                    @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
                    @else
                    <a href="{{ route('login') }}" class="nav-link" style="color:rgba(255,255,255,0.7);">Masuk</a>
                    @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
                    @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="hero-bg" style="padding:5rem 1.5rem 6rem;">
        <div style="max-width:900px;margin:0 auto;text-align:center;position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:0.5rem;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);border-radius:20px;padding:0.35rem 1rem;margin-bottom:2rem;">
                <span style="width:8px;height:8px;border-radius:50%;background:var(--color-forest-400);display:inline-block;"></span>
                <span style="font-size:0.78rem;color:rgba(255,255,255,0.75);font-weight:500;">Platform Keselamatan Pendakian Indonesia</span>
            </div>
            <h1 style="font-size:clamp(2.25rem, 5vw, 3.75rem);font-weight:800;color:#fff;line-height:1.1;letter-spacing:-0.03em;margin-bottom:1.5rem;">
                Digitalisasi Keselamatan<br>
                <span style="background:linear-gradient(90deg,var(--color-forest-400),var(--color-lake-300));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
                    Pendakian Gunung
                </span>
            </h1>
            <p style="font-size:1.1rem;color:rgba(255,255,255,0.7);max-width:600px;margin:0 auto 2.5rem;line-height:1.7;">
                SummitPass mencatat perjalanan pendaki di setiap pos — dari gerbang masuk hingga keluar.
                SIMAKSI digital, QR Pass pribadi, dan monitoring keselamatan real-time.
            </p>
            <div style="display:flex;align-items:center;justify-content:center;gap:1rem;flex-wrap:wrap;">
                @if(Route::has('register'))
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                    Mulai Pendakian
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/>
                    </svg>
                </a>
                @endif
                <a href="#alur" class="btn btn-outline btn-lg" style="color:rgba(255,255,255,0.85);border-color:rgba(255,255,255,0.25);background:rgba(255,255,255,0.05);">
                    Cara Kerja
                </a>
            </div>

            {{-- Quick stats --}}
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;max-width:480px;margin:3.5rem auto 0;text-align:center;">
                <div>
                    <div style="font-size:1.75rem;font-weight:800;color:#fff;letter-spacing:-0.03em;">15+</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.5);margin-top:0.25rem;">Gunung Terdaftar</div>
                </div>
                <div style="border-left:1px solid rgba(255,255,255,0.1);border-right:1px solid rgba(255,255,255,0.1);">
                    <div style="font-size:1.75rem;font-weight:800;color:#fff;letter-spacing:-0.03em;">10k+</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.5);margin-top:0.25rem;">Pendaki Terdaftar</div>
                </div>
                <div>
                    <div style="font-size:1.75rem;font-weight:800;color:#fff;letter-spacing:-0.03em;">Real-time</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.5);margin-top:0.25rem;">Monitoring SAR</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Fitur utama --}}
    <section id="fitur" style="padding:5rem 1.5rem;background:var(--color-surface-alt);">
        <div style="max-width:1100px;margin:0 auto;">
            <div style="text-align:center;margin-bottom:3rem;">
                <h2 style="font-size:clamp(1.5rem,3vw,2.25rem);font-weight:800;letter-spacing:-0.025em;color:var(--color-text);">Dua Modul Inti</h2>
                <p style="color:var(--color-text-muted);margin-top:0.75rem;font-size:1rem;max-width:500px;margin-left:auto;margin-right:auto;">
                    Perizinan digital yang menegakkan regulasi otomatis, dilengkapi sistem log keselamatan di setiap pos.
                </p>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.5rem;">

                <div class="feature-card">
                    <div style="width:48px;height:48px;border-radius:12px;background:var(--color-forest-100);display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                    </div>
                    <h3 style="font-size:1.1rem;font-weight:700;color:var(--color-text);margin-bottom:0.625rem;">SIMAKSI Digital</h3>
                    <p style="font-size:0.875rem;color:var(--color-text-muted);line-height:1.65;">
                        Urus izin pendakian online. Sistem menegakkan kuota, batas hari, dan aturan guide secara otomatis sesuai regulasi tiap gunung.
                    </p>
                    <ul style="margin-top:1rem;display:flex;flex-direction:column;gap:0.5rem;">
                        @foreach(['Kuota real-time per jalur', 'Validasi NIK peserta', 'Pembayaran via gateway', 'Kode booking unik'] as $f)
                        <li style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;color:var(--color-text-muted);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg>
                            {{ $f }}
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="feature-card">
                    <div style="width:48px;height:48px;border-radius:12px;background:var(--color-lake-100);display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-lake-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="3" height="3" rx="0.5"/>
                        </svg>
                    </div>
                    <h3 style="font-size:1.1rem;font-weight:700;color:var(--color-text);margin-bottom:0.625rem;">QR SummitPass</h3>
                    <p style="font-size:0.875rem;color:var(--color-text-muted);line-height:1.65;">
                        Setiap peserta mendapat QR pribadi terikat NIK. Scan di setiap pos — naik dan turun — menciptakan rekam jejak lengkap perjalanan.
                    </p>
                    <ul style="margin-top:1rem;display:flex;flex-direction:column;gap:0.5rem;">
                        @foreach(['QR unik per pendaki', 'Log setiap checkpoint', 'Alert otomatis bila terlambat', 'Data valid untuk SAR'] as $f)
                        <li style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;color:var(--color-text-muted);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-lake-600)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg>
                            {{ $f }}
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="feature-card">
                    <div style="width:48px;height:48px;border-radius:12px;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                    </div>
                    <h3 style="font-size:1.1rem;font-weight:700;color:var(--color-text);margin-bottom:0.625rem;">Anomaly Alert</h3>
                    <p style="font-size:0.875rem;color:var(--color-text-muted);line-height:1.65;">
                        Sistem cek otomatis setiap 30 menit. Jika pendaki melewati batas waktu tanpa checkout, notifikasi langsung dikirim ke pengelola.
                    </p>
                    <ul style="margin-top:1rem;display:flex;flex-direction:column;gap:0.5rem;">
                        @foreach(['Deteksi tidak checkout', 'Notifikasi WhatsApp/Email', 'Dashboard SAR', 'Riwayat posisi terakhir'] as $f)
                        <li style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;color:var(--color-text-muted);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg>
                            {{ $f }}
                        </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    </section>

    {{-- Cara kerja / Alur --}}
    <section id="alur" style="padding:5rem 1.5rem;background:#fff;">
        <div style="max-width:1100px;margin:0 auto;">
            <div style="text-align:center;margin-bottom:3rem;">
                <h2 style="font-size:clamp(1.5rem,3vw,2.25rem);font-weight:800;letter-spacing:-0.025em;color:var(--color-text);">Alur Pendakian</h2>
                <p style="color:var(--color-text-muted);margin-top:0.75rem;font-size:1rem;">Dari registrasi hingga checkout — semua tercatat.</p>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:3rem;">

                <div>
                    <div style="display:inline-flex;align-items:center;gap:0.5rem;background:var(--color-forest-100);border-radius:8px;padding:0.4rem 0.875rem;margin-bottom:1.5rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        <span style="font-size:0.78rem;font-weight:600;color:var(--color-forest-700);">Leader / Koordinator</span>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:1.25rem;">
                        @foreach(['Pilih gunung & lihat regulasi', 'Tentukan tanggal & jalur pendakian', 'Input NIK seluruh peserta', 'Bayar via payment gateway', 'Bagikan Kode Booking ke peserta'] as $i => $step)
                        <div class="flow-step">
                            <div class="flow-step-num">{{ $i+1 }}</div>
                            <div style="font-size:0.875rem;font-weight:500;color:var(--color-text);line-height:1.5;padding-top:0.5rem;">{{ $step }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <div style="display:inline-flex;align-items:center;gap:0.5rem;background:var(--color-lake-100);border-radius:8px;padding:0.4rem 0.875rem;margin-bottom:1.5rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-lake-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="7" r="4"/><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/></svg>
                        <span style="font-size:0.78rem;font-weight:600;color:var(--color-lake-700);">Peserta (semua anggota)</span>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:1.25rem;">
                        @foreach(['Login ke akun SummitPass', 'Input Kode Booking dari leader', 'Dapatkan QR SummitPass pribadi', 'Scan QR di setiap pos jalur', 'Checkout di gerbang keluar'] as $i => $step)
                        <div class="flow-step">
                            <div class="flow-step-num" style="background:var(--color-lake-600);">{{ $i+1 }}</div>
                            <div style="font-size:0.875rem;font-weight:500;color:var(--color-text);line-height:1.5;padding-top:0.5rem;">{{ $step }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section style="padding:5rem 1.5rem;background:linear-gradient(135deg,var(--color-forest-900),var(--color-lake-800));">
        <div style="max-width:600px;margin:0 auto;text-align:center;">
            <h2 style="font-size:clamp(1.5rem,3vw,2.25rem);font-weight:800;color:#fff;letter-spacing:-0.025em;margin-bottom:1rem;">
                Siap mendaki dengan aman?
            </h2>
            <p style="color:rgba(255,255,255,0.65);margin-bottom:2.5rem;font-size:1rem;line-height:1.7;">
                Daftarkan diri sekarang dan dapatkan QR SummitPass untuk pendakian pertama Anda.
            </p>
            @if(Route::has('register'))
            <a href="{{ route('register') }}" class="btn btn-lg" style="background:white;color:var(--color-forest-800);font-weight:700;">
                Daftar Sekarang — Gratis
            </a>
            @endif
        </div>
    </section>

    {{-- Footer --}}
    <footer style="background:var(--color-forest-950);padding:2rem 1.5rem;">
        <div style="max-width:1100px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div style="display:flex;align-items:center;gap:0.5rem;color:rgba(255,255,255,0.5);font-size:0.8rem;">
                <div style="width:24px;height:24px;background:var(--color-forest-700);border-radius:6px;display:flex;align-items:center;justify-content:center;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20l5-9 4 6 3-4 6 7H3z"/></svg>
                </div>
                SummitPass &copy; {{ date('Y') }}
            </div>
            <div style="font-size:0.75rem;color:rgba(255,255,255,0.3);">
                Platform Keselamatan Pendakian Indonesia
            </div>
        </div>
    </footer>

</body>
</html>
