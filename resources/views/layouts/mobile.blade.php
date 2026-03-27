<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#1a6344">
    <title>{{ $title ?? 'SummitPass' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: var(--color-surface-alt);
            min-height: 100dvh;
            overscroll-behavior: none;
        }
        .mobile-content {
            padding-bottom: calc(4.5rem + env(safe-area-inset-bottom));
        }
        .mobile-content.no-nav {
            padding-bottom: env(safe-area-inset-bottom);
        }
    </style>
    {{ $head ?? '' }}
</head>
<body x-data>

    {{-- Header --}}
    @isset($header)
    {{ $header }}
    @else
    <header class="mobile-header">
        @if($showBack ?? false)
        <a href="{{ $backUrl ?? 'javascript:history.back()' }}" class="btn btn-ghost btn-icon btn-sm" style="flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15,18 9,12 15,6"/>
            </svg>
        </a>
        @else
        <div class="flex items-center gap-2 flex-shrink-0">
            <div class="sidebar-logo-icon" style="width:30px;height:30px;border-radius:8px;background:var(--color-forest-700);">
                <img src="{{ asset('logo.png') }}" alt="SummitPass" style="width:100%;height:100%;object-fit:contain;">
            </div>
        </div>
        @endif

        <div class="flex-1 min-w-0">
            <div class="font-semibold text-sm truncate" style="color:var(--color-text);">
                {{ $title ?? 'SummitPass' }}
            </div>
            @isset($subtitle)
            <div class="text-xs truncate" style="color:var(--color-text-muted);">{{ $subtitle }}</div>
            @endisset
        </div>

        @isset($headerAction)
        {{ $headerAction }}
        @endif
    </header>
    @endisset

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="px-4 pt-3">
        <div class="alert alert-success animate-fade-in">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                <polyline points="20,6 9,17 4,12"/>
            </svg>
            <span class="text-sm">{{ session('success') }}</span>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="px-4 pt-3">
        <div class="alert alert-error animate-fade-in">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span class="text-sm">{{ session('error') }}</span>
        </div>
    </div>
    @endif

    {{-- Main content --}}
    <main class="mobile-content {{ ($hideNav ?? false) ? 'no-nav' : '' }}">
        {{ $slot }}
    </main>

    {{-- Bottom navigation --}}
    @unless($hideNav ?? false)
    <nav class="mobile-bottom-nav">
        <a href="{{ route('home') }}"
           class="mobile-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ request()->routeIs('home') ? '2.25' : '1.75' }}" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9,22 9,12 15,12 15,22"/>
            </svg>
            <span>Beranda</span>
        </a>

        <a href="#"
           class="mobile-nav-item {{ request()->routeIs('booking.*') ? 'active' : '' }}">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ request()->routeIs('booking.*') ? '2.25' : '1.75' }}" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14,2 14,8 20,8"/>
            </svg>
            <span>Booking</span>
        </a>

        <a href="#"
           class="mobile-nav-item {{ request()->routeIs('qr-pass.*') ? 'active' : '' }}"
           style="position:relative;">
            {{-- QR Pass center button --}}
            <div style="
                width:52px; height:52px;
                background: linear-gradient(135deg, var(--color-forest-700), var(--color-lake-600));
                border-radius: 16px;
                display: flex; align-items: center; justify-content: center;
                box-shadow: 0 4px 16px rgba(45,106,79,0.35);
                margin-top: -16px;
            ">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                    <path d="M14 14h1v1h-1z M14 17h1 M17 14h1 M17 17h1v1h-1 M20 14h1v4h-1 M14 20h4v1h-4"/>
                </svg>
            </div>
            <span>QR Pass</span>
        </a>

        <a href="#"
           class="mobile-nav-item {{ request()->routeIs('trekking.*') ? 'active' : '' }}">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ request()->routeIs('trekking.*') ? '2.25' : '1.75' }}" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12,6 12,12 16,14"/>
            </svg>
            <span>Log</span>
        </a>

        <a href="#"
           class="mobile-nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ request()->routeIs('profile.*') ? '2.25' : '1.75' }}" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            <span>Profil</span>
        </a>
    </nav>
    @endunless

</body>
</html>
