<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SummitPass — Digitalisasi Keselamatan Pendakian</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --green:    #16a34a;
            --green-d:  #14532d;
            --green-l:  #dcfce7;
            --green-m:  #bbf7d0;
            --amber:    #f59e0b;
            --amber-l:  #fef3c7;
            --stone:    #78716c;
            --stone-l:  #f5f5f4;
            --stone-xl: #fafaf9;
            --ink:      #1c1917;
            --ink-2:    #292524;
            --muted:    #a8a29e;
            --white:    #ffffff;
            --r-sm: 8px;
            --r-md: 14px;
            --r-lg: 22px;
            --r-xl: 32px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Sora', sans-serif;
            background: var(--stone-xl);
            color: var(--ink);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Noise texture */
        body::before {
            content: '';
            position: fixed; inset: 0; z-index: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            background-size: 200px 200px;
            pointer-events: none;
            opacity: .4;
        }

        /* NAV */
        nav {
            position: sticky; top: 0; z-index: 100;
            background: rgba(250,250,249,.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(120,113,108,.1);
            padding: 0 1.5rem;
            height: 60px;
            display: flex; align-items: center; gap: 1.5rem;
        }
        .nav-brand {
            display: flex; align-items: center; gap: .6rem;
            text-decoration: none; color: var(--ink); font-weight: 700; font-size: .95rem;
        }
        .nav-logo {
            width: 34px; height: 34px;
            background: var(--green);
            border-radius: var(--r-sm);
            display: flex; align-items: center; justify-content: center;
        }
        .nav-logo svg { width: 18px; height: 18px; }
        .nav-spacer { flex: 1; }
        .nav-links { display: flex; align-items: center; gap: 1.75rem; }
        .nav-link { font-size: .82rem; color: var(--stone); text-decoration: none; font-weight: 500; transition: color .15s; }
        .nav-link:hover { color: var(--ink); }
        .btn-nav-login {
            font-size: .82rem; font-weight: 600; color: var(--green-d);
            background: none; border: none; cursor: pointer;
            font-family: inherit; text-decoration: none;
        }
        .btn-nav-cta {
            background: var(--green); color: #fff;
            border: none; border-radius: var(--r-sm);
            padding: .48rem 1.1rem;
            font-size: .82rem; font-weight: 600;
            cursor: pointer; font-family: inherit;
            text-decoration: none;
            transition: background .15s, transform .1s;
            display: inline-block;
        }
        .btn-nav-cta:hover { background: #15803d; transform: translateY(-1px); }

        /* HERO */
        .hero {
            position: relative;
            min-height: 92vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            overflow: hidden;
        }
        .hero-left {
            display: flex; flex-direction: column; justify-content: center;
            padding: 6rem 4rem 6rem 5rem;
            position: relative; z-index: 2;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: .5rem;
            background: var(--green-l); border: 1px solid var(--green-m);
            border-radius: 20px; padding: .3rem .9rem;
            font-size: .72rem; font-weight: 600; color: #15803d;
            margin-bottom: 1.75rem; width: fit-content;
        }
        .hero-badge-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--green); animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%,100% { opacity: 1; transform: scale(1); }
            50% { opacity: .5; transform: scale(.85); }
        }
        .hero-headline {
            font-family: 'Instrument Serif', serif;
            font-size: clamp(2.6rem, 4.5vw, 4.2rem);
            line-height: 1.08;
            color: var(--ink);
            margin-bottom: 1.5rem;
            letter-spacing: -.025em;
        }
        .hero-headline em { color: var(--green); font-style: italic; }
        .hero-sub {
            font-size: 1rem; color: var(--stone); line-height: 1.75;
            max-width: 420px; margin-bottom: 2.25rem; font-weight: 400;
        }
        .hero-ctas { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
        .btn-primary {
            background: var(--green); color: #fff;
            border: none; border-radius: var(--r-md);
            padding: .8rem 1.75rem;
            font-size: .9rem; font-weight: 600;
            cursor: pointer; font-family: inherit;
            transition: background .15s, transform .1s, box-shadow .15s;
            display: inline-flex; align-items: center; gap: .5rem;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(22,163,74,.25);
        }
        .btn-primary:hover { background: #15803d; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(22,163,74,.3); }
        .btn-ghost {
            background: none; border: 1.5px solid rgba(120,113,108,.3);
            border-radius: var(--r-md); padding: .78rem 1.5rem;
            font-size: .9rem; font-weight: 500; color: var(--ink);
            cursor: pointer; font-family: inherit; text-decoration: none;
            transition: border-color .15s, background .15s;
            display: inline-flex; align-items: center; gap: .5rem;
        }
        .btn-ghost:hover { border-color: var(--green); background: var(--green-l); }
        .hero-stats {
            margin-top: 3rem;
            display: flex; gap: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(120,113,108,.15);
        }
        .stat-val { font-size: 1.8rem; font-weight: 800; color: var(--ink); letter-spacing: -.04em; line-height: 1; }
        .stat-lbl { font-size: .72rem; color: var(--muted); font-weight: 500; margin-top: .25rem; }

        /* Hero right */
        .hero-right {
            position: relative; overflow: hidden;
            background: linear-gradient(160deg, #14532d 0%, #166534 40%, #1a7a40 70%, #1d9a52 100%);
        }
        .hero-right::before {
            content: '';
            position: absolute; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='600' height='600'%3E%3Ccircle cx='300' cy='300' r='260' fill='none' stroke='rgba(255,255,255,0.04)' stroke-width='40'/%3E%3Ccircle cx='300' cy='300' r='200' fill='none' stroke='rgba(255,255,255,0.05)' stroke-width='30'/%3E%3Ccircle cx='300' cy='300' r='140' fill='none' stroke='rgba(255,255,255,0.06)' stroke-width='25'/%3E%3Ccircle cx='300' cy='300' r='85' fill='none' stroke='rgba(255,255,255,0.07)' stroke-width='20'/%3E%3Ccircle cx='300' cy='300' r='40' fill='none' stroke='rgba(255,255,255,0.1)' stroke-width='15'/%3E%3C/svg%3E");
            background-size: cover; background-position: center;
        }
        .hero-phone-wrap {
            position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .phone {
            width: 240px;
            background: var(--white);
            border-radius: 36px;
            box-shadow: 0 40px 80px rgba(0,0,0,.35), 0 0 0 1px rgba(255,255,255,.1);
            overflow: hidden;
            position: relative;
            animation: float 4s ease-in-out infinite;
        }
        @keyframes float {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        .phone-notch {
            width: 90px; height: 26px;
            background: var(--ink);
            border-radius: 0 0 18px 18px;
            margin: 0 auto;
            position: relative; z-index: 2;
        }
        .phone-screen { background: #f9fafb; min-height: 440px; padding: 0; position: relative; }
        .phone-topbar {
            background: var(--green-d);
            padding: .75rem 1rem .6rem;
            display: flex; align-items: center; justify-content: space-between;
        }
        .phone-brand { font-size: .7rem; font-weight: 700; color: #fff; }
        .phone-avatar {
            width: 24px; height: 24px; border-radius: 50%;
            background: rgba(255,255,255,.2);
            display: flex; align-items: center; justify-content: center;
            font-size: .5rem; color: #fff; font-weight: 700;
        }
        .phone-section { padding: .75rem; }
        .phone-qr-card {
            background: #fff; border-radius: 14px; padding: .875rem;
            text-align: center; box-shadow: 0 2px 12px rgba(0,0,0,.06); margin-bottom: .6rem;
        }
        .phone-qr-user { font-size: .6rem; font-weight: 700; color: var(--ink); margin-bottom: .15rem; }
        .phone-qr-nik { font-size: .5rem; color: var(--muted); margin-bottom: .5rem; font-family: monospace; }
        .phone-qr-box {
            width: 88px; height: 88px; margin: 0 auto .5rem;
            background: var(--ink); border-radius: 8px;
            display: grid; grid-template-columns: repeat(9,1fr); grid-template-rows: repeat(9,1fr);
            gap: 1.5px; padding: 6px;
        }
        .qr-cell { border-radius: 1px; background: #fff; }
        .qr-cell.b { background: var(--ink); }
        .phone-qr-trip { font-size: .5rem; color: var(--stone); }
        .phone-qr-trip strong { color: var(--green-d); }
        .phone-pos-label { font-size: .55rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; margin-bottom: .4rem; }
        .phone-pos-item {
            background: #fff; border-radius: 8px; padding: .45rem .6rem;
            display: flex; align-items: center; gap: .5rem; margin-bottom: .3rem;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
        }
        .pos-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .pos-name { font-size: .56rem; font-weight: 600; color: var(--ink); }
        .pos-time { font-size: .5rem; color: var(--muted); margin-left: auto; }
        .phone-sos {
            background: #fee2e2; border-radius: 8px; padding: .45rem .7rem;
            display: flex; align-items: center; gap: .5rem; margin-top: .5rem;
        }
        .sos-icon { font-size: .65rem; }
        .sos-text { font-size: .54rem; font-weight: 700; color: #991b1b; }

        /* MARQUEE */
        .marquee-strip { background: var(--green); overflow: hidden; padding: .7rem 0; position: relative; }
        .marquee-track {
            display: flex; gap: 0;
            animation: marquee 28s linear infinite;
            width: max-content;
        }
        @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        .marquee-item {
            display: flex; align-items: center; gap: .75rem;
            padding: 0 2rem; white-space: nowrap;
            font-size: .72rem; font-weight: 600; color: rgba(255,255,255,.85);
            text-transform: uppercase; letter-spacing: .08em;
        }
        .marquee-sep { width: 5px; height: 5px; border-radius: 50%; background: rgba(255,255,255,.4); }

        /* SECTION */
        .section-tag {
            display: inline-flex; align-items: center; gap: .4rem;
            background: var(--green-l); border: 1px solid var(--green-m);
            border-radius: 20px; padding: .25rem .8rem;
            font-size: .7rem; font-weight: 700; color: #15803d;
            text-transform: uppercase; letter-spacing: .07em; margin-bottom: 1rem;
        }
        .section-title {
            font-family: 'Instrument Serif', serif;
            font-size: clamp(1.9rem, 3vw, 3rem);
            line-height: 1.15; letter-spacing: -.02em;
            color: var(--ink); margin-bottom: 1rem;
        }
        .section-sub { font-size: .95rem; color: var(--stone); line-height: 1.75; max-width: 520px; }

        /* HOW IT WORKS */
        .how-header { display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center; margin-bottom: 4rem; }
        .steps-timeline { position: relative; }
        .steps-timeline::before {
            content: ''; position: absolute;
            left: 22px; top: 50px; bottom: 50px; width: 2px;
            background: linear-gradient(to bottom, var(--green), var(--green-m), transparent);
        }
        .step-item {
            display: flex; gap: 1.25rem; align-items: flex-start;
            padding: 1.25rem 0; position: relative;
            cursor: pointer; transition: transform .15s;
        }
        .step-item:hover { transform: translateX(4px); }
        .step-num-circle {
            width: 44px; height: 44px; border-radius: 50%;
            background: var(--white); border: 2px solid var(--green);
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem; font-weight: 800; color: var(--green);
            flex-shrink: 0; position: relative; z-index: 1;
            box-shadow: 0 0 0 6px var(--stone-xl);
        }
        .step-num-circle.active { background: var(--green); color: #fff; }
        .step-content { padding-top: .2rem; }
        .step-lbl { font-size: .95rem; font-weight: 700; color: var(--ink); margin-bottom: .25rem; }
        .step-desc { font-size: .82rem; color: var(--stone); line-height: 1.6; }
        .step-warn-badge {
            display: inline-block; font-size: .65rem; font-weight: 700;
            background: var(--amber-l); color: #92400e;
            border-radius: 4px; padding: .15rem .5rem; margin-top: .3rem;
        }

        /* FEATURES */
        .features-bg {
            background: var(--ink); padding: 6rem 2rem;
            position: relative; overflow: hidden;
        }
        .features-bg::before {
            content: '';
            position: absolute; top: -100px; right: -100px;
            width: 500px; height: 500px; border-radius: 50%;
            background: radial-gradient(circle, rgba(22,163,74,.15) 0%, transparent 70%);
        }
        .features-inner { max-width: 1160px; margin: 0 auto; position: relative; z-index: 1; }
        .feat-tag {
            display: inline-flex; align-items: center; gap: .4rem;
            background: rgba(22,163,74,.15); border: 1px solid rgba(22,163,74,.3);
            border-radius: 20px; padding: .25rem .8rem;
            font-size: .7rem; font-weight: 700; color: #4ade80;
            text-transform: uppercase; letter-spacing: .07em; margin-bottom: 1rem;
        }
        .feat-title {
            font-family: 'Instrument Serif', serif;
            font-size: clamp(1.9rem, 3vw, 3rem);
            color: #fff; margin-bottom: .75rem; letter-spacing: -.02em;
        }
        .feat-sub { font-size: .95rem; color: rgba(255,255,255,.5); max-width: 500px; margin-bottom: 3.5rem; line-height: 1.75; }
        .feat-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.25rem; }
        .feat-card {
            background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08);
            border-radius: var(--r-lg); padding: 1.75rem 1.5rem;
            transition: background .2s, border-color .2s, transform .2s; cursor: pointer;
        }
        .feat-card:hover { background: rgba(255,255,255,.07); border-color: rgba(22,163,74,.4); transform: translateY(-4px); }
        .feat-icon {
            width: 44px; height: 44px; border-radius: var(--r-sm);
            background: rgba(22,163,74,.15); border: 1px solid rgba(22,163,74,.25);
            display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;
        }
        .feat-card-title { font-size: .92rem; font-weight: 700; color: #fff; margin-bottom: .5rem; }
        .feat-card-desc { font-size: .8rem; color: rgba(255,255,255,.45); line-height: 1.65; }

        /* COMPARISON */
        .diff-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 5rem; align-items: center; }
        .comparison-card {
            background: var(--white); border-radius: var(--r-xl); padding: 2rem;
            box-shadow: 0 8px 40px rgba(0,0,0,.06); border: 1px solid rgba(120,113,108,.12);
        }
        .comp-row {
            display: flex; align-items: center; gap: 1rem;
            padding: .9rem 0; border-bottom: 1px dashed rgba(120,113,108,.15);
        }
        .comp-row:last-child { border-bottom: none; }
        .comp-feat { font-size: .82rem; font-weight: 500; color: var(--ink); flex: 1; }
        .comp-head { font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--muted); text-align: center; }
        .chip-no { display: inline-block; background: #fee2e2; color: #991b1b; border-radius: 4px; padding: .2rem .55rem; font-size: .65rem; font-weight: 700; }
        .chip-yes { display: inline-block; background: var(--green-l); color: #15803d; border-radius: 4px; padding: .2rem .55rem; font-size: .65rem; font-weight: 700; }

        /* TESTIMONIAL */
        .testimonial-section {
            background: var(--stone-l); padding: 6rem 2rem;
            border-top: 1px solid rgba(120,113,108,.1);
            border-bottom: 1px solid rgba(120,113,108,.1);
        }
        .testimonial-inner { max-width: 900px; margin: 0 auto; text-align: center; }
        .quote-mark {
            font-family: 'Instrument Serif', serif;
            font-size: 6rem; line-height: .6; color: var(--green-m);
            margin-bottom: 1.5rem; display: block;
        }
        .quote-text {
            font-family: 'Instrument Serif', serif;
            font-size: clamp(1.35rem, 2.5vw, 1.9rem);
            line-height: 1.45; color: var(--ink); margin-bottom: 2rem; font-style: italic;
        }
        .quote-author { font-size: .8rem; font-weight: 600; color: var(--stone); }
        .quote-role { font-size: .72rem; color: var(--muted); margin-top: .25rem; }

        /* CTA */
        .cta-section { padding: 7rem 2rem; text-align: center; position: relative; overflow: hidden; }
        .cta-section::before {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(ellipse 70% 60% at 50% 50%, rgba(22,163,74,.07) 0%, transparent 70%);
        }
        .cta-inner { max-width: 640px; margin: 0 auto; position: relative; z-index: 1; }
        .cta-title {
            font-family: 'Instrument Serif', serif;
            font-size: clamp(2rem, 4vw, 3.5rem);
            line-height: 1.1; letter-spacing: -.025em; margin-bottom: 1.25rem;
        }
        .cta-sub { font-size: 1rem; color: var(--stone); line-height: 1.75; margin-bottom: 2.5rem; }
        .cta-btns { display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap; }

        /* FOOTER */
        footer { background: var(--ink-2); padding: 3rem 2rem 2rem; color: rgba(255,255,255,.5); font-size: .78rem; }
        .footer-inner { max-width: 1160px; margin: 0 auto; }
        .footer-top {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;
        }
        .footer-brand { display: flex; align-items: center; gap: .6rem; color: #fff; font-weight: 700; font-size: .9rem; }
        .footer-links { display: flex; gap: 2rem; }
        .footer-link { color: rgba(255,255,255,.4); text-decoration: none; transition: color .15s; }
        .footer-link:hover { color: rgba(255,255,255,.8); }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,.08); padding-top: 1.5rem;
            display: flex; justify-content: space-between; flex-wrap: wrap; gap: .5rem;
        }

        /* ALERT */
        .alert-banner {
            background: var(--amber); padding: .55rem 1.5rem;
            display: flex; align-items: center; justify-content: center; gap: .75rem;
            font-size: .75rem; font-weight: 600; color: #451a03;
        }

        /* SCROLL REVEAL */
        .reveal { opacity: 0; transform: translateY(28px); transition: opacity .6s ease, transform .6s ease; }
        .reveal.visible { opacity: 1; transform: translateY(0); }

        /* RESPONSIVE */
        @media (max-width: 900px) {
            .hero { grid-template-columns: 1fr; min-height: auto; }
            .hero-left { padding: 4rem 1.5rem; }
            .hero-right { display: none; }
            .how-header { grid-template-columns: 1fr; gap: 2rem; }
            .feat-grid { grid-template-columns: 1fr; }
            .diff-grid { grid-template-columns: 1fr; gap: 2.5rem; }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>

{{-- Alert banner --}}
<div class="alert-banner">
    <span>⚠</span>
    <span>Platform ini adalah prototipe — belum aktif secara resmi. Daftar untuk akses awal.</span>
</div>

{{-- Nav --}}
<nav>
    <a href="{{ url('/') }}" class="nav-brand">
        <div class="nav-logo">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 20l5-9 4 6 3-4 6 7H3z"/>
            </svg>
        </div>
        SummitPass
    </a>
    <div class="nav-spacer"></div>
    <div class="nav-links">
        <a href="#cara-kerja" class="nav-link">Cara kerja</a>
        <a href="#fitur" class="nav-link">Fitur</a>
        <a href="#keamanan" class="nav-link">Keamanan</a>
    </div>
    @auth
        <a href="{{ route('dashboard') }}" class="btn-nav-login" style="margin-right:.5rem;">Dashboard</a>
    @else
        <a href="{{ route('login') }}" class="btn-nav-login" style="margin-right:.5rem;">Masuk</a>
        <a href="{{ route('register') }}" class="btn-nav-cta">Daftar gratis</a>
    @endauth
</nav>

{{-- Hero --}}
<section class="hero">
    <div class="hero-left">
        <div class="hero-badge">
            <div class="hero-badge-dot"></div>
            Platform Keselamatan Pendakian Indonesia
        </div>
        <h1 class="hero-headline">
            Setiap langkah<br>
            pendakianmu<br>
            <em>tercatat aman.</em>
        </h1>
        <p class="hero-sub">
            SummitPass menggantikan SIMAKSI kertas dengan sistem digital yang mencatat pergerakan pendaki di <strong>setiap pos</strong> — naik dan turun — untuk menyediakan data real-time bagi tim SAR.
        </p>
        <div class="hero-ctas">
            <a href="{{ route('register') }}" class="btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Daftar sekarang
            </a>
            <a href="#cara-kerja" class="btn-ghost">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                Lihat cara kerja
            </a>
        </div>
        <div class="hero-stats">
            <div>
                <div class="stat-val">15+</div>
                <div class="stat-lbl">Gunung</div>
            </div>
            <div>
                <div class="stat-val">10k+</div>
                <div class="stat-lbl">Pendaki aktif</div>
            </div>
            <div>
                <div class="stat-val">100%</div>
                <div class="stat-lbl">Termonitor</div>
            </div>
        </div>
    </div>

    <div class="hero-right">
        <div class="hero-phone-wrap">
            <div class="phone">
                <div class="phone-notch"></div>
                <div class="phone-screen">
                    <div class="phone-topbar">
                        <span class="phone-brand">SummitPass</span>
                        <div class="phone-avatar">RD</div>
                    </div>
                    <div class="phone-section">
                        <div class="phone-qr-card">
                            <div class="phone-qr-user">Rizky Dwipanegara</div>
                            <div class="phone-qr-nik">NIK: 3201 •••• •••• 0027</div>
                            <div class="phone-qr-box">
                                <div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell b"></div>
                                <div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell b"></div>
                                <div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell b"></div>
                                <div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell"></div>
                                <div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell b"></div>
                                <div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell"></div>
                                <div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell b"></div>
                                <div class="qr-cell"></div><div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell b"></div><div class="qr-cell"></div>
                                <div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell b"></div><div class="qr-cell"></div><div class="qr-cell b"></div>
                            </div>
                            <div class="phone-qr-trip">Rinjani · Jalur Sembalun<br><strong>28–30 Mar 2026</strong></div>
                        </div>

                        <div class="phone-pos-label">Rekam jejak pos</div>
                        <div class="phone-pos-item">
                            <div class="pos-dot" style="background:#16a34a"></div>
                            <div><div class="pos-name">Pos 1 — Sembalun Lawang</div></div>
                            <div class="pos-time">07:24</div>
                        </div>
                        <div class="phone-pos-item">
                            <div class="pos-dot" style="background:#16a34a"></div>
                            <div><div class="pos-name">Pos 2 — Padang Savana</div></div>
                            <div class="pos-time">10:15</div>
                        </div>
                        <div class="phone-pos-item">
                            <div class="pos-dot" style="background:#f59e0b"></div>
                            <div><div class="pos-name">Pos 3 — Pelawangan</div></div>
                            <div class="pos-time">Menunggu…</div>
                        </div>

                        <div class="phone-sos">
                            <span class="sos-icon">🆘</span>
                            <span class="sos-text">Tekan untuk kirim darurat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Marquee --}}
<div class="marquee-strip">
    <div class="marquee-track">
        <span class="marquee-item">SIMAKSI Digital <span class="marquee-sep"></span></span>
        <span class="marquee-item">QR per Pendaki <span class="marquee-sep"></span></span>
        <span class="marquee-item">Scan setiap pos <span class="marquee-sep"></span></span>
        <span class="marquee-item">Data SAR real-time <span class="marquee-sep"></span></span>
        <span class="marquee-item">Validasi NIK otomatis <span class="marquee-sep"></span></span>
        <span class="marquee-item">Kuota live <span class="marquee-sep"></span></span>
        <span class="marquee-item">Deteksi anomali waktu <span class="marquee-sep"></span></span>
        <span class="marquee-item">Mode offline tersedia <span class="marquee-sep"></span></span>
        <span class="marquee-item">SIMAKSI Digital <span class="marquee-sep"></span></span>
        <span class="marquee-item">QR per Pendaki <span class="marquee-sep"></span></span>
        <span class="marquee-item">Scan setiap pos <span class="marquee-sep"></span></span>
        <span class="marquee-item">Data SAR real-time <span class="marquee-sep"></span></span>
        <span class="marquee-item">Validasi NIK otomatis <span class="marquee-sep"></span></span>
        <span class="marquee-item">Kuota live <span class="marquee-sep"></span></span>
        <span class="marquee-item">Deteksi anomali waktu <span class="marquee-sep"></span></span>
        <span class="marquee-item">Mode offline tersedia <span class="marquee-sep"></span></span>
    </div>
</div>

{{-- Cara Kerja --}}
<section id="cara-kerja" style="padding: 7rem 2rem; max-width: 1160px; margin: 0 auto; position: relative; z-index: 1;">
    <div class="how-header">
        <div class="reveal">
            <div class="section-tag">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                Cara kerja
            </div>
            <h2 class="section-title">Dari daftar<br>hingga<br><em style="font-style:italic;color:var(--green)">turun gunung.</em></h2>
            <p class="section-sub">Setiap langkah dirancang agar pendaki bisa fokus mendaki, bukan mengurus administrasi.</p>
        </div>

        <div class="steps-timeline reveal">
            <div class="step-item">
                <div class="step-num-circle active">1</div>
                <div class="step-content">
                    <div class="step-lbl">Daftar & verifikasi NIK</div>
                    <div class="step-desc">Buat akun dengan email. Lengkapi NIK dan kontak darurat di profil — tersimpan untuk pendakian berikutnya.</div>
                </div>
            </div>
            <div class="step-item">
                <div class="step-num-circle">2</div>
                <div class="step-content">
                    <div class="step-lbl">Booking SIMAKSI digital</div>
                    <div class="step-desc">Pilih gunung, jalur, tanggal. Bayar online. Kuota tersedia tampil real-time.</div>
                </div>
            </div>
            <div class="step-item">
                <div class="step-num-circle">3</div>
                <div class="step-content">
                    <div class="step-lbl">Dapatkan QR SummitPass</div>
                    <div class="step-desc">QR unik terikat NIK, tersimpan offline. Satu QR untuk seluruh perjalanan.</div>
                    <span class="step-warn-badge">Tersedia tanpa sinyal</span>
                </div>
            </div>
            <div class="step-item">
                <div class="step-num-circle">4</div>
                <div class="step-content">
                    <div class="step-lbl">Scan di setiap pos — naik & turun</div>
                    <div class="step-desc">Petugas scan QR di tiap pos. Timestamp dan lokasi tercatat otomatis ke server.</div>
                </div>
            </div>
            <div class="step-item">
                <div class="step-num-circle">5</div>
                <div class="step-content">
                    <div class="step-lbl">Checkout & riwayat lengkap</div>
                    <div class="step-desc">Scan keluar di gerbang. Rekam jejak pendakian tersimpan selamanya di akun Anda.</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Features --}}
<div id="fitur" class="features-bg">
    <div class="features-inner">
        <div class="reveal">
            <div class="feat-tag">Fitur unggulan</div>
            <h2 class="feat-title">Dibangun untuk<br>keselamatan nyata.</h2>
            <p class="feat-sub">Bukan sekadar tiket digital — ini sistem rekam jejak yang menjadi landasan operasi SAR jika dibutuhkan.</p>
        </div>
        <div class="feat-grid reveal">
            <div class="feat-card">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="3" height="3" rx=".5"/></svg>
                </div>
                <div class="feat-card-title">QR terikat NIK</div>
                <div class="feat-card-desc">Setiap pendaki punya QR pribadi yang terhubung ke identitas resmi. Tidak bisa dipinjam atau dipalsukan.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                <div class="feat-card-title">Rekam jejak per pos</div>
                <div class="feat-card-desc">Waktu masuk dan keluar setiap pos tercatat. Tim SAR tahu posisi terakhir pendaki dengan presisi.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <div class="feat-card-title">Deteksi anomali waktu</div>
                <div class="feat-card-desc">Sistem memicu alert otomatis jika pendaki melewati batas waktu checkout yang ditetapkan regulasi.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="feat-card-title">Manajemen rombongan</div>
                <div class="feat-card-desc">Leader pantau status scan semua anggota dalam satu dashboard. Tahu siapa yang sudah/belum di pos mana.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="1" y1="1" x2="23" y2="23"/><path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"/><path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"/><path d="M10.71 5.05A16 16 0 0 1 22.56 9"/><path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
                </div>
                <div class="feat-card-title">Scan offline</div>
                <div class="feat-card-desc">QR bisa ditampilkan tanpa internet. Data pos disimpan lokal dan di-sync otomatis saat sinyal kembali.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                </div>
                <div class="feat-card-title">SIMAKSI terintegrasi</div>
                <div class="feat-card-desc">Satu platform untuk izin, pembayaran retribusi, dan monitoring. Pengelola kawasan tidak perlu sistem terpisah.</div>
            </div>
        </div>
    </div>
</div>

{{-- Comparison --}}
<div id="keamanan" style="padding: 7rem 2rem; max-width: 1160px; margin: 0 auto; position: relative; z-index: 1;">
    <div class="diff-grid">
        <div class="reveal">
            <div class="section-tag">Kenapa berbeda?</div>
            <h2 class="section-title">Digitalisasi yang berhenti di gerbang bukanlah solusi.</h2>
            <p class="section-sub" style="margin-bottom: 1.5rem;">Platform SIMAKSI saat ini mencatat pendaki masuk, tapi tidak tahu kapan dan di mana mereka berada di jalur. SummitPass hadir untuk menutup celah itu.</p>
            <div style="display:flex;align-items:center;gap:.6rem;font-size:.82rem;font-weight:500;color:var(--green-d);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg>
                Berkolaborasi dengan pengelola kawasan & Basarnas
            </div>
        </div>
        <div class="comparison-card reveal">
            <div style="display:grid; grid-template-columns: 1fr 80px 80px; gap:.5rem; padding-bottom:.75rem; border-bottom: 1px solid rgba(120,113,108,.15); margin-bottom:.5rem;">
                <div></div>
                <div class="comp-head">SIMAKSI<br>lama</div>
                <div class="comp-head" style="color:var(--green-d);">Summit<br>Pass</div>
            </div>
            <div class="comp-row">
                <div class="comp-feat">SIMAKSI digital (tanpa loket)</div>
                <div style="text-align:center"><span class="chip-yes">✓</span></div>
                <div style="text-align:center"><span class="chip-yes">✓</span></div>
            </div>
            <div class="comp-row">
                <div class="comp-feat">Rekam jejak setiap pos</div>
                <div style="text-align:center"><span class="chip-no">✗</span></div>
                <div style="text-align:center"><span class="chip-yes">✓</span></div>
            </div>
            <div class="comp-row">
                <div class="comp-feat">QR pribadi terikat NIK</div>
                <div style="text-align:center"><span class="chip-no">✗</span></div>
                <div style="text-align:center"><span class="chip-yes">✓</span></div>
            </div>
            <div class="comp-row">
                <div class="comp-feat">Deteksi anomali & alert SAR</div>
                <div style="text-align:center"><span class="chip-no">✗</span></div>
                <div style="text-align:center"><span class="chip-yes">✓</span></div>
            </div>
            <div class="comp-row">
                <div class="comp-feat">Dashboard rombongan real-time</div>
                <div style="text-align:center"><span class="chip-no">✗</span></div>
                <div style="text-align:center"><span class="chip-yes">✓</span></div>
            </div>
            <div class="comp-row">
                <div class="comp-feat">Akses QR tanpa sinyal</div>
                <div style="text-align:center"><span class="chip-no">✗</span></div>
                <div style="text-align:center"><span class="chip-yes">✓</span></div>
            </div>
            <div class="comp-row">
                <div class="comp-feat">Tombol SOS terintegrasi</div>
                <div style="text-align:center"><span class="chip-no">✗</span></div>
                <div style="text-align:center"><span class="chip-yes">✓</span></div>
            </div>
        </div>
    </div>
</div>

{{-- Testimonial --}}
<div class="testimonial-section">
    <div class="testimonial-inner reveal">
        <span class="quote-mark">"</span>
        <p class="quote-text">Dengan SummitPass, pengelola kawasan bisa tahu posisi terakhir pendaki secara real-time. Ini yang selama ini kami butuhkan untuk mempercepat respons SAR.</p>
        <div class="quote-author">Budi Santoso</div>
        <div class="quote-role">Kepala Pos Pendakian — Gunung Rinjani</div>
    </div>
</div>

{{-- CTA --}}
<div class="cta-section">
    <div class="cta-inner reveal">
        <h2 class="cta-title">Siap mendaki<br><em style="font-style:italic;color:var(--green)">dengan aman?</em></h2>
        <p class="cta-sub">Bergabung dengan ribuan pendaki yang sudah menggunakan SummitPass. Gratis untuk pendaki — selalu.</p>
        <div class="cta-btns">
            <a href="{{ route('register') }}" class="btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Daftar gratis sekarang
            </a>
            <a href="{{ route('login') }}" class="btn-ghost" style="color:var(--ink)">
                Saya sudah punya akun →
            </a>
        </div>
    </div>
</div>

{{-- Footer --}}
<footer>
    <div class="footer-inner">
        <div class="footer-top">
            <div class="footer-brand">
                <div class="nav-logo" style="width:28px;height:28px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20l5-9 4 6 3-4 6 7H3z"/></svg>
                </div>
                SummitPass
            </div>
            <div class="footer-links">
                <a href="#" class="footer-link">Tentang</a>
                <a href="#" class="footer-link">Kebijakan privasi</a>
                <a href="#" class="footer-link">Kontak</a>
                <a href="#" class="footer-link">API</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} SummitPass. Prototipe — belum aktif secara resmi.</span>
            <span>Dibuat untuk keselamatan pendaki Indonesia 🏔</span>
        </div>
    </div>
</footer>

<script>
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) e.target.classList.add('visible');
        });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
</body>
</html>
