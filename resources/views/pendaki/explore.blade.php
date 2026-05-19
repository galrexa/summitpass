<x-layouts.mobile>
    <x-slot:title>Jelajahi Gunung</x-slot:title>

    <x-slot:head>
    <style>
        .filter-strip { display:flex; gap:.5rem; padding:.75rem 1rem 0; overflow-x:auto; scrollbar-width:none; -webkit-overflow-scrolling:touch; }
        .filter-strip::-webkit-scrollbar { display:none; }
        .filter-pill { flex-shrink:0; padding:.35rem .875rem; border-radius:20px; font-size:.78rem; font-weight:600; border:1.5px solid var(--color-border); background:white; color:var(--color-text-muted); cursor:pointer; transition:all .15s; white-space:nowrap; }
        .filter-pill.active { background:var(--color-forest-100); color:var(--color-forest-800); border-color:var(--color-forest-300); }
        .mountain-grid { display:grid; grid-template-columns:1fr 1fr; gap:.875rem; padding:1rem; }
        @media(min-width:640px) { .mountain-grid { grid-template-columns:repeat(3,1fr); } }
        .m-card { border-radius:14px; overflow:hidden; background:white; border:1.5px solid var(--color-border); cursor:pointer; transition:transform .15s, box-shadow .15s; }
        .m-card:active { transform:scale(.97); }
        .m-card-hero { height:100px; position:relative; display:flex; align-items:flex-end; padding:.625rem .75rem; }
        .m-card-body { padding:.75rem; }
        .quota-bar { height:3px; border-radius:2px; background:#e5e7eb; overflow:hidden; margin-top:.5rem; }
        .quota-fill { height:100%; border-radius:2px; transition:width .3s; }
        /* Drawer */
        .drawer-overlay { position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:40; }
        .drawer-panel { position:fixed; bottom:0; left:0; right:0; background:white; border-radius:1.25rem 1.25rem 0 0; z-index:50; max-height:92vh; overflow-y:auto; overscroll-behavior:contain; }
        .drag-handle { width:36px; height:4px; border-radius:2px; background:#d1d5db; margin:12px auto 0; }
        .stat-chip { flex:1; min-width:0; text-align:center; padding:.5rem .25rem; background:#f9fafb; border-radius:8px; border:1px solid #e5e7eb; }
        .weather-card { flex:1; text-align:center; padding:.625rem .375rem; border-radius:10px; border:1px solid #e5e7eb; }
    </style>
    </x-slot:head>

    <div x-data="exploreApp()" x-init="init()">

        {{-- Search bar --}}
        <div style="padding:.75rem 1rem 0;">
            <div style="position:relative;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:absolute;left:.875rem;top:50%;transform:translateY(-50%);pointer-events:none;">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input
                    x-model="search"
                    type="search"
                    placeholder="Cari gunung atau lokasi…"
                    class="form-input"
                    style="padding-left:2.5rem;border-radius:12px;font-size:.875rem;"
                >
            </div>
        </div>

        {{-- Filter strip --}}
        <div class="filter-strip">
            @php
                $filters = [
                    ['all',      'Semua'],
                    ['pemula',   'Pemula'],
                    ['menengah', 'Menengah'],
                    ['lanjut',   'Lanjut'],
                    ['jawa',     'Jawa'],
                    ['sumatera', 'Sumatera'],
                    ['ntb',      'NTB'],
                    ['sulawesi', 'Sulawesi'],
                ];
            @endphp
            @foreach($filters as [$key, $label])
            <button
                @click="filter = '{{ $key }}'"
                :class="filter === '{{ $key }}' ? 'filter-pill active' : 'filter-pill'"
            >{{ $label }}</button>
            @endforeach
        </div>

        {{-- Mountain grid --}}
        <div class="mountain-grid">
            <template x-for="m in filtered" :key="m.id">
                <div class="m-card" @click="openDrawer(m.id)">
                    {{-- Hero --}}
                    <div class="m-card-hero" :style="`background:${heroGradient(m.grade)};`">
                        {{-- Mountain SVG silhouette --}}
                        <svg style="position:absolute;bottom:0;left:0;right:0;width:100%;height:60px;opacity:.2;" viewBox="0 0 200 60" preserveAspectRatio="none" fill="white">
                            <path d="M0,60 L40,20 L70,40 L100,5 L130,35 L160,15 L200,45 L200,60 Z"/>
                        </svg>
                        {{-- Height overlay --}}
                        <div style="position:absolute;top:.5rem;right:.5rem;background:rgba(0,0,0,.45);color:white;font-size:.65rem;font-weight:700;padding:.2rem .5rem;border-radius:20px;" x-text="m.height_mdpl.toLocaleString('id-ID') + ' mdpl'"></div>
                        {{-- Status badge --}}
                        <div style="position:absolute;top:.5rem;left:.5rem;">
                            <span
                                style="font-size:.6rem;font-weight:700;padding:.2rem .45rem;border-radius:20px;"
                                :style="trailStatusStyle(m.trail_status ?? 'open')"
                                x-text="trailStatusText(m.trail_status ?? 'open')"
                            ></span>
                        </div>
                    </div>

                    <div class="m-card-body">
                        {{-- Avail badge --}}
                        <div style="margin-bottom:.3rem;">
                            <span
                                style="font-size:.62rem;font-weight:700;padding:.15rem .45rem;border-radius:20px;"
                                :style="`background:${availBadge(m).bg};color:${availBadge(m).color};`"
                                x-text="availBadge(m).text"
                            ></span>
                        </div>
                        <div style="font-size:.82rem;font-weight:700;color:#111827;line-height:1.25;margin-bottom:.15rem;" x-text="m.name"></div>
                        <div style="font-size:.7rem;color:#6b7280;margin-bottom:.3rem;" x-text="m.location"></div>
                        {{-- Grade + duration --}}
                        <div style="display:flex;gap:.3rem;flex-wrap:wrap;margin-bottom:.375rem;">
                            <span style="font-size:.62rem;font-weight:600;padding:.15rem .45rem;border-radius:20px;" :style="`background:${gradeColor(m.grade)}22;color:${gradeColor(m.grade)};`" x-text="'Grade ' + m.grade"></span>
                            <span style="font-size:.62rem;font-weight:600;padding:.15rem .45rem;border-radius:20px;background:#f3f4f6;color:#374151;" x-text="(m.regulation?.max_hiking_days ?? '—') + ' hari maks'"></span>
                        </div>
                        {{-- Quota bar --}}
                        <div class="quota-bar">
                            <div class="quota-fill" :style="`width:${quotaPct(m)}%;background:${quotaPct(m) > 80 ? '#ef4444' : quotaPct(m) > 50 ? '#f59e0b' : '#22c55e'};`"></div>
                        </div>
                        <div style="font-size:.6rem;color:#9ca3af;margin-top:.2rem;" x-text="`${100 - quotaPct(m)}% slot tersisa minggu ini`"></div>
                    </div>
                </div>
            </template>

            <template x-if="filtered.length === 0">
                <div style="grid-column:1/-1;text-align:center;padding:3rem 1rem;color:#9ca3af;font-size:.875rem;">
                    Tidak ada gunung yang cocok dengan filter ini.
                </div>
            </template>
        </div>

        {{-- Drawer overlay --}}
        <div
            class="drawer-overlay"
            x-show="drawerOpen"
            @click="closeDrawer()"
            x-transition:enter="transition-opacity duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display:none;"
        ></div>

        {{-- Drawer panel --}}
        <div
            class="drawer-panel"
            x-show="drawerOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="transform translate-y-full"
            x-transition:enter-end="transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="transform translate-y-0"
            x-transition:leave-end="transform translate-y-full"
            style="display:none;"
        >
            <div class="drag-handle"></div>

            {{-- Loading --}}
            <template x-if="drawerLoading">
                <div style="padding:3rem;text-align:center;color:#9ca3af;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 1s linear infinite;margin:0 auto .75rem;display:block;"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    Memuat informasi gunung…
                </div>
            </template>

            {{-- Drawer content --}}
            <template x-if="!drawerLoading && mountain">
                <div style="padding:1.25rem 1.25rem 2rem;">

                    {{-- Name + grade --}}
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;margin-bottom:.5rem;">
                        <div>
                            <h2 style="font-size:1.15rem;font-weight:800;color:#111827;line-height:1.2;" x-text="mountain.name"></h2>
                            <div style="font-size:.8rem;color:#6b7280;margin-top:.2rem;" x-text="(mountain.location ?? '') + (mountain.province ? ' · ' + mountain.province : '')"></div>
                        </div>
                        <span style="flex-shrink:0;font-size:.75rem;font-weight:700;padding:.3rem .75rem;border-radius:20px;" :style="`background:${gradeColor(mountain.grade)}22;color:${gradeColor(mountain.grade)};`" x-text="'Grade ' + mountain.grade + ' · ' + gradeLabel(mountain.grade)"></span>
                    </div>

                    {{-- Status jalur banner --}}
                    <div style="padding:.625rem .875rem;border-radius:8px;font-size:.825rem;font-weight:600;margin-bottom:1rem;border:1px solid;"
                         :style="`background:${trailStatusBanner(mountain.trail_status ?? 'open').bg};border-color:${trailStatusBanner(mountain.trail_status ?? 'open').border};color:${trailStatusBanner(mountain.trail_status ?? 'open').color};`"
                         x-text="trailStatusBanner(mountain.trail_status ?? 'open').text">
                    </div>

                    {{-- Stat chips (4 kolom) --}}
                    <div style="display:flex;gap:.5rem;margin-bottom:1rem;">
                        <div class="stat-chip">
                            <div style="font-size:1rem;font-weight:800;color:#111827;" x-text="(mountain.height_mdpl ?? 0).toLocaleString('id-ID')"></div>
                            <div style="font-size:.65rem;color:#6b7280;margin-top:.1rem;">mdpl</div>
                        </div>
                        <div class="stat-chip">
                            <div style="font-size:1rem;font-weight:800;color:#111827;" x-text="(mountain.regulation?.max_hiking_days ?? '—') + ' hr'"></div>
                            <div style="font-size:.65rem;color:#6b7280;margin-top:.1rem;">Maks hari</div>
                        </div>
                        <div class="stat-chip">
                            <div style="font-size:1rem;font-weight:800;color:#16a34a;" x-text="drawerSlot !== null ? drawerSlot + ' slot' : '—'"></div>
                            <div style="font-size:.65rem;color:#6b7280;margin-top:.1rem;">Minggu ini</div>
                        </div>
                        <div class="stat-chip">
                            <div style="font-size:1rem;font-weight:800;color:#111827;">17+</div>
                            <div style="font-size:.65rem;color:#6b7280;margin-top:.1rem;">Min usia</div>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <template x-if="mountain.description">
                        <div style="margin-bottom:1rem;">
                            <div style="font-size:.78rem;font-weight:700;color:#374151;margin-bottom:.35rem;">Tentang gunung ini</div>
                            <div style="font-size:.82rem;color:#4b5563;line-height:1.65;" x-text="mountain.description"></div>
                        </div>
                    </template>

                    {{-- Ekosistem --}}
                    <template x-if="mountain.ecosystem_type">
                        <div style="display:flex;align-items:center;gap:.5rem;padding:.625rem .875rem;background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;margin-bottom:1rem;font-size:.8rem;color:#166534;">
                            <span>🌲</span>
                            <span x-text="mountain.ecosystem_type"></span>
                        </div>
                    </template>

                    {{-- Prakiraan cuaca --}}
                    <div style="margin-bottom:1rem;">
                        <div style="font-size:.78rem;font-weight:700;color:#374151;margin-bottom:.5rem;">Prakiraan cuaca di puncak</div>
                        <template x-if="weather && weather.length">
                            <div style="display:flex;gap:.5rem;">
                                <template x-for="w in weather" :key="w.day">
                                    <div class="weather-card">
                                        <div style="font-size:.65rem;color:#9ca3af;margin-bottom:.25rem;" x-text="w.day"></div>
                                        <div style="font-size:1.3rem;line-height:1;margin-bottom:.25rem;" x-text="weatherIcon(w.icon)"></div>
                                        <div style="font-size:.9rem;font-weight:700;color:#111827;" x-text="w.temp_c + '°C'"></div>
                                        <div style="font-size:.62rem;color:#6b7280;margin-top:.15rem;" x-text="w.condition"></div>
                                        <div style="font-size:.6rem;color:#9ca3af;margin-top:.1rem;" x-text="'💨 ' + w.wind_kmh + ' km/h'"></div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!weather || !weather.length">
                            <div style="font-size:.8rem;color:#9ca3af;text-align:center;padding:.75rem;">Data cuaca tidak tersedia</div>
                        </template>
                    </div>

                    {{-- Jalur tersedia --}}
                    <template x-if="mountain.trails && mountain.trails.length">
                        <div style="margin-bottom:1.25rem;">
                            <div style="font-size:.78rem;font-weight:700;color:#374151;margin-bottom:.5rem;">Jalur tersedia</div>
                            <div style="display:flex;flex-wrap:wrap;gap:.375rem;">
                                <template x-for="trail in mountain.trails" :key="trail.id">
                                    <span style="font-size:.78rem;font-weight:600;padding:.3rem .75rem;border-radius:20px;background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;" x-text="trail.name"></span>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Harga SIMAKSI --}}
                    <template x-if="mountain.regulation">
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:.75rem 1rem;background:#f9fafb;border-radius:8px;margin-bottom:1.25rem;border:1px solid #e5e7eb;">
                            <div>
                                <div style="font-size:.7rem;color:#6b7280;">Biaya SIMAKSI per orang</div>
                                <div style="font-size:1rem;font-weight:800;color:#166534;" x-text="'Rp ' + (mountain.regulation.base_price ?? 0).toLocaleString('id-ID')"></div>
                            </div>
                            <div style="font-size:.72rem;color:#6b7280;" x-text="'Kuota: ' + (mountain.regulation.quota_per_trail_per_day ?? '—') + '/hari'"></div>
                        </div>
                    </template>

                    {{-- CTA button --}}
                    <a
                        :href="`/my/bookings/create?mountain_id=${mountain.id}`"
                        :class="(mountain.trail_status === 'closed') ? 'btn btn-sm w-full' : 'btn btn-primary w-full'"
                        :style="mountain.trail_status === 'closed' ? 'opacity:.5;pointer-events:none;background:#e5e7eb;color:#6b7280;border:none;' : ''"
                        style="display:flex;align-items:center;justify-content:center;gap:.5rem;font-size:.925rem;padding:.875rem;"
                    >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                        <span x-text="mountain.trail_status === 'closed' ? 'Jalur Ditutup' : 'Booking ' + mountain.name"></span>
                    </a>

                </div>
            </template>
        </div>

    </div>

    <style>
    @keyframes spin { to { transform:rotate(360deg); } }
    </style>

    <script>
    function exploreApp() {
        return {
            filter: 'all',
            search: '',
            drawerOpen: false,
            drawerLoading: false,
            mountain: null,
            weather: null,
            drawerSlot: null,
            mountains: @json($mountains),

            init() {},

            get filtered() {
                return this.mountains.filter(m => {
                    const q = this.search.toLowerCase();
                    const matchSearch = !q ||
                        (m.name ?? '').toLowerCase().includes(q) ||
                        (m.location ?? '').toLowerCase().includes(q) ||
                        (m.province ?? '').toLowerCase().includes(q);
                    if (!matchSearch) return false;

                    if (this.filter === 'all')      return true;
                    if (this.filter === 'pemula')   return ['I','II'].includes(m.grade);
                    if (this.filter === 'menengah') return m.grade === 'III';
                    if (this.filter === 'lanjut')   return ['IV','V'].includes(m.grade);

                    // province-based filters
                    const prov = (m.province ?? '').toLowerCase();
                    const loc  = (m.location ?? '').toLowerCase();
                    if (this.filter === 'jawa')     return prov.includes('jawa') || loc.includes('jawa');
                    if (this.filter === 'sumatera') return prov.includes('sumatera') || loc.includes('sumatera');
                    if (this.filter === 'ntb')      return prov.includes('nusa tenggara barat') || prov.includes('ntb') || loc.includes('lombok');
                    if (this.filter === 'sulawesi') return prov.includes('sulawesi') || loc.includes('sulawesi');
                    return true;
                });
            },

            gradeLabel(grade) {
                if (['I','II'].includes(grade)) return 'Pemula';
                if (grade === 'III') return 'Menengah';
                return 'Lanjut';
            },

            gradeColor(grade) {
                if (['I','II'].includes(grade)) return '#16a34a';
                if (grade === 'III') return '#d97706';
                return '#dc2626';
            },

            heroGradient(grade) {
                const g = {
                    'I':  'linear-gradient(160deg,#166534 0%,#14532d 100%)',
                    'II': 'linear-gradient(160deg,#166534 0%,#14532d 100%)',
                    'III':'linear-gradient(160deg,#78350f 0%,#451a03 100%)',
                    'IV': 'linear-gradient(160deg,#7f1d1d 0%,#450a0a 100%)',
                    'V':  'linear-gradient(160deg,#450a0a 0%,#1c0808 100%)',
                };
                return g[grade] ?? 'linear-gradient(160deg,#1e3a5f 0%,#0f172a 100%)';
            },

            quotaPct(m) {
                const quota      = m.regulation?.quota_per_trail_per_day ?? 50;
                const trailCount = Math.max(1, m.trails?.length ?? 1);
                const total      = quota * 7 * trailCount;
                return Math.min(100, Math.round((m.booked_this_week ?? 0) / total * 100));
            },

            availBadge(m) {
                const pct = this.quotaPct(m);
                if (pct < 50) return { text: '✓ Tersedia',     bg: '#dcfce7', color: '#15803d' };
                if (pct < 90) return { text: '⚠ Hampir penuh', bg: '#fef3c7', color: '#b45309' };
                return           { text: '✕ Terbatas',         bg: '#fee2e2', color: '#dc2626' };
            },

            trailStatusText(s) {
                return { open:'Jalur Buka', caution:'Perhatian', closed:'Jalur Tutup' }[s] ?? 'Jalur Buka';
            },

            trailStatusStyle(s) {
                const m = {
                    open:    'background:#dcfce7;color:#166534;',
                    caution: 'background:#fef3c7;color:#92400e;',
                    closed:  'background:#fee2e2;color:#991b1b;',
                };
                return m[s] ?? m.open;
            },

            trailStatusBanner(s) {
                return {
                    open:    { text:'✓ Jalur terbuka',               bg:'#f0fdf4', border:'#bbf7d0', color:'#166534' },
                    caution: { text:'⚠ Perhatikan kondisi jalur',     bg:'#fef9c3', border:'#fde047', color:'#713f12' },
                    closed:  { text:'✕ Jalur ditutup sementara',      bg:'#fef2f2', border:'#fecaca', color:'#991b1b' },
                }[s] ?? { text:'✓ Jalur terbuka', bg:'#f0fdf4', border:'#bbf7d0', color:'#166534' };
            },

            weatherIcon(icon) {
                return { rain:'🌧', cloud:'☁️', sun:'☀️', thunder:'⛈', 'partly-cloudy':'⛅' }[icon] ?? '🌤';
            },

            async openDrawer(id) {
                this.drawerOpen   = true;
                this.drawerLoading = true;
                this.mountain     = null;
                this.weather      = null;
                this.drawerSlot   = null;

                try {
                    const [mRes, wRes] = await Promise.all([
                        fetch(`/api/mountains/${id}/detail`),
                        fetch(`/api/mountains/${id}/weather`),
                    ]);
                    const mData = await mRes.json();
                    const wData = await wRes.json();
                    this.mountain   = mData.mountain;
                    this.drawerSlot = mData.slot_remaining ?? null;
                    this.weather    = wData.forecast ?? [];
                } catch(e) {
                    this.drawerOpen = false;
                } finally {
                    this.drawerLoading = false;
                }
            },

            closeDrawer() {
                this.drawerOpen = false;
                setTimeout(() => {
                    this.mountain     = null;
                    this.weather      = null;
                    this.drawerSlot   = null;
                }, 250);
            },
        }
    }
    </script>

</x-layouts.mobile>
