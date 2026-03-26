<x-layouts.web>
    <x-slot:title>Booking Baru</x-slot:title>
    <x-slot:breadcrumb>['SummitPass', 'Booking Saya', 'Booking Baru']</x-slot:breadcrumb>

    <x-slot:head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
        <style>
            .flatpickr-calendar.inline { width:100% !important; box-shadow:none; border:none; }
            .flatpickr-calendar { font-family:inherit; border-radius:10px; box-shadow:0 4px 20px rgba(0,0,0,.1); border:1px solid var(--color-border); }
            .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange { background:#166534 !important; border-color:#166534 !important; }
            .flatpickr-day.inRange { background:#dcfce7 !important; border-color:#dcfce7 !important; color:#166534 !important; box-shadow:-5px 0 0 #dcfce7, 5px 0 0 #dcfce7; }
            .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover { background:#14532d !important; border-color:#14532d !important; }
            .flatpickr-day:hover:not(.selected):not(.inRange) { background:#f0fdf4; border-color:#bbf7d0; }
            .flatpickr-months .flatpickr-month { background:#166534; color:white; border-radius:10px 10px 0 0; }
            .flatpickr-current-month .flatpickr-monthDropdown-months { background:#166534; }
            .flatpickr-current-month input.cur-year { color:white; }
            .flatpickr-current-month .flatpickr-monthDropdown-months:hover { background:#14532d; }
            .flatpickr-months .flatpickr-prev-month, .flatpickr-months .flatpickr-next-month { color:white !important; fill:white !important; }
            .flatpickr-months .flatpickr-prev-month:hover svg, .flatpickr-months .flatpickr-next-month:hover svg { fill:white !important; }
            .flatpickr-day.flatpickr-disabled { color:#d1d5db !important; }
        </style>
    </x-slot:head>

    <div
        x-data="bookingForm()"
        x-init="init()"
        style="max-width:760px;"
    >

        {{-- Step indicator --}}
        <div style="display:flex;align-items:center;gap:0;margin-bottom:2rem;">
            @foreach([['1','Pilih Gunung'],['2','Jalur & Tanggal'],['3','Peserta'],['4','Review']] as [$num,$label])
            <div style="display:flex;align-items:center;flex:1;">
                <div style="display:flex;flex-direction:column;align-items:center;gap:.3rem;flex-shrink:0;">
                    <div
                        style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.78rem;font-weight:700;transition:all .2s;"
                        :style="step > {{ $num }} ? 'background:var(--color-forest-600);color:#fff;' : (step == {{ $num }} ? 'background:var(--color-forest-700);color:#fff;box-shadow:0 0 0 4px rgba(22,101,52,.15)' : 'background:var(--color-border);color:var(--color-text-muted)')"
                    >
                        <template x-if="step > {{ $num }}">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg>
                        </template>
                        <template x-if="step <= {{ $num }}">
                            <span>{{ $num }}</span>
                        </template>
                    </div>
                    <span style="font-size:.68rem;font-weight:500;white-space:nowrap;"
                          :style="step == {{ $num }} ? 'color:var(--color-forest-700)' : 'color:var(--color-text-muted)'"
                    >{{ $label }}</span>
                </div>
                @if(!$loop->last)
                <div style="flex:1;height:2px;margin:0 .5rem;margin-bottom:1.1rem;"
                     :style="step > {{ $num }} ? 'background:var(--color-forest-600)' : 'background:var(--color-border)'"></div>
                @endif
            </div>
            @endforeach
        </div>

        <form method="POST" action="{{ route('pendaki.bookings.store') }}" id="booking-form">
            @csrf

            {{-- Errors --}}
            @if($errors->any())
            <div class="alert alert-error mb-5">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span class="text-sm">{{ $errors->first() }}</span>
            </div>
            @endif

            {{-- ─── STEP 1: PILIH GUNUNG ─── --}}
            <div x-show="step === 1">
                <h2 style="font-size:.95rem;font-weight:700;color:var(--color-text);margin-bottom:1.25rem;">Pilih Gunung</h2>

                @if($mountains->isEmpty())
                <div style="text-align:center;padding:3rem;color:var(--color-text-muted);font-size:.875rem;">
                    Belum ada gunung yang tersedia.
                </div>
                @else
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1rem;">
                    @foreach($mountains as $mountain)
                    @php
                        $gradeMap = ['I'=>['Grade I','#dcfce7','#15803d'],'II'=>['Grade II','#dcfce7','#15803d'],'III'=>['Grade III','#fef3c7','#b45309'],'IV'=>['Grade IV','#fee2e2','#dc2626'],'V'=>['Grade V','#fef2f2','#991b1b']];
                        [$diffLabel,$diffBg,$diffFg] = $gradeMap[$mountain->grade] ?? ['Grade '.$mountain->grade,'#f3f4f6','#374151'];
                        // Gradient per grade for card header
                        $gradients = ['I'=>'linear-gradient(135deg,#166534 0%,#14532d 100%)','II'=>'linear-gradient(135deg,#166534 0%,#14532d 100%)','III'=>'linear-gradient(135deg,#92400e 0%,#78350f 100%)','IV'=>'linear-gradient(135deg,#991b1b 0%,#7f1d1d 100%)','V'=>'linear-gradient(135deg,#450a0a 0%,#1c0808 100%)'];
                        $grad = $gradients[$mountain->grade] ?? 'linear-gradient(135deg,#1e3a5f 0%,#0f172a 100%)';
                    @endphp
                    <div
                        @click="selectMountain({{ $mountain->id }}, '{{ addslashes($mountain->name) }}', {{ $mountain->regulation?->base_price ?? 0 }}, {{ $mountain->regulation?->max_participants_per_account ?? 10 }}, {{ $mountain->regulation?->max_hiking_days ?? 7 }})"
                        style="border-radius:14px;overflow:hidden;cursor:pointer;transition:transform .15s,box-shadow .15s;position:relative;box-shadow:0 1px 4px rgba(0,0,0,.08);"
                        :style="selectedMountainId == {{ $mountain->id }}
                            ? 'transform:translateY(-2px);box-shadow:0 0 0 3px #166534,0 6px 20px rgba(22,101,52,.2);'
                            : ''"
                        x-on:mouseenter="if(selectedMountainId != {{ $mountain->id }}) $el.style.boxShadow='0 4px 14px rgba(0,0,0,.13)'"
                        x-on:mouseleave="if(selectedMountainId != {{ $mountain->id }}) $el.style.boxShadow='0 1px 4px rgba(0,0,0,.08)'"
                    >
                        {{-- Card image/header --}}
                        <div style="height:110px;position:relative;overflow:hidden;
                            @if($mountain->image_url) background:url('{{ $mountain->image_url }}') center/cover; @else background:{{ $grad }}; @endif">

                            @if($mountain->image_url)
                            <div style="position:absolute;inset:0;background:linear-gradient(to bottom,rgba(0,0,0,.15) 0%,rgba(0,0,0,.55) 100%);"></div>
                            @endif

                            {{-- Difficulty badge top-left --}}
                            <span style="position:absolute;top:.625rem;left:.625rem;font-size:.65rem;font-weight:700;padding:.25rem .6rem;border-radius:20px;background:rgba(0,0,0,.35);color:white;backdrop-filter:blur(4px);">{{ $diffLabel }}</span>

                            {{-- Selected overlay --}}
                            <div x-show="selectedMountainId == {{ $mountain->id }}"
                                 style="position:absolute;inset:0;background:rgba(22,101,52,.25);display:flex;align-items:center;justify-content:center;">
                                <div style="width:40px;height:40px;border-radius:50%;background:#166534;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.3);">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg>
                                </div>
                            </div>

                            {{-- Mountain icon (when no image) --}}
                            @if(!$mountain->image_url)
                            <div style="position:absolute;bottom:.75rem;right:.875rem;opacity:.25;">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20l5-9 4 6 3-4 6 7H3z"/></svg>
                            </div>
                            {{-- Mountain name on header (no-image cards) --}}
                            <div style="position:absolute;bottom:.75rem;left:.875rem;right:3.5rem;">
                                <div style="font-size:.85rem;font-weight:700;color:white;text-shadow:0 1px 3px rgba(0,0,0,.4);">{{ $mountain->name }}</div>
                                <div style="font-size:.7rem;color:rgba(255,255,255,.75);margin-top:.1rem;">{{ $mountain->location }}</div>
                            </div>
                            @endif
                        </div>

                        {{-- Card body --}}
                        <div style="background:white;padding:.875rem 1rem;">
                            @if($mountain->image_url)
                            <div style="font-size:.95rem;font-weight:700;color:var(--color-text);margin-bottom:.1rem;">{{ $mountain->name }}</div>
                            <div style="font-size:.73rem;color:var(--color-text-muted);margin-bottom:.75rem;">{{ $mountain->location }}, {{ $mountain->province }}</div>
                            @else
                            <div style="font-size:.73rem;color:var(--color-text-muted);margin-bottom:.75rem;">{{ $mountain->province }}</div>
                            @endif

                            {{-- Stats row --}}
                            <div style="display:flex;gap:.5rem;align-items:center;margin-bottom:.75rem;">
                                <div style="display:flex;align-items:center;gap:.3rem;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/></svg>
                                    <span style="font-size:.75rem;font-weight:700;color:var(--color-forest-700);">{{ number_format($mountain->height_mdpl) }} mdpl</span>
                                </div>
                                @if($mountain->regulation?->max_hiking_days)
                                <span style="color:var(--color-border);">·</span>
                                <span style="font-size:.73rem;color:var(--color-text-muted);">maks. {{ $mountain->regulation->max_hiking_days }} hari</span>
                                @endif
                            </div>

                            {{-- Price footer --}}
                            <div style="border-top:1px solid var(--color-border);padding-top:.625rem;display:flex;align-items:center;justify-content:space-between;">
                                <div>
                                    <div style="font-size:.68rem;color:var(--color-text-muted);">SIMAKSI/orang</div>
                                    <div style="font-size:.9rem;font-weight:800;color:var(--color-forest-700);">
                                        @if($mountain->regulation)
                                            Rp{{ number_format($mountain->regulation->base_price,0,',','.') }}
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                                @if($mountain->regulation?->quota_per_trail_per_day)
                                <div style="text-align:right;">
                                    <div style="font-size:.68rem;color:var(--color-text-muted);">Kuota/hari</div>
                                    <div style="font-size:.85rem;font-weight:600;color:var(--color-text);">{{ $mountain->regulation->quota_per_trail_per_day }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <input type="hidden" name="mountain_id" :value="selectedMountainId">
            </div>

            {{-- ─── STEP 2: JALUR & TANGGAL ─── --}}
            <div x-show="step === 2" style="display:none;">
                <h2 style="font-size:.95rem;font-weight:700;color:var(--color-text);margin-bottom:.75rem;">Jalur & Tanggal</h2>

                {{-- Selected mountain pill --}}
                <div style="display:inline-flex;align-items:center;gap:.5rem;background:var(--color-forest-50);border:1px solid var(--color-forest-200);border-radius:8px;padding:.5rem .875rem;margin-bottom:1.25rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20l5-9 4 6 3-4 6 7H3z"/></svg>
                    <span style="font-size:.82rem;font-weight:600;color:var(--color-forest-700);" x-text="selectedMountainName"></span>
                </div>

                {{-- Trail select --}}
                <div style="margin-bottom:1.25rem;">
                    <label class="form-label">Jalur Pendakian</label>
                    <div x-show="loadingTrails" style="font-size:.825rem;color:var(--color-text-muted);padding:.75rem;">
                        Memuat jalur...
                    </div>
                    <div x-show="!loadingTrails">
                        <template x-if="trails.length === 0">
                            <div style="font-size:.825rem;color:var(--color-text-muted);padding:.75rem;background:var(--color-surface-alt);border-radius:8px;">
                                Tidak ada jalur aktif untuk gunung ini.
                            </div>
                        </template>
                        <template x-if="trails.length > 0">
                            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.75rem;">
                                <template x-for="(trail, idx) in trails" :key="trail.id">
                                    <div
                                        @click="selectedTrailId = trail.id; selectedTrailGrade = trail.grade || null"
                                        style="border-radius:12px;overflow:hidden;cursor:pointer;transition:transform .15s,box-shadow .15s;position:relative;box-shadow:0 1px 4px rgba(0,0,0,.07);"
                                        :style="selectedTrailId == trail.id
                                            ? 'transform:translateY(-2px);box-shadow:0 0 0 3px #166534,0 6px 16px rgba(22,101,52,.18);'
                                            : ''"
                                        x-on:mouseenter="if(selectedTrailId != trail.id) $el.style.boxShadow='0 4px 12px rgba(0,0,0,.12)'"
                                        x-on:mouseleave="if(selectedTrailId != trail.id) $el.style.boxShadow='0 1px 4px rgba(0,0,0,.07)'"
                                    >
                                        {{-- Colored top band --}}
                                        <div style="height:6px;transition:background .15s;"
                                             :style="selectedTrailId == trail.id ? 'background:#166534;' : 'background:#d1d5db;'"></div>

                                        {{-- Card body --}}
                                        <div style="padding:.875rem 1rem;background:white;transition:background .15s;"
                                             :style="selectedTrailId == trail.id ? 'background:#f0fdf4;' : ''">

                                            {{-- Route number + selected check --}}
                                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.625rem;">
                                                <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:800;transition:all .15s;flex-shrink:0;"
                                                     :style="selectedTrailId == trail.id
                                                         ? 'background:#166534;color:white;'
                                                         : 'background:#f3f4f6;color:#6b7280;'">
                                                    <template x-if="selectedTrailId == trail.id">
                                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg>
                                                    </template>
                                                    <template x-if="selectedTrailId != trail.id">
                                                        <span x-text="idx + 1"></span>
                                                    </template>
                                                </div>

                                                {{-- Trail icon --}}
                                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                                     :style="selectedTrailId == trail.id ? 'stroke:#166534;opacity:.6;' : 'stroke:#d1d5db;'">
                                                    <path d="M3 17l4-8 4 5 3-4 4 7H3z"/>
                                                    <circle cx="18.5" cy="6.5" r="1.5" :fill="selectedTrailId == trail.id ? '#166534' : '#d1d5db'" stroke="none"/>
                                                </svg>
                                            </div>

                                            <input type="radio" name="trail_id" :value="trail.id" x-model="selectedTrailId" style="display:none;">

                                            <div style="font-size:.9rem;font-weight:700;margin-bottom:.3rem;line-height:1.3;"
                                                 :style="selectedTrailId == trail.id ? 'color:#166534;' : 'color:var(--color-text);'"
                                                 x-text="trail.name"></div>

                                            <div style="font-size:.73rem;color:var(--color-text-muted);line-height:1.5;min-height:2.2em;"
                                                 x-text="trail.description || 'Jalur pendakian resmi'"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Dates --}}
                <div style="margin-bottom:1.25rem;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.625rem;">
                        <label class="form-label" style="margin:0;">Tanggal Pendakian</label>
                        <span x-show="maxDays > 0" style="font-size:.72rem;color:var(--color-text-muted);">
                            maks. <strong x-text="maxDays" style="color:var(--color-forest-700);"></strong> hari untuk gunung ini
                        </span>
                    </div>

                    {{-- Inline calendar container --}}
                    <div style="border:1.5px solid var(--color-border);border-radius:12px;overflow:hidden;">
                        <div id="date-range-picker"></div>
                    </div>

                    {{-- Hidden inputs for form submit --}}
                    <input type="hidden" name="start_date" :value="startDate">
                    <input type="hidden" name="end_date"   :value="endDate">

                    {{-- Selected range summary --}}
                    <div style="margin-top:.875rem;">
                        <template x-if="!startDate">
                            <div style="font-size:.8rem;color:var(--color-text-muted);text-align:center;padding:.5rem;">
                                Pilih tanggal naik terlebih dahulu
                            </div>
                        </template>
                        <template x-if="startDate && !endDate">
                            <div style="display:flex;align-items:center;gap:.625rem;padding:.625rem .875rem;background:#fef3c7;border:1px solid #fde68a;border-radius:8px;font-size:.82rem;color:#92400e;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                Tanggal naik: <strong x-text="formatDisplayDate(startDate)"></strong> — pilih tanggal turun
                            </div>
                        </template>
                        <template x-if="startDate && endDate">
                            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.625rem;">
                                <div style="padding:.625rem .875rem;background:var(--color-forest-50);border:1px solid var(--color-forest-200);border-radius:8px;text-align:center;">
                                    <div style="font-size:.68rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-forest-700);margin-bottom:.2rem;">Naik</div>
                                    <div style="font-size:.82rem;font-weight:700;color:var(--color-text);" x-text="formatDisplayDate(startDate)"></div>
                                </div>
                                <div style="padding:.625rem .875rem;background:var(--color-forest-50);border:1px solid var(--color-forest-200);border-radius:8px;text-align:center;">
                                    <div style="font-size:.68rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-forest-700);margin-bottom:.2rem;">Turun</div>
                                    <div style="font-size:.82rem;font-weight:700;color:var(--color-text);" x-text="formatDisplayDate(endDate)"></div>
                                </div>
                                <div style="padding:.625rem .875rem;background:#166534;border-radius:8px;text-align:center;">
                                    <div style="font-size:.68rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#bbf7d0;margin-bottom:.2rem;">Durasi</div>
                                    <div style="font-size:.9rem;font-weight:800;color:white;" x-text="durationText"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Guide --}}
                <div style="margin-top:1rem;padding:.875rem 1rem;border:1px solid var(--color-border);border-radius:10px;">
                    {{-- Grade III warning --}}
                    <div x-show="selectedTrailGrade === 'III'"
                         style="display:none;margin-bottom:.75rem;padding:.625rem .875rem;background:#fefce8;border:1px solid #fde047;border-radius:8px;">
                        <p style="font-size:.8rem;font-weight:600;color:#854d0e;">⚠ Jalur Grade III — Sangat disarankan menggunakan pemandu berpengalaman.</p>
                    </div>
                    {{-- Grade IV warning --}}
                    <div x-show="selectedTrailGrade === 'IV'"
                         style="display:none;margin-bottom:.75rem;padding:.625rem .875rem;background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;">
                        <p style="font-size:.8rem;font-weight:600;color:#991b1b;">🚫 Jalur Grade IV — WAJIB menggunakan pemandu bersertifikat. Booking tidak dapat diproses tanpa pemandu.</p>
                    </div>
                    {{-- Grade V warning --}}
                    <div x-show="selectedTrailGrade === 'V'"
                         style="display:none;margin-bottom:.75rem;padding:.625rem .875rem;background:#450a0a;border:1px solid #dc2626;border-radius:8px;">
                        <p style="font-size:.8rem;font-weight:600;color:#fca5a5;">🚫 Jalur Grade V — WAJIB menggunakan tenaga ahli/pemandu bersertifikasi khusus.</p>
                    </div>

                    <label style="display:flex;align-items:center;gap:.75rem;cursor:pointer;">
                        <input type="checkbox" name="guide_requested" value="1" x-model="guideRequested"
                               style="width:16px;height:16px;accent-color:var(--color-forest-600);">
                        <span style="font-size:.875rem;color:var(--color-text);">Saya memerlukan pemandu (guide)</span>
                    </label>
                </div>
            </div>

            {{-- ─── STEP 3: PESERTA ─── --}}
            <div x-show="step === 3" style="display:none;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
                    <h2 style="font-size:.95rem;font-weight:700;color:var(--color-text);">Data Peserta</h2>
                    <button type="button" @click="addParticipant()" class="btn btn-outline btn-sm">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Tambah Peserta
                    </button>
                </div>

                <div style="display:flex;flex-direction:column;gap:.875rem;">
                    <template x-for="(pax, idx) in participants" :key="idx">
                        <div style="border:1px solid var(--color-border);border-radius:10px;padding:1rem 1.25rem;position:relative;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.875rem;">
                                <span style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;"
                                      :style="idx === 0 ? 'color:var(--color-forest-700)' : 'color:var(--color-text-muted)'"
                                      x-text="idx === 0 ? 'Leader (Anda)' : 'Anggota ' + idx"></span>
                                <button x-show="idx > 0" type="button" @click="removeParticipant(idx)"
                                        style="background:none;border:none;cursor:pointer;color:#ef4444;padding:.2rem;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.875rem;">
                                <div>
                                    <label class="form-label" style="font-size:.72rem;">Nama Lengkap</label>
                                    <input type="text" :name="'participants['+idx+'][name]'" x-model="pax.name"
                                           :readonly="idx === 0"
                                           class="form-input"
                                           :style="idx === 0 ? 'background:var(--color-surface-alt);color:var(--color-text-muted)' : ''"
                                           placeholder="Nama sesuai KTP" required>
                                </div>
                                <div>
                                    <label class="form-label" style="font-size:.72rem;">NIK (16 digit)</label>
                                    <input type="text" :name="'participants['+idx+'][nik]'" x-model="pax.nik"
                                           :readonly="idx === 0"
                                           class="form-input"
                                           :style="idx === 0 ? 'background:var(--color-surface-alt);color:var(--color-text-muted)' : ''"
                                           maxlength="16" inputmode="numeric"
                                           placeholder="16 digit NIK" required>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div style="margin-top:.875rem;font-size:.78rem;color:var(--color-text-muted);">
                    Total peserta: <strong x-text="participants.length"></strong>
                    <span x-show="maxPax > 0"> — maks. <span x-text="maxPax"></span> orang</span>
                </div>
            </div>

            {{-- ─── STEP 4: REVIEW ─── --}}
            <div x-show="step === 4" style="display:none;">
                <h2 style="font-size:.95rem;font-weight:700;color:var(--color-text);margin-bottom:1.25rem;">Review & Konfirmasi</h2>

                <div class="card" style="margin-bottom:1rem;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div>
                            <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);margin-bottom:.2rem;">Gunung</div>
                            <div style="font-size:.875rem;font-weight:600;color:var(--color-text);" x-text="selectedMountainName"></div>
                        </div>
                        <div>
                            <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);margin-bottom:.2rem;">Jalur</div>
                            <div style="font-size:.875rem;font-weight:600;color:var(--color-text);" x-text="selectedTrailName()"></div>
                        </div>
                        <div>
                            <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);margin-bottom:.2rem;">Tanggal Naik</div>
                            <div style="font-size:.875rem;color:var(--color-text);" x-text="startDate"></div>
                        </div>
                        <div>
                            <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);margin-bottom:.2rem;">Tanggal Turun</div>
                            <div style="font-size:.875rem;color:var(--color-text);" x-text="endDate"></div>
                        </div>
                        <div>
                            <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);margin-bottom:.2rem;">Jumlah Peserta</div>
                            <div style="font-size:.875rem;color:var(--color-text);" x-text="participants.length + ' orang'"></div>
                        </div>
                        <div>
                            <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-text-muted);margin-bottom:.2rem;">Pemandu</div>
                            <div style="font-size:.875rem;color:var(--color-text);" x-text="guideRequested ? 'Ya' : 'Tidak'"></div>
                        </div>
                    </div>

                    <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--color-border);display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:.875rem;font-weight:600;color:var(--color-text);">Total Biaya SIMAKSI</span>
                        <span style="font-size:1.1rem;font-weight:800;color:var(--color-forest-700);" x-text="'Rp' + totalPrice.toLocaleString('id-ID')"></span>
                    </div>
                </div>

                {{-- Participants summary --}}
                <div class="card" style="margin-bottom:1rem;">
                    <div style="font-size:.8rem;font-weight:700;color:var(--color-text);margin-bottom:.75rem;">Daftar Peserta</div>
                    <template x-for="(pax, idx) in participants" :key="idx">
                        <div style="display:flex;align-items:center;gap:.75rem;padding:.5rem 0;border-bottom:1px solid var(--color-border);"
                             :style="idx === participants.length-1 ? 'border-bottom:none' : ''">
                            <div class="avatar" style="width:28px;height:28px;font-size:.65rem;flex-shrink:0;"
                                 x-text="pax.name.substring(0,2).toUpperCase()"></div>
                            <div style="flex:1;">
                                <div style="font-size:.825rem;font-weight:600;color:var(--color-text);" x-text="pax.name"></div>
                                <div style="font-size:.7rem;color:var(--color-text-muted);" x-text="pax.nik.substring(0,4) + ' •••• •••• ' + pax.nik.substring(12)"></div>
                            </div>
                            <span style="font-size:.65rem;font-weight:700;padding:.15rem .5rem;border-radius:4px;"
                                  :style="idx===0 ? 'background:var(--color-forest-100);color:var(--color-forest-700)' : 'background:var(--color-border);color:var(--color-text-muted)'"
                                  x-text="idx===0 ? 'Leader' : 'Member'"></span>
                        </div>
                    </template>
                </div>

                {{-- TOS --}}
                <div style="border:1px solid var(--color-border);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.25rem;">
                    <label style="display:flex;align-items:flex-start;gap:.875rem;cursor:pointer;">
                        <input type="checkbox" name="tos_accepted" value="1" x-model="tosAccepted"
                               style="width:16px;height:16px;margin-top:.1rem;accent-color:var(--color-forest-600);flex-shrink:0;">
                        <span style="font-size:.825rem;color:var(--color-text);line-height:1.6;">
                            Saya menyetujui <strong>SOP Pendakian</strong> dan <strong>Syarat & Ketentuan</strong> SummitPass. Saya bertanggung jawab atas keselamatan seluruh peserta yang saya daftarkan.
                        </span>
                    </label>
                </div>
            </div>

            {{-- Navigation buttons --}}
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:1.75rem;padding-top:1.25rem;border-top:1px solid var(--color-border);">
                <button type="button" @click="prevStep()"
                        x-show="step > 1"
                        class="btn btn-ghost btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15,18 9,12 15,6"/></svg>
                    Kembali
                </button>
                <div x-show="step === 1" style="width:1px;"></div>

                <div style="display:flex;gap:.75rem;align-items:center;">
                    <a href="{{ route('pendaki.bookings') }}" class="btn btn-ghost btn-sm">Batal</a>

                    <button type="button" @click="nextStep()"
                            x-show="step < 4"
                            class="btn btn-primary btn-sm"
                            :disabled="!canProceed()">
                        Lanjut
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9,18 15,12 9,6"/></svg>
                    </button>

                    <button type="submit"
                            x-show="step === 4"
                            class="btn btn-primary btn-sm"
                            :disabled="!tosAccepted"
                            :style="!tosAccepted ? 'opacity:.5;cursor:not-allowed' : ''">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg>
                        Buat Booking
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
    function bookingForm() {
        return {
            step: 1,
            selectedMountainId: null,
            selectedMountainName: '',
            selectedTrailId: null,
            basePrice: 0,
            maxPax: 10,
            maxDays: 7,
            trails: [],
            loadingTrails: false,
            startDate: '',
            endDate: '',
            guideRequested: false,
            selectedTrailGrade: null,
            tosAccepted: false,
            _fp: null,
            participants: [
                { name: '{{ addslashes($user->name) }}', nik: '{{ $user->nik ?? '' }}' }
            ],

            init() {
                @if(old('mountain_id'))
                    this.selectedMountainId = {{ old('mountain_id') }};
                    this.step = 4;
                @endif
            },

            selectMountain(id, name, price, maxPax, maxDays) {
                this.selectedMountainId = id;
                this.selectedMountainName = name;
                this.basePrice = price;
                this.maxPax = maxPax;
                this.maxDays = maxDays;
                // Reset dates when mountain changes
                this.startDate = '';
                this.endDate = '';
                if (this._fp) {
                    this._fp.clear();
                    this._fp.set('maxDate', null);
                }
            },

            async loadTrails() {
                if (!this.selectedMountainId) return;
                this.loadingTrails = true;
                this.trails = [];
                this.selectedTrailId = null;
                this.selectedTrailGrade = null;
                try {
                    const r = await fetch(`/api/mountains/${this.selectedMountainId}/trails`);
                    this.trails = await r.json();
                } finally {
                    this.loadingTrails = false;
                }
            },

            initDatePicker() {
                if (this._fp) { this._fp.destroy(); this._fp = null; }

                const maxDays = this.maxDays;
                const self = this;

                this._fp = flatpickr('#date-range-picker', {
                    mode: 'range',
                    inline: true,
                    minDate: 'today',
                    locale: 'id',
                    dateFormat: 'Y-m-d',
                    showMonths: 1,
                    onChange(selectedDates) {
                        if (selectedDates.length >= 1) {
                            self.startDate = self.toYmd(selectedDates[0]);
                            self.endDate   = '';

                            // Enforce max days: disable dates beyond startDate + maxDays - 1
                            if (maxDays > 0) {
                                const cap = new Date(selectedDates[0]);
                                cap.setDate(cap.getDate() + maxDays - 1);
                                self._fp.set('maxDate', cap);
                            }
                        }
                        if (selectedDates.length === 2) {
                            self.startDate = self.toYmd(selectedDates[0]);
                            self.endDate   = self.toYmd(selectedDates[1]);
                            // Reset maxDate cap after full range selected
                            self._fp.set('maxDate', null);
                            self._fp.set('minDate', 'today');
                        }
                    },
                });
            },

            toYmd(date) {
                const y = date.getFullYear();
                const m = String(date.getMonth() + 1).padStart(2, '0');
                const d = String(date.getDate()).padStart(2, '0');
                return `${y}-${m}-${d}`;
            },

            formatDisplayDate(ymd) {
                if (!ymd) return '';
                const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                const [y, m, d] = ymd.split('-');
                return `${parseInt(d)} ${months[parseInt(m)-1]} ${y}`;
            },

            selectedTrailName() {
                const t = this.trails.find(t => t.id == this.selectedTrailId);
                return t ? t.name : '-';
            },

            get durationText() {
                if (!this.startDate || !this.endDate) return '';
                const diff = Math.round((new Date(this.endDate) - new Date(this.startDate)) / 86400000) + 1;
                return diff + ' hari';
            },

            get totalPrice() {
                return this.basePrice * this.participants.length;
            },

            addParticipant() {
                if (this.maxPax && this.participants.length >= this.maxPax) {
                    alert(`Maksimal ${this.maxPax} peserta.`);
                    return;
                }
                this.participants.push({ name: '', nik: '' });
            },

            removeParticipant(idx) {
                this.participants.splice(idx, 1);
            },

            canProceed() {
                if (this.step === 1) return !!this.selectedMountainId;
                if (this.step === 2) return !!this.selectedTrailId && !!this.startDate && !!this.endDate;
                if (this.step === 3) return this.participants.every(p => p.name && p.nik && p.nik.length === 16);
                return false;
            },

            async nextStep() {
                if (!this.canProceed()) return;
                if (this.step === 1) await this.loadTrails();
                this.step++;
                window.scrollTo(0, 0);
                if (this.step === 2) {
                    this.$nextTick(() => this.initDatePicker());
                }
            },

            prevStep() {
                this.step--;
                window.scrollTo(0, 0);
            },
        }
    }
    </script>
</x-layouts.web>
