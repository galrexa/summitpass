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
        .nav-logo { width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; }
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
            font-size: clamp(2.2rem, 3.8vw, 3.6rem);
            line-height: 1.1;
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

        /* =============================================
           ROLE SHOWCASE SECTIONS
        ============================================= */
        .role-section {
            padding: 7rem 2rem;
            position: relative; z-index: 1;
        }
        .role-section.pendaki-bg {
            background: var(--stone-xl);
        }
        .role-section.pengelola-bg {
            background: linear-gradient(180deg, #f0fdf4 0%, var(--stone-xl) 100%);
        }
        .role-inner { max-width: 1160px; margin: 0 auto; }
        .role-header {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 5rem; align-items: center; margin-bottom: 3.5rem;
        }
        .role-header.reverse { direction: rtl; }
        .role-header.reverse > * { direction: ltr; }
        .role-label {
            display: inline-flex; align-items: center; gap: .5rem;
            border-radius: 20px; padding: .28rem .9rem;
            font-size: .68rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .08em; margin-bottom: 1rem;
        }
        .role-label.green { background: var(--green-l); border: 1px solid var(--green-m); color: #15803d; }
        .role-label.dark { background: var(--ink); border: 1px solid rgba(255,255,255,.1); color: #4ade80; }
        .role-title {
            font-family: 'Instrument Serif', serif;
            font-size: clamp(1.8rem, 2.8vw, 2.8rem);
            line-height: 1.15; letter-spacing: -.02em;
            color: var(--ink); margin-bottom: 1rem;
        }
        .role-title em { color: var(--green); font-style: italic; }
        .role-sub {
            font-size: .92rem; color: var(--stone); line-height: 1.8; margin-bottom: 1.75rem;
        }
        .role-checklist { list-style: none; display: flex; flex-direction: column; gap: .65rem; }
        .role-checklist li {
            display: flex; align-items: flex-start; gap: .6rem;
            font-size: .83rem; color: var(--stone); line-height: 1.5;
        }
        .role-checklist li span.check {
            width: 18px; height: 18px; border-radius: 50%;
            background: var(--green-l); border: 1px solid var(--green-m);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; margin-top: 1px;
        }
        .role-checklist li span.check svg { width: 10px; height: 10px; }

        /* Carousel */
        .carousel-wrap { position: relative; }
        .carousel-viewport {
            overflow: hidden;
            border-radius: var(--r-xl);
        }
        .carousel-track {
            display: flex;
            gap: 1.25rem;
            transition: transform .45s cubic-bezier(.4,0,.2,1);
            will-change: transform;
        }
        .carousel-slide {
            flex: 0 0 calc(100%);
            border-radius: var(--r-lg);
            overflow: hidden;
            position: relative;
            background: var(--white);
            box-shadow: 0 8px 40px rgba(0,0,0,.08);
            border: 1px solid rgba(120,113,108,.1);
        }

        /* Screenshot placeholder */
        .screenshot-placeholder {
            width: 100%; aspect-ratio: 16/10;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: 1rem;
            position: relative; overflow: hidden;
        }
        .screenshot-placeholder.pendaki-ph {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 50%, #bbf7d0 100%);
        }
        .screenshot-placeholder.pengelola-ph {
            background: linear-gradient(135deg, #14532d 0%, #166534 60%, #1a7a40 100%);
        }
        .ph-icon {
            width: 64px; height: 64px; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
        }
        .pendaki-ph .ph-icon { background: rgba(22,163,74,.15); border: 1.5px dashed #16a34a; }
        .pengelola-ph .ph-icon { background: rgba(255,255,255,.1); border: 1.5px dashed rgba(255,255,255,.4); }
        .ph-label {
            font-size: .8rem; font-weight: 600; text-align: center;
            max-width: 200px; line-height: 1.5;
        }
        .pendaki-ph .ph-label { color: #15803d; }
        .pengelola-ph .ph-label { color: rgba(255,255,255,.7); }
        .ph-upload-hint {
            font-size: .68rem; font-weight: 500;
            padding: .3rem .85rem; border-radius: 20px;
        }
        .pendaki-ph .ph-upload-hint {
            background: rgba(22,163,74,.12); color: #15803d; border: 1px dashed #86efac;
        }
        .pengelola-ph .ph-upload-hint {
            background: rgba(255,255,255,.08); color: rgba(255,255,255,.5); border: 1px dashed rgba(255,255,255,.2);
        }
        /* Grid pattern overlay for placeholders */
        .ph-grid {
            position: absolute; inset: 0; pointer-events: none;
            background-image:
                linear-gradient(rgba(0,0,0,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,0,0,.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .pengelola-ph .ph-grid {
            background-image:
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
        }

        /* Slide caption bar */
        .slide-caption {
            padding: 1rem 1.25rem;
            background: var(--white);
            border-top: 1px solid rgba(120,113,108,.08);
            display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        }
        .slide-caption-text { font-size: .78rem; font-weight: 600; color: var(--ink); }
        .slide-caption-sub { font-size: .7rem; color: var(--muted); margin-top: .1rem; }
        .slide-badge {
            flex-shrink: 0; font-size: .65rem; font-weight: 700;
            padding: .2rem .7rem; border-radius: 20px;
            background: var(--green-l); color: #15803d;
        }
        .slide-badge.dark { background: var(--ink); color: #4ade80; }

        /* Carousel nav */
        .carousel-nav {
            display: flex; align-items: center; justify-content: space-between;
            margin-top: 1.25rem;
        }
        .carousel-dots { display: flex; gap: .5rem; }
        .carousel-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: rgba(120,113,108,.2); border: none; cursor: pointer;
            transition: background .2s, width .2s; padding: 0;
        }
        .carousel-dot.active { background: var(--green); width: 22px; border-radius: 4px; }
        .carousel-arrows { display: flex; gap: .5rem; }
        .carousel-btn {
            width: 38px; height: 38px; border-radius: 50%;
            border: 1.5px solid rgba(120,113,108,.2);
            background: var(--white); cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: border-color .15s, background .15s;
        }
        .carousel-btn:hover { border-color: var(--green); background: var(--green-l); }
        .carousel-btn svg { width: 14px; height: 14px; }
        .carousel-btn:disabled { opacity: .35; cursor: default; pointer-events: none; }

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

        /* =============================================
           TESTIMONIALS — 3 CARDS
        ============================================= */
        .testimonials-section {
            background: var(--stone-l); padding: 6rem 2rem;
            border-top: 1px solid rgba(120,113,108,.1);
            border-bottom: 1px solid rgba(120,113,108,.1);
        }
        .testimonials-inner { max-width: 1160px; margin: 0 auto; }
        .testimonials-header { text-align: center; margin-bottom: 3.5rem; }
        .testimonial-eyebrow {
            display: inline-flex; align-items: center; gap: .4rem;
            background: var(--green-l); border: 1px solid var(--green-m);
            border-radius: 20px; padding: .25rem .8rem;
            font-size: .7rem; font-weight: 700; color: #15803d;
            text-transform: uppercase; letter-spacing: .07em; margin-bottom: .85rem;
        }
        .testimonials-title {
            font-family: 'Instrument Serif', serif;
            font-size: clamp(1.7rem, 2.5vw, 2.6rem);
            letter-spacing: -.02em; color: var(--ink);
        }
        .testi-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;
        }
        .testi-card {
            background: var(--white); border-radius: var(--r-xl); padding: 2rem 1.75rem;
            box-shadow: 0 4px 24px rgba(0,0,0,.05); border: 1px solid rgba(120,113,108,.1);
            display: flex; flex-direction: column; gap: 1.25rem;
            transition: transform .2s, box-shadow .2s;
        }
        .testi-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,.09); }
        .testi-role-chip {
            display: inline-flex; align-items: center; gap: .4rem;
            border-radius: 20px; padding: .22rem .75rem;
            font-size: .65rem; font-weight: 700; width: fit-content;
            text-transform: uppercase; letter-spacing: .06em;
        }
        .chip-pengelola { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
        .chip-pendaki   { background: var(--green-l); color: #15803d; border: 1px solid var(--green-m); }
        .chip-officer   { background: var(--amber-l); color: #92400e; border: 1px solid #fde68a; }
        .testi-quote {
            font-family: 'Instrument Serif', serif;
            font-size: 1.05rem; line-height: 1.55; color: var(--ink);
            font-style: italic; flex: 1;
        }
        .testi-quote::before { content: '\201C'; color: var(--green-m); font-size: 1.4em; line-height: 0; vertical-align: -0.3em; margin-right: .1em; }
        .testi-quote::after  { content: '\201D'; color: var(--green-m); font-size: 1.4em; line-height: 0; vertical-align: -0.3em; margin-left: .1em; }
        .testi-author { display: flex; align-items: center; gap: .75rem; }
        .testi-avatar {
            width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; font-weight: 800; color: #fff;
        }
        .avatar-blue   { background: linear-gradient(135deg, #1d4ed8, #3b82f6); }
        .avatar-green  { background: linear-gradient(135deg, #15803d, #22c55e); }
        .avatar-amber  { background: linear-gradient(135deg, #b45309, #f59e0b); }
        .testi-name { font-size: .82rem; font-weight: 700; color: var(--ink); }
        .testi-pos  { font-size: .7rem; color: var(--muted); margin-top: .1rem; }

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
            .role-header { grid-template-columns: 1fr; gap: 2.5rem; }
            .role-header.reverse { direction: ltr; }
            .testi-grid { grid-template-columns: 1fr; }
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
            <img src="{{ asset('logo.png') }}" alt="SummitPass" style="width:100%;height:100%;object-fit:contain;">
        </div>
        SummitPass
    </a>
    <div class="nav-spacer"></div>
    <div class="nav-links">
        <a href="#cara-kerja" class="nav-link">Cara kerja</a>
        <a href="#untuk-pendaki" class="nav-link">Untuk pendaki</a>
        <a href="#untuk-pengelola" class="nav-link">Untuk pengelola</a>
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
            Pendaki hilang bukan<br>
            karena tak ada yang mencari —<br>
            <em>tapi karena tak ada<br>yang tahu harus mulai dari mana.</em>
        </h1>
        <p class="hero-sub">
            SummitPass mencatat pergerakan pendaki di <strong>setiap pos</strong>, naik dan turun. Bukan sekadar tiket masuk — ini jejak digital yang menjadi landasan operasi SAR saat menit-menit pertama paling kritis.
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
        <span class="marquee-item">Tahu posisimu sekarang <span class="marquee-sep"></span></span>
        <span class="marquee-item">SAR butuh data, bukan asumsi <span class="marquee-sep"></span></span>
        <span class="marquee-item">Setiap pos, tercatat <span class="marquee-sep"></span></span>
        <span class="marquee-item">SIMAKSI online — tanpa loket <span class="marquee-sep"></span></span>
        <span class="marquee-item">QR terikat NIK <span class="marquee-sep"></span></span>
        <span class="marquee-item">Kuota jalur real-time <span class="marquee-sep"></span></span>
        <span class="marquee-item">Anomali terdeteksi otomatis <span class="marquee-sep"></span></span>
        <span class="marquee-item">Bukan tiket — ini lifeline <span class="marquee-sep"></span></span>
        <span class="marquee-item">Tahu posisimu sekarang <span class="marquee-sep"></span></span>
        <span class="marquee-item">SAR butuh data, bukan asumsi <span class="marquee-sep"></span></span>
        <span class="marquee-item">Setiap pos, tercatat <span class="marquee-sep"></span></span>
        <span class="marquee-item">SIMAKSI online — tanpa loket <span class="marquee-sep"></span></span>
        <span class="marquee-item">QR terikat NIK <span class="marquee-sep"></span></span>
        <span class="marquee-item">Kuota jalur real-time <span class="marquee-sep"></span></span>
        <span class="marquee-item">Anomali terdeteksi otomatis <span class="marquee-sep"></span></span>
        <span class="marquee-item">Bukan tiket — ini lifeline <span class="marquee-sep"></span></span>
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
            <h2 class="section-title">Dari daftar<br>hingga<br><em style="font-style:italic;color:var(--green)">kembali dengan selamat.</em></h2>
            <p class="section-sub">Setiap langkah dirancang agar pendaki bisa fokus mendaki, bukan mengurus administrasi. Pengelola kawasan cukup scan — sistem yang bekerja.</p>
        </div>

        <div class="steps-timeline reveal">
            <div class="step-item">
                <div class="step-num-circle active">1</div>
                <div class="step-content">
                    <div class="step-lbl">Daftar & verifikasi NIK</div>
                    <div class="step-desc">Buat akun sekali pakai. Lengkapi NIK dan kontak darurat — data tersimpan untuk semua pendakian berikutnya.</div>
                </div>
            </div>
            <div class="step-item">
                <div class="step-num-circle">2</div>
                <div class="step-content">
                    <div class="step-lbl">Ajukan SIMAKSI online</div>
                    <div class="step-desc">Pilih gunung, jalur, dan tanggal. Lihat kuota tersisa secara real-time. Bayar online — tidak perlu antre di loket.</div>
                </div>
            </div>
            <div class="step-item">
                <div class="step-num-circle">3</div>
                <div class="step-content">
                    <div class="step-lbl">Terima QR SummitPass</div>
                    <div class="step-desc">QR unik terikat NIK langsung aktif setelah izin disetujui. Tersimpan di aplikasi, bisa ditampilkan tanpa sinyal.</div>
                    <span class="step-warn-badge">Tersedia tanpa sinyal</span>
                </div>
            </div>
            <div class="step-item">
                <div class="step-num-circle">4</div>
                <div class="step-content">
                    <div class="step-lbl">Scan di setiap pos — naik & turun</div>
                    <div class="step-desc">Petugas scan QR di tiap pos. Timestamp dan lokasi tercatat otomatis. Pengelola bisa melihat posisi semua pendaki aktif di peta.</div>
                </div>
            </div>
            <div class="step-item">
                <div class="step-num-circle">5</div>
                <div class="step-content">
                    <div class="step-lbl">Checkout & riwayat lengkap</div>
                    <div class="step-desc">Scan keluar di gerbang. Rekam jejak pendakian tersimpan selamanya — data untuk SAR, kenangan untuk kamu.</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===================================================
     SECTION: UNTUK PENDAKI
================================================== --}}
<section id="untuk-pendaki" class="role-section pendaki-bg">
    <div class="role-inner">
        <div class="role-header reveal">
            <div>
                <div class="role-label green">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    Untuk Pendaki
                </div>
                <h2 class="role-title">SIMAKSI selesai<br>sebelum kamu<br><em>sampai di basecamp.</em></h2>
                <p class="role-sub">Tidak ada lagi antre di loket saat subuh, tidak ada lagi kertas izin yang basah kehujanan. Semua proses pendaftaran, pembayaran, dan perizinan bisa diselesaikan dari rumah — QR kamu sudah siap saat kamu berangkat.</p>
                <ul class="role-checklist">
                    <li>
                        <span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg></span>
                        Ajukan SIMAKSI kapan saja, dari mana saja — proses online penuh
                    </li>
                    <li>
                        <span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg></span>
                        Lihat kuota jalur dan ketersediaan tanggal secara real-time
                    </li>
                    <li>
                        <span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg></span>
                        QR berfungsi tanpa sinyal — cukup simpan di galeri atau aplikasi
                    </li>
                    <li>
                        <span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg></span>
                        Kelola rombongan — satu leader, semua anggota terpantau
                    </li>
                    <li>
                        <span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg></span>
                        Tombol SOS terintegrasi — kirim sinyal darurat beserta posisi terakhirmu
                    </li>
                </ul>
            </div>
            <div>
                {{-- Carousel Pendaki --}}
                <div class="carousel-wrap" id="carousel-pendaki">
                    <div class="carousel-viewport">
                        <div class="carousel-track" id="track-pendaki">
                            {{-- Slide 1 --}}
                            <div class="carousel-slide">
                                <img src="{{ asset('images/booking.png') }}" alt="Booking Pendakian" style="width:100%;aspect-ratio:16/10;object-fit:cover;display:block;">
                                <div class="slide-caption">
                                    <div>
                                        <div class="slide-caption-text">SIMAKSI Online</div>
                                        <div class="slide-caption-sub">Pengajuan izin pendakian tanpa loket</div>
                                    </div>
                                    <span class="slide-badge">Pendaki</span>
                                </div>
                            </div>
                            {{-- Slide 2 --}}
                            <div class="carousel-slide">
                                <img src="{{ asset('images/jejaksummit.png') }}" alt="Jejak Summit" style="width:100%;aspect-ratio:16/10;object-fit:cover;display:block;">
                                <div class="slide-caption">
                                    <div>
                                        <div class="slide-caption-text">Rekam Jejak & Riwayat</div>
                                        <div class="slide-caption-sub">Semua pendakianmu tersimpan permanen</div>
                                    </div>
                                    <span class="slide-badge">Pendaki</span>
                                </div>
                            </div>
                            {{-- Slide 3 --}}
                            <div class="carousel-slide">
                                <img src="{{ asset('images/trekkinglog.png') }}" alt="Trekking Log" style="width:100%;aspect-ratio:16/10;object-fit:cover;display:block;">
                                <div class="slide-caption">
                                    <div>
                                        <div class="slide-caption-text">Trekking Log</div>
                                        <div class="slide-caption-sub">Catatan setiap pos yang kamu lewati</div>
                                    </div>
                                    <span class="slide-badge">Pendaki</span>
                                </div>
                            </div>
                            {{-- Slide 4 --}}
                            <div class="carousel-slide">
                                <img src="{{ asset('images/qrpass.png') }}" alt="QR Pass" style="width:100%;aspect-ratio:16/10;object-fit:cover;display:block;">
                                <div class="slide-caption">
                                    <div>
                                        <div class="slide-caption-text">QR Pass</div>
                                        <div class="slide-caption-sub">Tiket digital pendakianmu di setiap pos</div>
                                    </div>
                                    <span class="slide-badge">Pendaki</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-nav">
                        <div class="carousel-dots" id="dots-pendaki">
                            <button class="carousel-dot active" data-carousel="pendaki" data-index="0"></button>
                            <button class="carousel-dot" data-carousel="pendaki" data-index="1"></button>
                            <button class="carousel-dot" data-carousel="pendaki" data-index="2"></button>
                            <button class="carousel-dot" data-carousel="pendaki" data-index="3"></button>
                        </div>
                        <div class="carousel-arrows">
                            <button class="carousel-btn" id="prev-pendaki" disabled aria-label="Sebelumnya">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15,18 9,12 15,6"/></svg>
                            </button>
                            <button class="carousel-btn" id="next-pendaki" aria-label="Berikutnya">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9,18 15,12 9,6"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===================================================
     SECTION: UNTUK PENGELOLA
================================================== --}}
<section id="untuk-pengelola" class="role-section pengelola-bg">
    <div class="role-inner">
        <div class="role-header reverse reveal">
            <div>
                <div class="role-label green">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    Untuk Pengelola
                </div>
                <h2 class="role-title">Semua pendaki aktif,<br>terpantau dalam<br><em>satu layar.</em></h2>
                <p class="role-sub">Dashboard monitoring dan peta jalur real-time memberi pengelola kawasan visibilitas penuh atas kondisi di lapangan. Tidak perlu menunggu laporan dari pos — sistem yang memberitahu terlebih dahulu saat ada anomali.</p>
                <ul class="role-checklist">
                    <li>
                        <span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg></span>
                        Peta jalur interaktif — lihat posisi terakhir setiap pendaki aktif
                    </li>
                    <li>
                        <span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg></span>
                        Dashboard monitoring kuota dan kepadatan per pos secara live
                    </li>
                    <li>
                        <span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg></span>
                        Alert otomatis jika pendaki melewati batas waktu checkout regulasi
                    </li>
                    <li>
                        <span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg></span>
                        Kelola SIMAKSI, retribusi, dan laporan dalam satu platform terintegrasi
                    </li>
                    <li>
                        <span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg></span>
                        Data tersedia untuk Basarnas — titik awal pencarian langsung teridentifikasi
                    </li>
                </ul>
            </div>
            <div>
                {{-- Carousel Pengelola --}}
                <div class="carousel-wrap" id="carousel-pengelola">
                    <div class="carousel-viewport">
                        <div class="carousel-track" id="track-pengelola">
                            {{-- Slide 1 --}}
                            <div class="carousel-slide">
                                <img src="{{ asset('images/dashboard.png') }}" alt="Dashboard Monitoring" style="width:100%;aspect-ratio:16/10;object-fit:cover;display:block;">
                                <div class="slide-caption">
                                    <div>
                                        <div class="slide-caption-text">Dashboard Monitoring</div>
                                        <div class="slide-caption-sub">Semua pendaki aktif dalam satu tampilan</div>
                                    </div>
                                    <span class="slide-badge dark">Pengelola</span>
                                </div>
                            </div>
                            {{-- Slide 2 --}}
                            <div class="carousel-slide">
                                <img src="{{ asset('images/trekkingmap.png') }}" alt="Trekking Map Real-time" style="width:100%;aspect-ratio:16/10;object-fit:cover;display:block;">
                                <div class="slide-caption">
                                    <div>
                                        <div class="slide-caption-text">Trekking Map Real-time</div>
                                        <div class="slide-caption-sub">Posisi terakhir tiap pendaki di peta jalur</div>
                                    </div>
                                    <span class="slide-badge dark">Pengelola</span>
                                </div>
                            </div>
                            {{-- Slide 3 --}}
                            <div class="carousel-slide">
                                <img src="{{ asset('images/alert.png') }}" alt="Alert & Notifikasi SAR" style="width:100%;aspect-ratio:16/10;object-fit:cover;display:block;">
                                <div class="slide-caption">
                                    <div>
                                        <div class="slide-caption-text">Alert & Notifikasi SAR</div>
                                        <div class="slide-caption-sub">Deteksi anomali waktu &amp; eskalasi otomatis</div>
                                    </div>
                                    <span class="slide-badge dark">Pengelola</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-nav">
                        <div class="carousel-dots" id="dots-pengelola">
                            <button class="carousel-dot active" data-carousel="pengelola" data-index="0"></button>
                            <button class="carousel-dot" data-carousel="pengelola" data-index="1"></button>
                            <button class="carousel-dot" data-carousel="pengelola" data-index="2"></button>
                        </div>
                        <div class="carousel-arrows">
                            <button class="carousel-btn" id="prev-pengelola" disabled aria-label="Sebelumnya">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15,18 9,12 15,6"/></svg>
                            </button>
                            <button class="carousel-btn" id="next-pengelola" aria-label="Berikutnya">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9,18 15,12 9,6"/></svg>
                            </button>
                        </div>
                    </div>
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
            <p class="feat-sub">Rata-rata tim SAR kehilangan 4–6 jam hanya untuk menentukan zona pencarian. SummitPass memotong waktu itu menjadi menit.</p>
        </div>
        <div class="feat-grid reveal">
            <div class="feat-card">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="3" height="3" rx=".5"/></svg>
                </div>
                <div class="feat-card-title">QR terikat NIK</div>
                <div class="feat-card-desc">Setiap pendaki punya QR pribadi yang terhubung ke identitas resmi. Tidak bisa dipinjam, tidak bisa dipalsukan.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                <div class="feat-card-title">Rekam jejak per pos</div>
                <div class="feat-card-desc">Waktu masuk dan keluar setiap pos tercatat. Tim SAR tahu pos terakhir pendaki dengan presisi waktu.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <div class="feat-card-title">Deteksi anomali waktu</div>
                <div class="feat-card-desc">Sistem memicu alert otomatis jika pendaki melewati batas waktu checkout. Eskalasi ke SAR dilakukan tanpa menunggu laporan manual.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                </div>
                <div class="feat-card-title">Trekking map real-time</div>
                <div class="feat-card-desc">Peta jalur interaktif menampilkan posisi terakhir setiap pendaki aktif. Pengelola kawasan bisa melihat kepadatan setiap pos sekaligus.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="1" y1="1" x2="23" y2="23"/><path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"/><path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"/><path d="M10.71 5.05A16 16 0 0 1 22.56 9"/><path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
                </div>
                <div class="feat-card-title">Scan offline</div>
                <div class="feat-card-desc">QR tampil tanpa internet. Data pos tersimpan lokal dan di-sync otomatis begitu sinyal kembali — tidak ada data yang terlewat.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                </div>
                <div class="feat-card-title">SIMAKSI terintegrasi</div>
                <div class="feat-card-desc">Satu platform untuk izin, retribusi, dan monitoring. Pengelola kawasan tidak perlu sistem terpisah — semuanya terhubung.</div>
            </div>
        </div>
    </div>
</div>

{{-- Comparison --}}
<div id="keamanan" style="padding: 7rem 2rem; max-width: 1160px; margin: 0 auto; position: relative; z-index: 1;">
    <div class="diff-grid">
        <div class="reveal">
            <div class="section-tag">Kenapa berbeda?</div>
            <h2 class="section-title">Dicatat masuk,<br>lalu <em style="font-style:italic;color:var(--green)">dilupakan.</em></h2>
            <p class="section-sub" style="margin-bottom: 1.5rem;">Sistem lama tahu kamu masuk. Tapi ketika kamu tidak keluar — mereka tidak tahu harus cari ke mana. SummitPass mengubah setiap pos menjadi titik data yang bisa membedakan pencarian tepat sasaran dan pencarian yang terlambat.</p>
            <div style="display:flex;align-items:center;gap:.6rem;font-size:.82rem;font-weight:500;color:var(--green-d);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg>
                Dirancang untuk berkolaborasi dengan pengelola kawasan &amp; Basarnas
            </div>
        </div>
        <div class="comparison-card reveal">
            <div style="display:grid; grid-template-columns: 1fr 80px 80px; gap:.5rem; padding-bottom:.75rem; border-bottom: 1px solid rgba(120,113,108,.15); margin-bottom:.5rem;">
                <div></div>
                <div class="comp-head">SIMAKSI<br>lama</div>
                <div class="comp-head" style="color:var(--green-d);">Summit<br>Pass</div>
            </div>
            <div class="comp-row">
                <div class="comp-feat">SIMAKSI online (tanpa loket)</div>
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
                <div class="comp-feat">Trekking map real-time</div>
                <div style="text-align:center"><span class="chip-no">✗</span></div>
                <div style="text-align:center"><span class="chip-yes">✓</span></div>
            </div>
            <div class="comp-row">
                <div class="comp-feat">Deteksi anomali &amp; alert SAR</div>
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

{{-- =============================================
     TESTIMONIALS — 3 CARDS
============================================= --}}
<div class="testimonials-section">
    <div class="testimonials-inner">
        <div class="testimonials-header reveal">
            <div class="testimonial-eyebrow">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Kata mereka
            </div>
            <h2 class="testimonials-title">Dirasakan dari tiga sisi berbeda.</h2>
        </div>
        <div class="testi-grid reveal">

            {{-- Card 1: Pengelola --}}
            <div class="testi-card">
                <span class="testi-role-chip chip-pengelola">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/></svg>
                    Pengelola Kawasan
                </span>
                <p class="testi-quote">Sebelumnya, kalau ada pendaki overdue, kami hanya bisa tebak-tebakan di pos mana mereka terakhir berada. Dengan SummitPass, kami langsung tahu — dan itu mengubah segalanya dalam operasi pencarian.</p>
                <div class="testi-author">
                    <div class="testi-avatar avatar-blue">AW</div>
                    <div>
                        <div class="testi-name">Ardan Wirosaputro</div>
                        <div class="testi-pos">Kepala Taman Nasional Gunung Orodruin</div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Pendaki --}}
            <div class="testi-card">
                <span class="testi-role-chip chip-pendaki">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    Pendaki
                </span>
                <p class="testi-quote">Saya solo hiker. Keluarga di rumah nggak bisa tidur kalau saya di gunung. Sekarang mereka bisa pantau sampai pos mana saya sudah jalan — dan saya bisa fokus mendaki tanpa rasa bersalah.</p>
                <div class="testi-author">
                    <div class="testi-avatar avatar-green">KN</div>
                    <div>
                        <div class="testi-name">Kira Natsume</div>
                        <div class="testi-pos">Solo hiker · 23 puncak terdaki</div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Petugas lapangan --}}
            <div class="testi-card">
                <span class="testi-role-chip chip-officer">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    Petugas Lapangan
                </span>
                <p class="testi-quote">Tiap hari saya scan ratusan QR. Yang bikin tenang: kalau ada yang belum balik lewat jam, sistem langsung kasih notif. Saya nggak harus ngitung buku manual sambil jaga pos sendirian lagi.</p>
                <div class="testi-author">
                    <div class="testi-avatar avatar-amber">WG</div>
                    <div>
                        <div class="testi-name">Pak Welman Goro</div>
                        <div class="testi-pos">Petugas Pos 3 — Jalur Selatan Gunung Myōboku</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- CTA --}}
<div class="cta-section">
    <div class="cta-inner reveal">
        <h2 class="cta-title">Gunung tidak menunggu.<br><em style="font-style:italic;color:var(--green)">Tapi SAR butuh datamu.</em></h2>
        <p class="cta-sub">Satu QR. Satu pendakian. Rekam jejak lengkap yang bisa membedakan antara pencarian tepat sasaran — dan pencarian yang terlambat. Gratis untuk pendaki, selalu.</p>
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
                <img src="{{ asset('logo.png') }}" alt="SummitPass" style="width:28px;height:28px;object-fit:contain;">
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
            <span>Dikembangkan oleh Bravopala Tech</span>
        </div>
    </div>
</footer>

<script>
    // Scroll reveal
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) e.target.classList.add('visible');
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    // Carousel factory
    function initCarousel(id) {
        const track   = document.getElementById('track-' + id);
        const slides  = track.querySelectorAll('.carousel-slide');
        const dots    = document.querySelectorAll(`[data-carousel="${id}"]`);
        const prevBtn = document.getElementById('prev-' + id);
        const nextBtn = document.getElementById('next-' + id);
        let current = 0;
        const total = slides.length;

        function goTo(index) {
            current = Math.max(0, Math.min(index, total - 1));
            track.style.transform = `translateX(calc(-${current * 100}% - ${current * 1.25}rem))`;
            dots.forEach((d, i) => d.classList.toggle('active', i === current));
            prevBtn.disabled = current === 0;
            nextBtn.disabled = current === total - 1;
        }

        prevBtn.addEventListener('click', () => goTo(current - 1));
        nextBtn.addEventListener('click', () => goTo(current + 1));
        dots.forEach(d => d.addEventListener('click', () => goTo(+d.dataset.index)));

        // Touch/swipe support
        let startX = 0;
        track.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
        track.addEventListener('touchend', e => {
            const diff = startX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 40) goTo(diff > 0 ? current + 1 : current - 1);
        });

        goTo(0);
    }

    initCarousel('pendaki');
    initCarousel('pengelola');
</script>
</body>
</html>