<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="webLayout()" :class="{ 'sidebar-collapsed': collapsed }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'SummitPass') }} — SummitPass</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ $head ?? '' }}
</head>
<body>

{{-- Mobile overlay --}}
<div
    class="fixed inset-0 bg-black/40 z-30 lg:hidden"
    x-show="mobileOpen"
    x-transition:enter="transition-opacity duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="mobileOpen = false"
    style="display:none"
></div>

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside
        class="sidebar"
        :class="{ 'collapsed': collapsed, 'mobile-open': mobileOpen }"
        @keydown.escape.window="mobileOpen = false"
    >
        {{-- Logo --}}
        @php $role = auth()->user()?->role ?? 'guest'; @endphp
        <a href="{{ in_array($role, ['admin', 'pengelola_tn']) ? route('admin.dashboard') : route('dashboard') }}" class="sidebar-logo">
            <div class="sidebar-logo-icon">
                <img src="{{ asset('logo.png') }}" alt="SummitPass" style="width:100%;height:100%;object-fit:contain;">
            </div>
            <span class="sidebar-logo-text">SummitPass</span>
        </a>

        {{-- Navigation --}}
        <nav class="sidebar-nav">

            {{-- Main --}}
            <div class="sidebar-section-label">Utama</div>

            @if(in_array($role, ['admin', 'pengelola_tn']))
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               data-tooltip="Dashboard">
                <svg class="sidebar-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
                </svg>
                <span class="sidebar-link-label">Dashboard</span>
            </a>
            @else
            <a href="{{ route('pendaki.jejak-summit') }}"
               class="sidebar-link {{ request()->routeIs('pendaki.jejak-summit') ? 'active' : '' }}"
               data-tooltip="Jejak Summit">
                <svg class="sidebar-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 20l5-9 4 6 3-4 6 7H3z"/>
                </svg>
                <span class="sidebar-link-label">Jejak Summit</span>
            </a>
            @endif

            @if(in_array($role, ['admin', 'pengelola_tn']))
            <a href="{{ route('admin.bookings.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}"
               data-tooltip="SIMAKSI">
                <svg class="sidebar-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                    <line x1="10" y1="9" x2="8" y2="9"/>
                </svg>
                <span class="sidebar-link-label">SIMAKSI / Booking</span>
                <span class="sidebar-badge" x-show="!collapsed">{{ $pendingBookings ?? 0 }}</span>
            </a>
            @else
            <a href="{{ route('pendaki.bookings') }}"
               class="sidebar-link {{ request()->routeIs('pendaki.bookings') ? 'active' : '' }}"
               data-tooltip="Booking Saya">
                <svg class="sidebar-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                    <line x1="10" y1="9" x2="8" y2="9"/>
                </svg>
                <span class="sidebar-link-label">Booking Saya</span>
            </a>
            @endif

            @if(!in_array($role, ['admin', 'pengelola_tn']))
            <a href="{{ route('pendaki.trekking-log') }}"
               class="sidebar-link {{ request()->routeIs('pendaki.trekking-log') ? 'active' : '' }}"
               data-tooltip="Trekking Log">
                <svg class="sidebar-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12,6 12,12 16,14"/>
                </svg>
                <span class="sidebar-link-label">Trekking Log</span>
            </a>

            <a href="{{ route('pendaki.my-pass') }}"
               class="sidebar-link {{ request()->routeIs('pendaki.my-pass') ? 'active' : '' }}"
               data-tooltip="QR Pass Saya">
                <svg class="sidebar-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="3" height="3" rx=".5"/>
                </svg>
                <span class="sidebar-link-label">QR Pass Saya</span>
            </a>
            @endif

            {{-- Manajemen (admin/pengelola only) --}}
            @if(in_array($role, ['admin', 'pengelola_tn']))
            <div class="sidebar-section-label">Manajemen</div>

            <a href="{{ route('admin.mountains.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.mountains.*') ? 'active' : '' }}"
               data-tooltip="Gunung">
                <svg class="sidebar-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 20l5-9 4 6 3-4 6 7H3z"/>
                </svg>
                <span class="sidebar-link-label">Gunung & Jalur</span>
            </a>

            <a href="{{ route('admin.bookings.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}"
               data-tooltip="Booking">
                <svg class="sidebar-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                <span class="sidebar-link-label">Booking / SIMAKSI</span>
            </a>

            <a href="{{ route('admin.payments.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}"
               data-tooltip="Pembayaran">
                <svg class="sidebar-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
                <span class="sidebar-link-label">Pembayaran</span>
            </a>

            @if($role === 'admin')
            <a href="{{ route('admin.users.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
               data-tooltip="Pengguna">
                <svg class="sidebar-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span class="sidebar-link-label">Pengguna</span>
            </a>
            @endif

            <a href="{{ route('admin.trekking-map.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.trekking-map.*') ? 'active' : '' }}"
               data-tooltip="Trekking Map">
                <svg class="sidebar-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="3,6 9,3 15,6 21,3 21,18 15,21 9,18 3,21"/>
                    <line x1="9" y1="3" x2="9" y2="18"/>
                    <line x1="15" y1="6" x2="15" y2="21"/>
                </svg>
                <span class="sidebar-link-label">Trekking Map</span>
            </a>
            @endif

            {{-- Simulasi Scan — admin & pengelola_tn --}}
            @if(in_array($role, ['admin', 'pengelola_tn']))
            <div class="sidebar-section-label">Sistem</div>
            <a href="{{ route('admin.simulate.scan') }}"
               class="sidebar-link {{ request()->routeIs('admin.simulate.*') ? 'active' : '' }}"
               data-tooltip="Simulasi Scan">
                <svg class="sidebar-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="3" height="3" rx=".5"/>
                </svg>
                <span class="sidebar-link-label">Simulasi Scan Pos</span>
            </a>
            @endif
            @if($role === 'admin')
            <a href="{{ route('admin.settings.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
               data-tooltip="Pengaturan">
                <svg class="sidebar-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.07 4.93l-1.42 1.42M4.93 4.93l1.42 1.42M12 2v2M12 20v2M20 12h2M2 12h2M19.07 19.07l-1.42-1.42M4.93 19.07l1.42-1.42"/>
                </svg>
                <span class="sidebar-link-label">Pengaturan</span>
            </a>
            @endif

        </nav>

        {{-- Sidebar footer --}}
        <div class="border-t border-white/10 p-3">
            <div class="sidebar-link" style="cursor:default; padding: 0.5rem 0.75rem;">
                <div class="avatar" style="width:28px;height:28px;font-size:0.7rem;background:var(--color-forest-600);color:white;flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                </div>
                <div class="sidebar-link-label" style="line-height:1.2;">
                    <div style="font-size:0.8rem;font-weight:600;color:var(--color-forest-100);">{{ auth()->user()?->name ?? 'Guest' }}</div>
                    <div style="font-size:0.68rem;color:var(--color-stone-400);">{{ ucfirst(str_replace('_', ' ', auth()->user()?->role ?? '')) }}</div>
                </div>
            </div>
        </div>

        {{-- Collapse toggle (desktop) --}}
        <div class="hidden lg:flex items-center justify-end px-3 pb-3">
            <button
                class="sidebar-toggle"
                @click="collapsed = !collapsed; localStorage.setItem('sidebar-collapsed', collapsed)"
                :title="collapsed ? 'Perluas sidebar' : 'Ciutkan sidebar'"
            >
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                     :style="collapsed ? 'transform:rotate(180deg)' : ''">
                    <polyline points="15,18 9,12 15,6"/>
                </svg>
            </button>
        </div>
    </aside>

    {{-- Main area --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Topbar --}}
        <header class="topbar">
            {{-- Mobile menu button --}}
            <button class="btn btn-ghost btn-icon lg:hidden" @click="mobileOpen = !mobileOpen" aria-label="Toggle menu">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>

            {{-- Breadcrumb / Page title --}}
            <div class="flex-1 min-w-0">
                <h1 class="text-base font-semibold truncate" style="color:var(--color-text);">
                    {{ $title ?? 'Dashboard' }}
                </h1>
                @if(isset($breadcrumb))
                <div class="flex items-center gap-1 text-xs mt-0.5" style="color:var(--color-text-muted);">
                    @foreach($breadcrumb as $idx => $crumb)
                        @if($idx > 0)
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9,18 15,12 9,6"/></svg>
                        @endif
                        <span>{{ $crumb }}</span>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Right actions --}}
            <div class="flex items-center gap-2">
                {{-- Notification bell --}}
                <button class="btn btn-ghost btn-icon" aria-label="Notifikasi" style="position:relative;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    @if(($alertCount ?? 0) > 0)
                    <span style="position:absolute;top:4px;right:4px;width:8px;height:8px;border-radius:50%;background:#ef4444;border:2px solid white;"></span>
                    @endif
                </button>

                {{-- Profile dropdown --}}
                <div style="position:relative;" @click.outside="profileOpen = false">
                    <button
                        @click="profileOpen = !profileOpen"
                        class="avatar"
                        style="cursor:pointer;border:2px solid transparent;transition:border-color 0.15s;"
                        :style="profileOpen ? 'border-color:var(--color-forest-400);' : ''"
                        aria-label="Menu profil"
                    >
                        {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                    </button>

                    {{-- Dropdown --}}
                    <div
                        x-show="profileOpen"
                        style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:220px;background:white;border:1px solid var(--color-border);border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.1);z-index:50;overflow:hidden;"
                    >
                        {{-- User info --}}
                        <div style="padding:0.875rem 1rem;border-bottom:1px solid var(--color-border);">
                            <div style="font-size:0.875rem;font-weight:600;color:var(--color-text);truncate;">{{ auth()->user()?->name ?? 'Guest' }}</div>
                            <div style="font-size:0.75rem;color:var(--color-text-muted);margin-top:0.1rem;">{{ auth()->user()?->email ?? '' }}</div>
                            <div style="margin-top:0.375rem;">
                                <span class="badge badge-green" style="font-size:0.65rem;">{{ ucfirst(str_replace('_', ' ', auth()->user()?->role ?? '')) }}</span>
                            </div>
                        </div>

                        {{-- Menu items --}}
                        <div style="padding:0.375rem 0;">
                            @php $isAdmin = in_array(auth()->user()?->role, ['admin', 'pengelola_tn']); @endphp
                            <a href="{{ $isAdmin ? route('admin.dashboard') : route('pendaki.profile') }}"
                               style="display:flex;align-items:center;gap:0.625rem;padding:0.6rem 1rem;font-size:0.85rem;color:var(--color-text);text-decoration:none;transition:background 0.1s;"
                               onmouseover="this.style.background='var(--color-forest-50)'" onmouseout="this.style.background='transparent'">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" style="color:var(--color-text-muted);">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                </svg>
                                Profil Saya
                            </a>
                            <a href="{{ $isAdmin ? route('admin.settings.index') : route('pendaki.settings') }}"
                               style="display:flex;align-items:center;gap:0.625rem;padding:0.6rem 1rem;font-size:0.85rem;color:var(--color-text);text-decoration:none;transition:background 0.1s;"
                               onmouseover="this.style.background='var(--color-forest-50)'" onmouseout="this.style.background='transparent'">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" style="color:var(--color-text-muted);">
                                    <circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.42 1.42M4.93 4.93l1.42 1.42M12 2v2M12 20v2M20 12h2M2 12h2M19.07 19.07l-1.42-1.42M4.93 19.07l1.42-1.42"/>
                                </svg>
                                Pengaturan Akun
                            </a>
                        </div>

                        {{-- Logout --}}
                        <div style="padding:0.375rem 0;border-top:1px solid var(--color-border);">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    style="display:flex;align-items:center;gap:0.625rem;padding:0.6rem 1rem;font-size:0.85rem;color:#dc2626;background:none;border:none;cursor:pointer;width:100%;text-align:left;transition:background 0.1s;"
                                    onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'"
                                >
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                        <polyline points="16,17 21,12 16,7"/>
                                        <line x1="21" y1="12" x2="9" y2="12"/>
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="mx-6 mt-4">
            <div class="alert alert-success animate-fade-in">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" flex-shrink="0" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20,6 9,17 4,12"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @endif
        @if(session('error'))
        <div class="mx-6 mt-4">
            <div class="alert alert-error animate-fade-in">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 p-6">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="px-6 py-3 border-t text-xs" style="border-color:var(--color-border);color:var(--color-text-muted);">
            &copy; {{ date('Y') }} SummitPass &mdash; Platform Keselamatan Pendakian Indonesia
        </footer>
    </div>
</div>

<script>
function webLayout() {
    return {
        collapsed: localStorage.getItem('sidebar-collapsed') === 'true',
        mobileOpen: false,
        profileOpen: false,
    }
}
</script>

</body>
</html>
