<x-layouts.web>
    <x-slot:title>Trekking Map</x-slot:title>
    <x-slot:breadcrumb>['Admin', 'Trekking Map']</x-slot:breadcrumb>

    {{-- Leaflet CSS --}}
    <x-slot:head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.css" crossorigin=""/>
        <style>
            #trekking-map {
                height: 520px;
                width: 100%;
                border-radius: 12px;
                z-index: 0;
            }
            .leaflet-popup-content-wrapper {
                border-radius: 10px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                padding: 0;
                overflow: hidden;
            }
            .leaflet-popup-content {
                margin: 0;
                min-width: 260px;
                max-width: 320px;
            }
            .popup-header {
                padding: 10px 14px 8px;
                color: white;
            }
            .popup-header .pos-name {
                font-size: 0.875rem;
                font-weight: 700;
                line-height: 1.2;
            }
            .popup-header .pos-meta {
                font-size: 0.7rem;
                opacity: 0.85;
                margin-top: 2px;
            }
            .popup-log-list {
                max-height: 220px;
                overflow-y: auto;
            }
            .popup-log-item {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 7px 14px;
                border-bottom: 1px solid #f1f5f9;
                font-size: 0.8rem;
            }
            .popup-log-item:last-child { border-bottom: none; }
            .dir-up   { color: #16a34a; font-weight: 700; font-size: 1rem; }
            .dir-down { color: #2563eb; font-weight: 700; font-size: 1rem; }
            .log-name  { font-weight: 600; color: #1e293b; flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .log-time  { font-size: 0.7rem; color: #94a3b8; flex-shrink: 0; }
            .log-anomaly { color: #ef4444; font-size: 0.68rem; font-weight: 600; }
            .popup-empty { padding: 14px; font-size: 0.8rem; color: #94a3b8; text-align: center; }

            #refresh-bar {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.75rem;
                color: var(--color-text-muted, #64748b);
            }
            #countdown-text { font-weight: 600; color: var(--color-forest-700, #2d6a4f); }

            .trail-checkbox-item {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 6px 10px;
                border-radius: 8px;
                cursor: pointer;
                transition: background 0.15s;
                user-select: none;
            }
            .trail-checkbox-item:hover { background: var(--color-forest-50, #f0fdf4); }
            .trail-color-dot {
                width: 12px;
                height: 12px;
                border-radius: 50%;
                flex-shrink: 0;
            }
            .trail-checkbox-item input[type=checkbox] { accent-color: var(--color-forest-600, #16a34a); }
        </style>
    </x-slot:head>

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-semibold" style="color:var(--color-text);">Trekking Map</h2>
            <p class="text-sm mt-0.5" style="color:var(--color-text-muted);">
                @if($pengelolaMode)
                    {{ $mountain?->name ?? 'Gunung belum di-assign' }} &mdash; posisi pos &amp; log aktivitas pendaki
                @else
                    Posisi titik pos jalur pendakian &amp; log aktivitas pendaki
                @endif
            </p>
        </div>
        <div id="refresh-bar">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23,4 23,10 17,10"/><polyline points="1,20 1,14 7,14"/>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
            </svg>
            Refresh otomatis: <span id="countdown-text">30:00</span>
            <button id="btn-refresh-now" class="btn btn-ghost btn-sm" style="font-size:0.72rem;padding:2px 8px;">Refresh Sekarang</button>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="card mb-4" style="padding:1rem 1.25rem;">

        @if($pengelolaMode)
        {{-- PENGELOLA: checkbox jalur langsung --}}
        <div>
            <div class="text-xs font-medium mb-2" style="color:var(--color-text-muted);">Tampilkan Jalur</div>
            @if($trails->isEmpty())
                <p class="text-sm" style="color:var(--color-text-muted);">Belum ada jalur aktif untuk gunung ini.</p>
            @else
            <div class="flex flex-wrap gap-1" id="trail-checkboxes">
                @foreach($trails as $trail)
                <label class="trail-checkbox-item">
                    <input type="checkbox" class="trail-cb" value="{{ $trail->id }}" checked>
                    <span class="trail-color-dot" data-trail-id="{{ $trail->id }}"></span>
                    <span style="font-size:0.85rem;font-weight:500;color:var(--color-text);">{{ $trail->name }}</span>
                </label>
                @endforeach
            </div>
            @endif
        </div>

        @else
        {{-- ADMIN: dropdown gunung dulu, lalu checkbox jalur --}}
        <div class="flex flex-wrap items-end gap-4">
            <div style="flex:0 0 220px;">
                <label class="block text-xs font-medium mb-1" style="color:var(--color-text-muted);">Gunung</label>
                <select id="sel-mountain" class="input" style="width:100%;">
                    <option value="">— Pilih Gunung —</option>
                    @foreach($mountains ?? [] as $mountain)
                        <option value="{{ $mountain->id }}">{{ $mountain->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:200px;" id="trail-checkbox-wrap" class="hidden">
                <div class="text-xs font-medium mb-2" style="color:var(--color-text-muted);">Tampilkan Jalur</div>
                <div class="flex flex-wrap gap-1" id="trail-checkboxes"></div>
            </div>
        </div>
        @endif

    </div>

    {{-- Map container --}}
    <div class="card" style="padding:1rem;">
        {{-- Legend --}}
        <div class="flex flex-wrap gap-3 mb-3" style="font-size:0.75rem;color:var(--color-text-muted);">
            <span class="flex items-center gap-1.5">
                <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#16a34a;"></span> Gate In
            </span>
            <span class="flex items-center gap-1.5">
                <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#0284c7;"></span> Pos
            </span>
            <span class="flex items-center gap-1.5">
                <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#d97706;"></span> Puncak
            </span>
            <span class="flex items-center gap-1.5">
                <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#6366f1;"></span> Gate Out
            </span>
            <span class="flex items-center gap-1.5 ml-2">
                <span style="color:#16a34a;font-weight:700;">▲</span> Naik
            </span>
            <span class="flex items-center gap-1.5">
                <span style="color:#2563eb;font-weight:700;">▼</span> Turun
            </span>
            <span class="flex items-center gap-1.5">
                <span style="color:#ef4444;font-weight:700;">⚠</span> Anomali
            </span>
        </div>

        <div id="trekking-map">
            <div id="map-placeholder" style="height:100%;display:flex;align-items:center;justify-content:center;background:#f8fafc;border-radius:12px;color:#94a3b8;font-size:0.875rem;">
                @if($pengelolaMode)
                    Pilih jalur di atas untuk menampilkan peta
                @else
                    Pilih gunung &amp; jalur untuk menampilkan peta
                @endif
            </div>
        </div>
    </div>

    {{-- Leaflet JS --}}
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js" crossorigin=""></script>

    <script>
    (function () {
        const REFRESH_INTERVAL = 30 * 60;

        // Warna per jalur — diassign secara urut
        const TRAIL_COLORS = ['#2d6a4f','#0284c7','#d97706','#7c3aed','#be123c','#0f766e'];

        // Map: trail_id → warna
        const trailColorMap = {};

        let map         = null;
        let layerGroups = {}; // trail_id → L.LayerGroup
        let countdownTimer = null;
        let refreshTimer   = null;
        let secondsLeft    = REFRESH_INTERVAL;

        const btnRefresh  = document.getElementById('btn-refresh-now');
        const countdownEl = document.getElementById('countdown-text');

        // ─── Assign warna ke setiap checkbox jalur ───────────────────
        function assignTrailColors() {
            document.querySelectorAll('.trail-cb').forEach((cb, idx) => {
                const color = TRAIL_COLORS[idx % TRAIL_COLORS.length];
                trailColorMap[cb.value] = color;
                const dot = document.querySelector(`.trail-color-dot[data-trail-id="${cb.value}"]`);
                if (dot) dot.style.background = color;
            });
        }

        // ─── Inisialisasi Leaflet map ─────────────────────────────────
        function initMap() {
            if (map) return;
            const placeholder = document.getElementById('map-placeholder');
            if (placeholder) placeholder.remove();

            map = L.map('trekking-map').setView([-2.5, 118.0], 5);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 18,
            }).addTo(map);
        }

        // ─── Warna pin checkpoint ─────────────────────────────────────
        const typeColor = {
            gate_in:  '#16a34a',
            pos:      '#0284c7',
            summit:   '#d97706',
            gate_out: '#6366f1',
        };

        function makeIcon(type, trailColor) {
            const pinColor = typeColor[type] || '#64748b';
            // Ring luar sesuai warna jalur, dot dalam sesuai tipe
            const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="36" viewBox="0 0 28 36">
                <path d="M14 0C6.27 0 0 6.27 0 14c0 10.5 14 22 14 22S28 24.5 28 14C28 6.27 21.73 0 14 0z"
                      fill="${pinColor}" stroke="${trailColor}" stroke-width="2.5"/>
                <circle cx="14" cy="14" r="6" fill="white"/>
            </svg>`;
            return L.divIcon({
                html: svg,
                iconSize: [28, 36],
                iconAnchor: [14, 36],
                popupAnchor: [0, -36],
                className: '',
            });
        }

        // ─── Build popup HTML ─────────────────────────────────────────
        function buildPopup(cp, trailName, trailColor) {
            const typeLabel = { gate_in: 'Gate In', pos: 'Pos', summit: 'Puncak', gate_out: 'Gate Out' };
            const altText   = cp.altitude ? ` &middot; ${cp.altitude} mdpl` : '';
            let html = `<div class="popup-header" style="background:${trailColor};">
                <div class="pos-name">${cp.name}</div>
                <div class="pos-meta">${typeLabel[cp.type] || cp.type}${altText} &mdash; ${trailName}</div>
            </div>`;

            if (cp.logs && cp.logs.length > 0) {
                html += `<div class="popup-log-list">`;
                cp.logs.forEach(log => {
                    const arrow   = log.direction === 'up' ? `<span class="dir-up">▲</span>` : `<span class="dir-down">▼</span>`;
                    const anomaly = log.anomaly ? `<span class="log-anomaly">⚠</span>` : '';
                    html += `<div class="popup-log-item">${arrow}<span class="log-name">${log.name}</span>${anomaly}<span class="log-time">${log.scanned_at}</span></div>`;
                });
                html += `</div>`;
            } else {
                html += `<div class="popup-empty">Belum ada log di pos ini</div>`;
            }
            return html;
        }

        // ─── Hapus layer satu jalur ───────────────────────────────────
        function clearTrailLayer(trailId) {
            if (layerGroups[trailId]) {
                layerGroups[trailId].clearLayers();
                if (map) map.removeLayer(layerGroups[trailId]);
                delete layerGroups[trailId];
            }
        }

        // ─── Render satu jalur ke map ─────────────────────────────────
        function renderTrail(trailData, color) {
            initMap();

            const trailId = trailData.trail_id;
            clearTrailLayer(trailId);

            const checkpoints = trailData.checkpoints || [];
            if (checkpoints.length === 0) return;

            const group   = L.layerGroup().addTo(map);
            layerGroups[trailId] = group;

            const latlngs = [];
            checkpoints.forEach(cp => {
                const latlng = [cp.lat, cp.lng];
                latlngs.push(latlng);
                L.marker(latlng, { icon: makeIcon(cp.type, color) })
                    .bindPopup(buildPopup(cp, trailData.trail_name, color), { maxWidth: 320 })
                    .addTo(group);
            });

            L.polyline(latlngs, {
                color: color,
                weight: 2.5,
                opacity: 0.65,
                dashArray: '6 4',
            }).addTo(group);
        }

        // ─── Fit bounds ke semua layer aktif ─────────────────────────
        function fitAllBounds() {
            if (!map) return;
            const allLatLngs = [];
            Object.values(layerGroups).forEach(group => {
                group.eachLayer(layer => {
                    if (layer.getLatLng) allLatLngs.push(layer.getLatLng());
                    if (layer.getLatLngs) layer.getLatLngs().forEach(ll => allLatLngs.push(ll));
                });
            });
            if (allLatLngs.length > 0) {
                map.fitBounds(L.latLngBounds(allLatLngs), { padding: [40, 40] });
            }
        }

        // ─── Load data untuk trail yang dicentang ────────────────────
        function loadCheckedTrails() {
            const checked = [...document.querySelectorAll('.trail-cb:checked')].map(cb => cb.value);

            // Hapus jalur yang tidak lagi dicentang
            Object.keys(layerGroups).forEach(id => {
                if (!checked.includes(id)) clearTrailLayer(id);
            });

            if (checked.length === 0) return;

            const params = checked.map(id => `trail_ids[]=${id}`).join('&');

            fetch(`{{ route('admin.trekking-map.data') }}?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                (data.trails || []).forEach(trailData => {
                    const color = trailColorMap[trailData.trail_id] || TRAIL_COLORS[0];
                    renderTrail(trailData, color);
                });
                fitAllBounds();
                resetCountdown();
            })
            .catch(() => alert('Gagal memuat data peta.'));
        }

        // ─── Countdown timer ──────────────────────────────────────────
        function formatTime(s) {
            const m   = Math.floor(s / 60).toString().padStart(2, '0');
            const sec = (s % 60).toString().padStart(2, '0');
            return `${m}:${sec}`;
        }

        function resetCountdown() {
            clearInterval(countdownTimer);
            clearTimeout(refreshTimer);
            secondsLeft = REFRESH_INTERVAL;
            countdownEl.textContent = formatTime(secondsLeft);

            countdownTimer = setInterval(() => {
                secondsLeft--;
                countdownEl.textContent = formatTime(secondsLeft);
                if (secondsLeft <= 0) clearInterval(countdownTimer);
            }, 1000);

            refreshTimer = setTimeout(loadCheckedTrails, REFRESH_INTERVAL * 1000);
        }

        // ─── Event: checkbox jalur berubah ────────────────────────────
        function bindCheckboxEvents() {
            document.querySelectorAll('.trail-cb').forEach(cb => {
                cb.addEventListener('change', function () {
                    if (!this.checked) {
                        clearTrailLayer(this.value);
                        fitAllBounds();
                    } else {
                        loadCheckedTrails();
                    }
                });
            });
        }

        @if($pengelolaMode)
        // ─── PENGELOLA: langsung assign warna & load semua jalur ─────
        assignTrailColors();
        bindCheckboxEvents();

        // Auto-load semua jalur yang sudah dicentang saat halaman dibuka
        if (document.querySelectorAll('.trail-cb').length > 0) {
            loadCheckedTrails();
        }

        @else
        // ─── ADMIN: dropdown gunung → render checkboxes ──────────────
        const selMountain       = document.getElementById('sel-mountain');
        const trailCheckboxWrap = document.getElementById('trail-checkbox-wrap');
        const trailCheckboxes   = document.getElementById('trail-checkboxes');

        selMountain.addEventListener('change', function () {
            const mountainId = this.value;
            trailCheckboxes.innerHTML = '';
            trailCheckboxWrap.classList.add('hidden');

            // Hapus semua layer yang ada
            Object.keys(layerGroups).forEach(id => clearTrailLayer(id));

            if (!mountainId) return;

            fetch(`{{ route('admin.trekking-map.trails', ['mountainId' => '__ID__']) }}`.replace('__ID__', mountainId), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                const trails = data.trails || [];
                if (trails.length === 0) return;

                trails.forEach((trail, idx) => {
                    const color = TRAIL_COLORS[idx % TRAIL_COLORS.length];
                    trailColorMap[trail.id] = color;

                    const label = document.createElement('label');
                    label.className = 'trail-checkbox-item';
                    label.innerHTML = `
                        <input type="checkbox" class="trail-cb" value="${trail.id}" checked>
                        <span class="trail-color-dot" style="background:${color};"></span>
                        <span style="font-size:0.85rem;font-weight:500;color:var(--color-text);">${trail.name}</span>
                    `;
                    trailCheckboxes.appendChild(label);
                });

                trailCheckboxWrap.classList.remove('hidden');
                bindCheckboxEvents();
                loadCheckedTrails();
            });
        });
        @endif

        // ─── Refresh manual ───────────────────────────────────────────
        btnRefresh.addEventListener('click', function () {
            loadCheckedTrails();
        });

    })();
    </script>

</x-layouts.web>
