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
                background: var(--color-forest-700, #2d6a4f);
                color: white;
            }
            .popup-header .pos-name {
                font-size: 0.875rem;
                font-weight: 700;
                line-height: 1.2;
            }
            .popup-header .pos-meta {
                font-size: 0.7rem;
                opacity: 0.8;
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

            .pin-gate_in  { background: #16a34a; }
            .pin-pos      { background: #0284c7; }
            .pin-summit   { background: #d97706; }
            .pin-gate_out { background: #6366f1; }

            #refresh-bar {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.75rem;
                color: var(--color-text-muted, #64748b);
            }
            #countdown-text { font-weight: 600; color: var(--color-forest-700, #2d6a4f); }
        </style>
    </x-slot:head>

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-semibold" style="color:var(--color-text);">Trekking Map</h2>
            <p class="text-sm mt-0.5" style="color:var(--color-text-muted);">
                Posisi titik pos jalur pendakian &amp; log aktivitas pendaki
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
        <div class="flex flex-wrap items-end gap-4">
            <div style="flex:1;min-width:180px;">
                <label class="block text-xs font-medium mb-1" style="color:var(--color-text-muted);">Gunung</label>
                <select id="sel-mountain" class="input" style="width:100%;">
                    <option value="">— Pilih Gunung —</option>
                    @foreach($mountains as $mountain)
                        <option value="{{ $mountain->id }}">{{ $mountain->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:180px;">
                <label class="block text-xs font-medium mb-1" style="color:var(--color-text-muted);">Jalur / Trail</label>
                <select id="sel-trail" class="input" style="width:100%;" disabled>
                    <option value="">— Pilih Jalur —</option>
                </select>
            </div>
            <div>
                <button id="btn-load-map" class="btn btn-primary btn-sm" disabled style="gap:0.5rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="3,11 22,2 13,21 11,13 3,11"/>
                    </svg>
                    Tampilkan Peta
                </button>
            </div>
        </div>
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
            <div style="height:100%;display:flex;align-items:center;justify-content:center;background:#f8fafc;border-radius:12px;color:#94a3b8;font-size:0.875rem;">
                Pilih gunung &amp; jalur, lalu klik "Tampilkan Peta"
            </div>
        </div>
    </div>

    {{-- Leaflet JS --}}
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js" crossorigin=""></script>

    <script>
    (function () {
        const REFRESH_INTERVAL = 30 * 60; // 30 menit dalam detik

        let map = null;
        let markers = [];
        let polyline = null;
        let countdownTimer = null;
        let refreshTimer = null;
        let secondsLeft = REFRESH_INTERVAL;
        let currentTrailId = null;

        const selMountain = document.getElementById('sel-mountain');
        const selTrail    = document.getElementById('sel-trail');
        const btnLoad     = document.getElementById('btn-load-map');
        const btnRefresh  = document.getElementById('btn-refresh-now');
        const countdownEl = document.getElementById('countdown-text');

        // ─── Inisialisasi Leaflet map ─────────────────────────────────
        function initMap() {
            if (map) return;
            const placeholder = document.querySelector('#trekking-map > div');
            if (placeholder) placeholder.remove();

            map = L.map('trekking-map').setView([-2.5, 118.0], 5);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 18,
            }).addTo(map);
        }

        // ─── Warna & ikon per tipe checkpoint ────────────────────────
        const typeColor = {
            gate_in:  '#16a34a',
            pos:      '#0284c7',
            summit:   '#d97706',
            gate_out: '#6366f1',
        };

        function makeIcon(type) {
            const color = typeColor[type] || '#64748b';
            const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="36" viewBox="0 0 28 36">
                <path d="M14 0C6.27 0 0 6.27 0 14c0 10.5 14 22 14 22S28 24.5 28 14C28 6.27 21.73 0 14 0z" fill="${color}" stroke="white" stroke-width="1.5"/>
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
        function buildPopup(cp) {
            const typeLabel = {
                gate_in: 'Gate In', pos: 'Pos', summit: 'Puncak', gate_out: 'Gate Out'
            };
            const altText = cp.altitude ? ` &middot; ${cp.altitude} mdpl` : '';
            let html = `<div class="popup-header">
                <div class="pos-name">${cp.name}</div>
                <div class="pos-meta">${typeLabel[cp.type] || cp.type}${altText}</div>
            </div>`;

            if (cp.logs && cp.logs.length > 0) {
                html += `<div class="popup-log-list">`;
                cp.logs.forEach(log => {
                    const arrow = log.direction === 'up'
                        ? `<span class="dir-up">▲</span>`
                        : `<span class="dir-down">▼</span>`;
                    const anomaly = log.anomaly
                        ? `<span class="log-anomaly">⚠</span>`
                        : '';
                    html += `<div class="popup-log-item">
                        ${arrow}
                        <span class="log-name">${log.name}</span>
                        ${anomaly}
                        <span class="log-time">${log.scanned_at}</span>
                    </div>`;
                });
                html += `</div>`;
            } else {
                html += `<div class="popup-empty">Belum ada log di pos ini</div>`;
            }

            return html;
        }

        // ─── Load & render checkpoints ────────────────────────────────
        function loadMapData() {
            if (!currentTrailId) return;

            fetch(`{{ route('admin.trekking-map.data') }}?trail_id=${currentTrailId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                initMap();

                // Hapus marker & polyline lama
                markers.forEach(m => m.remove());
                markers = [];
                if (polyline) { polyline.remove(); polyline = null; }

                const checkpoints = data.checkpoints || [];
                if (checkpoints.length === 0) {
                    alert('Tidak ada checkpoint dengan koordinat pada jalur ini.');
                    return;
                }

                const latlngs = [];

                checkpoints.forEach(cp => {
                    const latlng = [cp.lat, cp.lng];
                    latlngs.push(latlng);

                    const marker = L.marker(latlng, { icon: makeIcon(cp.type) })
                        .bindPopup(buildPopup(cp), { maxWidth: 320 })
                        .addTo(map);

                    markers.push(marker);
                });

                // Garis penghubung antar pos (urut berdasarkan order_seq)
                polyline = L.polyline(latlngs, {
                    color: '#2d6a4f',
                    weight: 2.5,
                    opacity: 0.6,
                    dashArray: '6 4',
                }).addTo(map);

                // Fit bounds ke semua checkpoint
                map.fitBounds(L.latLngBounds(latlngs), { padding: [40, 40] });

                // Reset countdown
                resetCountdown();
            })
            .catch(() => alert('Gagal memuat data peta.'));
        }

        // ─── Countdown timer ──────────────────────────────────────────
        function formatTime(s) {
            const m = Math.floor(s / 60).toString().padStart(2, '0');
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
                if (secondsLeft <= 0) {
                    clearInterval(countdownTimer);
                }
            }, 1000);

            refreshTimer = setTimeout(() => {
                loadMapData();
            }, REFRESH_INTERVAL * 1000);
        }

        // ─── Event listeners ──────────────────────────────────────────

        // Pilih gunung → load trail
        selMountain.addEventListener('change', function () {
            const mountainId = this.value;
            selTrail.innerHTML = '<option value="">— Pilih Jalur —</option>';
            selTrail.disabled = true;
            btnLoad.disabled = true;

            if (!mountainId) return;

            fetch(`{{ route('admin.trekking-map.trails', ['mountainId' => '__ID__']) }}`.replace('__ID__', mountainId), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                (data.trails || []).forEach(trail => {
                    const opt = document.createElement('option');
                    opt.value = trail.id;
                    opt.textContent = trail.name;
                    selTrail.appendChild(opt);
                });
                selTrail.disabled = false;
            });
        });

        // Pilih trail → aktifkan tombol
        selTrail.addEventListener('change', function () {
            btnLoad.disabled = !this.value;
        });

        // Tombol tampilkan peta
        btnLoad.addEventListener('click', function () {
            currentTrailId = selTrail.value;
            if (currentTrailId) loadMapData();
        });

        // Tombol refresh manual
        btnRefresh.addEventListener('click', function () {
            if (currentTrailId) loadMapData();
        });

    })();
    </script>

</x-layouts.web>
