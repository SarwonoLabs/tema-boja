@if (!empty($widget_keuangan['tahun']) && !is_null($widget_keuangan['tahun']))
    <!-- widget APBDes Elegan -->
    <style>
        /* ===== APBDes Widget — Elegant Design ===== */
        .apbdes-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(37,99,235,.10);
            border: 1px solid #E2E8F0;
        }

        .apbdes-header {
            background: linear-gradient(135deg, #2563EB 0%, #1E3A5F 100%);
            padding: 20px 24px 0;
            position: relative;
            overflow: hidden;
        }

        .apbdes-header::before {
            content: '';
            position: absolute;
            right: -30px;
            top: -30px;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: rgba(255,255,255,.06);
            pointer-events: none;
        }

        .apbdes-header::after {
            content: '';
            position: absolute;
            right: 30px;
            bottom: -50px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255,255,255,.04);
            pointer-events: none;
        }

        .apbdes-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            position: relative;
            z-index: 1;
        }

        .apbdes-header-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .apbdes-header-icon {
            width: 38px;
            height: 38px;
            background: rgba(255,255,255,.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #fff;
            flex-shrink: 0;
        }

        .apbdes-header h3 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
        }

        .apbdes-header-subtitle {
            margin: 2px 0 0;
            font-size: 11px;
            color: rgba(255,255,255,.65);
        }

        .apbdes-year-select {
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.25);
            border-radius: 8px;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            padding: 5px 28px 5px 10px;
            cursor: pointer;
            outline: none;
            position: relative;
            z-index: 1;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 12px;
        }

        .apbdes-year-select option {
            background: #1E3A5F;
            color: #fff;
        }

        .apbdes-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            padding: 0 24px 16px;
            position: relative;
            z-index: 1;
        }

        .apbdes-summary-card {
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 10px;
            padding: 10px 12px;
        }

        .apbdes-summary-label {
            font-size: 10px;
            color: rgba(255,255,255,.7);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 3px;
        }

        .apbdes-summary-value {
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
            word-break: break-all;
        }

        .apbdes-summary-pct {
            font-size: 10px;
            color: rgba(255,255,255,.6);
            margin-top: 2px;
        }

        .apbdes-summary-pct span {
            display: inline-block;
            background: rgba(52,211,153,.25);
            color: #6EE7B7;
            border-radius: 4px;
            padding: 0 5px;
            font-weight: 700;
        }

        .apbdes-tabs {
            display: flex;
            border-bottom: 1px solid #E2E8F0;
            background: #F8FAFC;
            padding: 0 16px;
        }

        .apbdes-tab {
            flex: 1;
            padding: 11px 8px;
            text-align: center;
            font-size: 11.5px;
            font-weight: 600;
            color: #64748B;
            cursor: pointer;
            border: none;
            background: transparent;
            border-bottom: 2px solid transparent;
            transition: color .2s, border-color .2s;
            white-space: nowrap;
            outline: none;
        }

        .apbdes-tab:hover { color: #2563EB; }

        .apbdes-tab.active {
            color: #2563EB;
            border-bottom-color: #2563EB;
        }

        .apbdes-tab i {
            display: block;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .apbdes-body {
            padding: 16px 20px 20px;
        }

        .apbdes-legend {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 12px;
            padding: 8px 12px;
            background: #F8FAFC;
            border-radius: 8px;
            border: 1px solid #E2E8F0;
            flex-wrap: wrap;
        }

        .apbdes-legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #475569;
            font-weight: 600;
        }

        .apbdes-legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
            flex-shrink: 0;
        }

        .apbdes-items { display: flex; flex-direction: column; gap: 10px; }

        .apbdes-item {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 12px 14px;
            transition: box-shadow .2s, border-color .2s;
        }

        .apbdes-item:hover {
            box-shadow: 0 2px 8px rgba(37,99,235,.08);
            border-color: #BFDBFE;
        }

        .apbdes-item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
            gap: 8px;
        }

        .apbdes-item-name {
            font-size: 12px;
            font-weight: 700;
            color: #1E293B;
            line-height: 1.4;
        }

        .apbdes-item-pct {
            font-size: 11px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 20px;
            flex-shrink: 0;
        }

        .apbdes-item-pct.good   { background: #D1FAE5; color: #065F46; }
        .apbdes-item-pct.medium { background: #FEF3C7; color: #92400E; }
        .apbdes-item-pct.low    { background: #FEE2E2; color: #991B1B; }
        .apbdes-item-pct.over   { background: #EDE9FE; color: #5B21B6; }

        .apbdes-progress-track {
            height: 6px;
            background: #E2E8F0;
            border-radius: 99px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .apbdes-progress-bar {
            height: 100%;
            border-radius: 99px;
            transition: width .6s ease;
        }

        .apbdes-progress-bar.good   { background: linear-gradient(90deg, #10B981, #34D399); }
        .apbdes-progress-bar.medium { background: linear-gradient(90deg, #F59E0B, #FCD34D); }
        .apbdes-progress-bar.low    { background: linear-gradient(90deg, #EF4444, #F87171); }
        .apbdes-progress-bar.over   { background: linear-gradient(90deg, #8B5CF6, #A78BFA); }

        .apbdes-item-amounts {
            display: flex;
            justify-content: space-between;
            gap: 4px;
        }

        .apbdes-amount { font-size: 10.5px; }
        .apbdes-amount-label { color: #94A3B8; font-weight: 600; }
        .apbdes-amount-value { color: #334155; font-weight: 700; }

        .apbdes-empty {
            text-align: center;
            padding: 24px 0;
            color: #94A3B8;
            font-size: 12px;
        }

        .apbdes-empty i {
            font-size: 28px;
            display: block;
            margin-bottom: 6px;
            opacity: .4;
        }

        .apbdes-unavail {
            text-align: center;
            padding: 8px 0 0;
            color: #94A3B8;
            font-size: 11px;
            font-style: italic;
        }

        @media (max-width: 480px) {
            .apbdes-header { padding: 16px 16px 0; }
            .apbdes-summary { padding: 0 16px 14px; }
            .apbdes-body { padding: 12px 14px 16px; }
            .apbdes-summary-value { font-size: 11px; }
            .apbdes-tab { font-size: 10.5px; padding: 9px 4px; }
        }
    </style>
    <div class="apbdes-card">
        {{-- Header --}}
        <div class="apbdes-header">
            <div class="apbdes-header-top">
                <div class="apbdes-header-title">
                    <div class="apbdes-header-icon">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <div>
                        <h3>{{ $judul_widget }}</h3>
                        <p class="apbdes-header-subtitle">Transparansi Keuangan Desa</p>
                    </div>
                </div>
                <select class="apbdes-year-select" id="apbdes-year-sel" onchange="apbdesGantiTahun(this.value)">
                    @foreach ($widget_keuangan['tahun'] as $thn)
                        <option value="{{ $thn }}" {{ $thn == $widget_keuangan['tahun_terbaru'] ? 'selected' : '' }}>{{ $thn }}</option>
                    @endforeach
                </select>
            </div>
            <div class="apbdes-summary">
                <div class="apbdes-summary-card">
                    <div class="apbdes-summary-label"><i class="fas fa-coins mr-1"></i>Total Anggaran</div>
                    <div class="apbdes-summary-value" id="apbdes-total-anggaran">—</div>
                </div>
                <div class="apbdes-summary-card">
                    <div class="apbdes-summary-label"><i class="fas fa-check-circle mr-1"></i>Total Realisasi</div>
                    <div class="apbdes-summary-value" id="apbdes-total-realisasi">—</div>
                    <div class="apbdes-summary-pct"><span id="apbdes-total-pct">0%</span> terealisasi</div>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="apbdes-tabs">
            <button class="apbdes-tab active" id="tab-pelaksanaan" onclick="apbdesGantiTipe('pelaksanaan')">
                <i class="fas fa-chart-pie"></i>Pelaksanaan
            </button>
            <button class="apbdes-tab" id="tab-pendapatan" onclick="apbdesGantiTipe('pendapatan')">
                <i class="fas fa-arrow-circle-down"></i>Pendapatan
            </button>
            <button class="apbdes-tab" id="tab-belanja" onclick="apbdesGantiTipe('belanja')">
                <i class="fas fa-arrow-circle-up"></i>Belanja
            </button>
        </div>

        {{-- Body --}}
        <div class="apbdes-body">
            <div class="apbdes-legend">
                <div class="apbdes-legend-item">
                    <div class="apbdes-legend-dot" style="background:#60A5FA"></div>
                    <span>Anggaran</span>
                </div>
                <div class="apbdes-legend-item">
                    <div class="apbdes-legend-dot" style="background:#10B981"></div>
                    <span>Realisasi (≥70%)</span>
                </div>
                <div class="apbdes-legend-item">
                    <div class="apbdes-legend-dot" style="background:#F59E0B"></div>
                    <span>Sedang (40–69%)</span>
                </div>
                <div class="apbdes-legend-item">
                    <div class="apbdes-legend-dot" style="background:#EF4444"></div>
                    <span>Rendah (&lt;40%)</span>
                </div>
            </div>
            <div class="apbdes-items" id="apbdes-items-container">
            </div>
        </div>
    </div>

    <script>
        (function() {
            var rawData = {!! $widget_keuangan['data'] !!};
            var currentYear = "{{ $widget_keuangan['tahun_terbaru'] }}";
            var currentTipe = "pelaksanaan";

            var tipeMap = {
                pelaksanaan: 'res_pelaksanaan',
                pendapatan:  'res_pendapatan',
                belanja:     'res_belanja'
            };

            function formatRupiah(angka) {
                if (!angka || isNaN(parseInt(angka))) return 'Rp 0';
                var n = parseInt(angka);
                if (n >= 1000000000) return 'Rp ' + (n / 1000000000).toFixed(2).replace(/\.?0+$/, '') + ' M';
                if (n >= 1000000)    return 'Rp ' + (n / 1000000).toFixed(1).replace(/\.?0+$/, '') + ' Jt';
                if (n >= 1000)       return 'Rp ' + Math.round(n / 1000) + ' Rb';
                return 'Rp ' + n.toLocaleString('id-ID');
            }

            function getPctClass(pct) {
                if (pct > 100) return 'over';
                if (pct >= 70) return 'good';
                if (pct >= 40) return 'medium';
                return 'low';
            }

            function renderItems(tahun, tipe) {
                var container = document.getElementById('apbdes-items-container');
                var key = tipeMap[tipe];
                container.innerHTML = '';

                if (!rawData[tahun] || !rawData[tahun][key]) {
                    container.innerHTML = '<div class="apbdes-empty"><i class="fas fa-inbox"></i>Data belum tersedia</div>';
                    updateSummary(0, 0);
                    return;
                }

                var chartData = rawData[tahun][key];
                var totalAnggaran = 0, totalRealisasi = 0;
                var html = '';

                chartData.forEach(function(item) {
                    if (!item['nama']) return;
                    var anggaran  = parseInt(item['anggaran'])  || 0;
                    var realisasi = parseInt(item['realisasi']) || 0;
                    totalAnggaran  += anggaran;
                    totalRealisasi += realisasi;

                    if (!anggaran && !realisasi) {
                        html += '<div class="apbdes-item">' +
                            '<div class="apbdes-item-header"><div class="apbdes-item-name">' + item['nama'] + '</div></div>' +
                            '<div class="apbdes-unavail">Data tidak tersedia</div></div>';
                        return;
                    }

                    var pct = anggaran > 0 ? Math.round((realisasi / anggaran) * 100) : 0;
                    var cls = getPctClass(pct);
                    var barWidth = Math.min(pct, 100);

                    html += '<div class="apbdes-item">' +
                        '<div class="apbdes-item-header">' +
                            '<div class="apbdes-item-name">' + item['nama'] + '</div>' +
                            '<div class="apbdes-item-pct ' + cls + '">' + pct + '%</div>' +
                        '</div>' +
                        '<div class="apbdes-progress-track">' +
                            '<div class="apbdes-progress-bar ' + cls + '" style="width:' + barWidth + '%"></div>' +
                        '</div>' +
                        '<div class="apbdes-item-amounts">' +
                            '<div class="apbdes-amount"><span class="apbdes-amount-label">Anggaran </span>' +
                            '<span class="apbdes-amount-value">' + formatRupiah(anggaran) + '</span></div>' +
                            '<div class="apbdes-amount"><span class="apbdes-amount-label">Realisasi </span>' +
                            '<span class="apbdes-amount-value">' + formatRupiah(realisasi) + '</span></div>' +
                        '</div></div>';
                });

                container.innerHTML = html || '<div class="apbdes-empty"><i class="fas fa-inbox"></i>Data belum tersedia</div>';
                updateSummary(totalAnggaran, totalRealisasi);
            }

            function updateSummary(anggaran, realisasi) {
                document.getElementById('apbdes-total-anggaran').textContent = formatRupiah(anggaran);
                document.getElementById('apbdes-total-realisasi').textContent = formatRupiah(realisasi);
                var pct = anggaran > 0 ? Math.round((realisasi / anggaran) * 100) : 0;
                document.getElementById('apbdes-total-pct').textContent = pct + '%';
            }

            window.apbdesGantiTahun = function(thn) {
                currentYear = thn;
                renderItems(currentYear, currentTipe);
            };

            window.apbdesGantiTipe = function(tipe) {
                currentTipe = tipe;
                ['pelaksanaan', 'pendapatan', 'belanja'].forEach(function(t) {
                    var el = document.getElementById('tab-' + t);
                    if (el) el.classList.toggle('active', t === tipe);
                });
                renderItems(currentYear, currentTipe);
            };

            renderItems(currentYear, currentTipe);
        })();
    </script>
@endif
