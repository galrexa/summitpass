<x-layouts.web>
    <x-slot:title>Simulasi Scan Pos</x-slot:title>
    <x-slot:breadcrumb>['Admin', 'Simulasi Scan Pos']</x-slot:breadcrumb>

    <div style="max-width:680px;" x-data="scanSim()" x-init="init()">

        {{-- Step 1: Input token --}}
        <div class="card" style="margin-bottom:1.25rem;">
            <h3 style="font-size:.875rem;font-weight:700;color:var(--color-text);margin-bottom:.25rem;">Scan QR SummitPass</h3>
            <p style="font-size:.78rem;color:var(--color-text-muted);margin-bottom:1rem;">Masukkan token dari QR Code pendaki, lalu pilih pos dan arah pendakian.</p>

            <div style="display:flex;gap:.75rem;align-items:flex-start;">
                <div style="flex:1;">
                    <input
                        x-model="token"
                        @keydown.enter.prevent="resolve()"
                        type="text"
                        class="form-input"
                        placeholder="Tempel token QR di sini…"
                        style="font-family:monospace;font-size:.8rem;"
                        :disabled="loading"
                    >
                </div>
                <button @click="resolve()" :disabled="loading || !token.trim()"
                        class="btn btn-primary btn-sm" style="flex-shrink:0;height:38px;">
                    <template x-if="loading">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 1s linear infinite;"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    </template>
                    <template x-if="!loading">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9,18 15,12 9,6"/></svg>
                    </template>
                    <span x-text="loading ? 'Memuat…' : 'Cari'"></span>
                </button>
            </div>

            {{-- Error --}}
            <div x-show="error" style="margin-top:.75rem;padding:.625rem .875rem;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;font-size:.8rem;color:#dc2626;" x-text="error"></div>
        </div>

        {{-- Step 2: Booking info + checkpoint list --}}
        <template x-if="pass">
            <div>
                {{-- Pendaki info card --}}
                <div style="border:2px solid var(--color-forest-300);border-radius:12px;overflow:hidden;margin-bottom:1.25rem;">
                    <div style="background:var(--color-forest-700);padding:.75rem 1.125rem;display:flex;align-items:center;justify-content:space-between;">
                        <div>
                            <div style="font-size:.65rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--color-forest-300);">SummitPass Terverifikasi</div>
                            <div style="font-size:.95rem;font-weight:700;color:white;margin-top:.1rem;" x-text="pass.participant.name"></div>
                            <div style="font-size:.75rem;color:var(--color-forest-300);margin-top:.1rem;">
                                <span x-text="pass.booking.mountain"></span> —
                                <span x-text="pass.booking.trail"></span>
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <span style="font-size:.68rem;font-weight:700;padding:.3rem .65rem;border-radius:20px;"
                                  :style="pass.status === 'active' ? 'background:#22c55e;color:white;' : 'background:#f59e0b;color:white;'"
                                  x-text="pass.status === 'active' ? 'AKTIF' : pass.status.toUpperCase()"></span>
                            <div style="font-size:.72rem;color:var(--color-forest-300);margin-top:.3rem;">
                                <span x-text="pass.booking.start_date"></span> →
                                <span x-text="pass.booking.end_date"></span>
                            </div>
                        </div>
                    </div>
                    <div style="padding:.75rem 1.125rem;background:#f9fafb;display:flex;gap:2rem;flex-wrap:wrap;">
                        <div>
                            <div style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);">Kode Booking</div>
                            <div style="font-size:.85rem;font-weight:700;font-family:monospace;color:var(--color-forest-700);" x-text="pass.booking.code"></div>
                        </div>
                        <div>
                            <div style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);">Peran</div>
                            <div style="font-size:.85rem;font-weight:600;color:var(--color-text);text-transform:capitalize;" x-text="pass.participant.role"></div>
                        </div>
                        <div>
                            <div style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);">Status Booking</div>
                            <div style="font-size:.85rem;font-weight:600;color:var(--color-text);text-transform:capitalize;" x-text="pass.booking.status.replace('_',' ')"></div>
                        </div>
                    </div>
                </div>

                {{-- Direction toggle --}}
                <div style="display:flex;gap:.625rem;margin-bottom:1.25rem;align-items:center;">
                    <span style="font-size:.82rem;font-weight:600;color:var(--color-text);flex-shrink:0;">Arah:</span>
                    <button @click="direction='up'"
                            style="flex:1;padding:.5rem;border-radius:8px;border:2px solid;font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;"
                            :style="direction==='up' ? 'border-color:#166534;background:#166534;color:white;' : 'border-color:var(--color-border);background:white;color:var(--color-text-muted);'">
                        ↑ Naik
                    </button>
                    <button @click="direction='down'"
                            style="flex:1;padding:.5rem;border-radius:8px;border:2px solid;font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;"
                            :style="direction==='down' ? 'border-color:#dc2626;background:#dc2626;color:white;' : 'border-color:var(--color-border);background:white;color:var(--color-text-muted);'">
                        ↓ Turun
                    </button>
                </div>

                {{-- Checkpoint list --}}
                <div class="card">
                    <h3 style="font-size:.82rem;font-weight:700;color:var(--color-text);margin-bottom:.875rem;">Pilih Pos Check-in</h3>
                    <div style="display:flex;flex-direction:column;gap:.5rem;">
                        <template x-for="cp in pass.checkpoints" :key="cp.id">
                            <div
                                @click="recordScan(cp)"
                                style="display:flex;align-items:center;gap:.875rem;padding:.75rem 1rem;border-radius:10px;border:2px solid;transition:all .15s;position:relative;"
                                :style="isScannedDir(cp)
                                    ? 'border-color:#bbf7d0;background:#f0fdf4;cursor:default;'
                                    : 'border-color:var(--color-border);background:white;cursor:pointer;'"
                                x-on:mouseenter="if(!isScannedDir(cp)) $el.style.borderColor='#166534'"
                                x-on:mouseleave="if(!isScannedDir(cp)) $el.style.borderColor='var(--color-border)'"
                            >
                                {{-- Order / status circle --}}
                                <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:800;flex-shrink:0;"
                                     :style="(cp.scanned_up && cp.scanned_down)
                                         ? 'background:#166534;color:white;'
                                         : (cp.scanned_up || cp.scanned_down)
                                             ? 'background:#f59e0b;color:white;'
                                             : 'background:#f3f4f6;color:#6b7280;'">
                                    <template x-if="cp.scanned_up && cp.scanned_down">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg>
                                    </template>
                                    <template x-if="!(cp.scanned_up && cp.scanned_down)">
                                        <span x-text="cp.order"></span>
                                    </template>
                                </div>

                                {{-- Info --}}
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:.875rem;font-weight:600;color:var(--color-text);" x-text="cp.name"></div>
                                    <div style="font-size:.72rem;color:var(--color-text-muted);margin-top:.1rem;">
                                        <span x-text="{'gate_in':'Pintu Masuk','gate_out':'Pintu Keluar','pos':'Pos','summit':'Puncak'}[cp.type] || cp.type"></span>
                                        <template x-if="cp.altitude">
                                            <span> · <span x-text="cp.altitude.toLocaleString('id-ID')"></span> mdpl</span>
                                        </template>
                                    </div>
                                    {{-- Badge status naik/turun --}}
                                    <div style="display:flex;gap:.3rem;margin-top:.3rem;flex-wrap:wrap;">
                                        <span style="font-size:.65rem;font-weight:700;padding:.1rem .45rem;border-radius:20px;"
                                              :style="cp.scanned_up ? 'background:#dcfce7;color:#166534;' : 'background:#f3f4f6;color:#9ca3af;'">
                                            ↑ Naik
                                        </span>
                                        <span style="font-size:.65rem;font-weight:700;padding:.1rem .45rem;border-radius:20px;"
                                              :style="cp.scanned_down ? 'background:#fee2e2;color:#dc2626;' : 'background:#f3f4f6;color:#9ca3af;'">
                                            ↓ Turun
                                        </span>
                                    </div>
                                </div>

                                {{-- Right: aksi --}}
                                <div style="flex-shrink:0;text-align:right;">
                                    <template x-if="isScannedDir(cp)">
                                        <span style="font-size:.7rem;font-weight:700;"
                                              :style="direction==='up' ? 'color:#166534;' : 'color:#dc2626;'">
                                            Sudah Scan <span x-text="direction==='up' ? '↑' : '↓'"></span>
                                        </span>
                                    </template>
                                    <template x-if="!isScannedDir(cp)">
                                        <span style="font-size:.72rem;font-weight:600;padding:.25rem .625rem;border-radius:20px;"
                                              :style="direction==='up'
                                                  ? 'background:#dcfce7;color:#166534;'
                                                  : 'background:#fee2e2;color:#dc2626;'">
                                            Tap untuk scan <span x-text="direction==='up' ? '↑' : '↓'"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Scan log (this session) --}}
                <template x-if="scanLog.length > 0">
                    <div class="card" style="margin-top:1.25rem;">
                        <h3 style="font-size:.82rem;font-weight:700;color:var(--color-text);margin-bottom:.75rem;">Log Sesi Ini</h3>
                        <div style="display:flex;flex-direction:column;gap:.375rem;">
                            <template x-for="(entry, i) in scanLog" :key="i">
                                <div style="display:flex;align-items:center;gap:.75rem;padding:.5rem .75rem;background:#f9fafb;border-radius:8px;font-size:.8rem;">
                                    <div style="width:8px;height:8px;border-radius:50%;flex-shrink:0;"
                                         :style="entry.direction==='up' ? 'background:#22c55e;' : 'background:#ef4444;'"></div>
                                    <span style="font-weight:600;flex:1;" x-text="entry.checkpoint"></span>
                                    <span :style="entry.direction==='up' ? 'color:#16a34a;' : 'color:#dc2626;'"
                                          x-text="entry.direction==='up' ? '↑ Naik' : '↓ Turun'"></span>
                                    <span style="color:var(--color-text-muted);" x-text="entry.scanned_at"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Reset --}}
                <div style="margin-top:1rem;text-align:right;">
                    <button @click="resetSim()" class="btn btn-ghost btn-sm">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="1,4 1,10 7,10"/><path d="M3.51 15a9 9 0 1 0 .49-4.91"/></svg>
                        Scan QR lain
                    </button>
                </div>
            </div>
        </template>

    </div>

    <style>
    @keyframes spin { to { transform: rotate(360deg); } }
    </style>

    <script>
    function scanSim() {
        return {
            token: '',
            loading: false,
            error: '',
            pass: null,
            direction: 'up',
            scanLog: [],

            init() {},

            async resolve() {
                this.error = '';
                this.pass  = null;
                if (!this.token.trim()) return;
                this.loading = true;
                try {
                    const res = await fetch('{{ route("admin.simulate.resolve") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ token: this.token.trim() }),
                    });
                    const data = await res.json();
                    if (!res.ok) { this.error = data.error || 'Terjadi kesalahan.'; return; }
                    this.pass     = data;
                    this.scanLog  = [];
                    this.direction = 'up';
                } catch (e) {
                    this.error = 'Gagal menghubungi server.';
                } finally {
                    this.loading = false;
                }
            },

            // Cek apakah checkpoint sudah di-scan untuk arah yang sedang aktif
            isScannedDir(cp) {
                return this.direction === 'up' ? cp.scanned_up : cp.scanned_down;
            },

            async recordScan(cp) {
                if (this.isScannedDir(cp)) return;
                try {
                    const res = await fetch('{{ route("admin.simulate.record") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({
                            qr_pass_id:          this.pass.qr_pass_id,
                            trail_checkpoint_id: cp.id,
                            direction:           this.direction,
                        }),
                    });
                    const data = await res.json();
                    if (!res.ok) { alert(data.message || 'Gagal merekam scan.'); return; }

                    // Update status naik/turun di checkpoint
                    if (this.direction === 'up')   cp.scanned_up   = true;
                    if (this.direction === 'down')  cp.scanned_down = true;

                    // Update status booking & QR pass di tampilan
                    if (data.booking_status) this.pass.booking.status = data.booking_status;
                    if (data.qr_status)      this.pass.status          = data.qr_status;

                    // Add to session log
                    this.scanLog.unshift({
                        checkpoint: data.checkpoint,
                        direction:  data.direction,
                        scanned_at: data.scanned_at,
                    });
                } catch (e) {
                    alert('Gagal menghubungi server.');
                }
            },

            resetSim() {
                this.token    = '';
                this.pass     = null;
                this.error    = '';
                this.scanLog  = [];
                this.direction = 'up';
            },
        }
    }
    </script>

</x-layouts.web>
