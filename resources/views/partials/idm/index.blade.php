@extends('theme::layouts.full-content')
@include('theme::commons.asset_highcharts')

@section('content')
    {{-- ═══ Breadcrumb ═══ --}}
    <nav class="breadcrumb-boja" aria-label="breadcrumb">
        <ol>
            <li><a href="{{ ci_route() }}"><i class="fas fa-home"></i> Beranda</a></li>
            <li>Status IDM {{ $tahun }}</li>
        </ol>
    </nav>

    {{-- ═══ Page Header ═══ --}}
    <div class="boja-page-header">
        <div class="boja-page-header-text">
            <h1 class="boja-page-title"><i class="fas fa-tachometer-alt"></i> Indeks Desa Membangun (IDM)</h1>
            <p class="boja-page-subtitle">Status IDM {{ ucwords(strtolower(setting('sebutan_desa'))) }} Tahun {{ $tahun }}</p>
        </div>
    </div>

    {{-- ═══ Error State ═══ --}}
    <div id="status-error" style="display:none">
        <div class="idm-error-card">
            <i class="fas fa-exclamation-triangle"></i>
            <p id="error-message">Data IDM tidak tersedia.</p>
        </div>
    </div>

    {{-- ═══ IDM Content ═══ --}}
    <div id="status-idm" style="display:none">

        {{-- Skor Cards ──────────────────────── --}}
        <div class="idm-skor-grid">
            <div class="idm-skor-card idm-skor-blue">
                <div class="idm-skor-icon"><i class="fas fa-arrow-trend-up"></i></div>
                <div class="idm-skor-info">
                    <span class="idm-skor-number" id="skor-saat-ini">—</span>
                    <span class="idm-skor-label">Skor IDM Saat Ini</span>
                </div>
            </div>
            <div class="idm-skor-card idm-skor-amber">
                <div class="idm-skor-icon"><i class="fas fa-signal"></i></div>
                <div class="idm-skor-info">
                    <span class="idm-skor-number" id="status-saat-ini">—</span>
                    <span class="idm-skor-label">Status IDM</span>
                </div>
            </div>
            <div class="idm-skor-card idm-skor-green">
                <div class="idm-skor-icon"><i class="fas fa-bullseye"></i></div>
                <div class="idm-skor-info">
                    <span class="idm-skor-number" id="target-status">—</span>
                    <span class="idm-skor-label">Target Status</span>
                </div>
            </div>
            <div class="idm-skor-card idm-skor-red">
                <div class="idm-skor-icon"><i class="fas fa-gauge-simple-high"></i></div>
                <div class="idm-skor-info">
                    <span class="idm-skor-number" id="skor-minimal">—</span>
                    <span class="idm-skor-label">Skor Minimal</span>
                </div>
            </div>
        </div>

        {{-- Identitas + Chart ──────────────── --}}
        <div class="idm-overview-row">
            {{-- Identitas Card --}}
            <div class="idm-identitas-card">
                <div class="idm-identitas-header"><i class="fas fa-map-marker-alt"></i> Identitas Wilayah</div>
                <div class="idm-identitas-body">
                    <div class="idm-identitas-row">
                        <span class="idm-identitas-label">Provinsi</span>
                        <span class="idm-identitas-value" id="nama-provinsi">-</span>
                    </div>
                    <div class="idm-identitas-row">
                        <span class="idm-identitas-label">Kabupaten</span>
                        <span class="idm-identitas-value" id="nama-kabupaten">-</span>
                    </div>
                    <div class="idm-identitas-row">
                        <span class="idm-identitas-label">{{ ucfirst(setting('sebutan_kecamatan')) }}</span>
                        <span class="idm-identitas-value" id="nama-kecamatan">-</span>
                    </div>
                    <div class="idm-identitas-row">
                        <span class="idm-identitas-label">{{ ucfirst(setting('sebutan_desa')) }}</span>
                        <span class="idm-identitas-value" id="nama-desa">-</span>
                    </div>
                </div>
            </div>
            {{-- Chart Card --}}
            <div class="idm-chart-card">
                <div class="idm-chart-header"><i class="fas fa-chart-pie"></i> Komposisi Skor</div>
                <div class="idm-chart-body">
                    <div id="container" style="width:100%;min-height:300px"></div>
                </div>
            </div>
        </div>

        {{-- Tabel Indikator ────────────────── --}}
        <div class="boja-table-wrap">
            <div class="idm-section-title"><i class="fas fa-list-check"></i> Detail Indikator IDM</div>
            <div class="boja-table-inner">
                <div style="overflow-x:auto">
                    <table class="display" id="tabel-daftar" style="width:100%;font-size:.78rem">
                        <thead>
                            <tr>
                                <th rowspan="2" width="3%">No</th>
                                <th rowspan="2">Indikator IDM</th>
                                <th rowspan="2" width="5%">Skor</th>
                                <th rowspan="2">Keterangan</th>
                                <th rowspan="2">Kegiatan yang Dapat Dilakukan</th>
                                <th rowspan="2" width="5%">+Nilai</th>
                                <th colspan="6" style="text-align:center">Yang Dapat Melaksanakan Kegiatan</th>
                            </tr>
                            <tr>
                                <th width="5%">Pusat</th>
                                <th width="5%">Provinsi</th>
                                <th width="5%">Kabupaten</th>
                                <th width="5%">Desa</th>
                                <th width="5%">CSR</th>
                                <th width="5%">Lainnya</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading --}}
    <div id="idm-loading" style="text-align:center;padding:48px">
        <i class="fas fa-spinner fa-spin" style="color:var(--primary);font-size:1.4rem"></i>
        <p style="margin-top:10px;color:#6b7280;font-size:.88rem">Memuat data IDM tahun {{ $tahun }}...</p>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            var tahun = '{{ $tahun }}';
            var route = '{{ route('api.idm', $tahun) }}';

            $.get(route, function(data) {
                $('#idm-loading').hide();

                if (data['error_msg']) {
                    $('#status-error').show();
                    $('#status-idm').hide();
                    $('#error-message').text(data['error_msg']);
                    return;
                }

                $('#status-idm').show();
                $('#status-error').hide();

                var summaries = data['data'][0]['attributes']['SUMMARIES'];
                var row = data['data'][0]['attributes']['ROW'];
                var identitas = data['data'][0]['attributes']['IDENTITAS'][0];
                var iks = parseFloat(row[35].SKOR || 0);
                var ike = parseFloat(row[48].SKOR || 0);
                var ikl = parseFloat(row[52].SKOR || 0);

                // Skor cards
                $('#skor-saat-ini').text(parseFloat(summaries.SKOR_SAAT_INI).toFixed(4));
                $('#status-saat-ini').text(summaries.STATUS);
                $('#skor-minimal').text(parseFloat(summaries.SKOR_MINIMAL).toFixed(4));
                $('#target-status').text(summaries.TARGET_STATUS);

                // Highcharts
                loadHighcharts(tahun, iks, ike, ikl);

                // Identitas
                $('#nama-provinsi').text(identitas.nama_provinsi);
                $('#nama-kabupaten').text(identitas.nama_kab_kota);
                $('#nama-kecamatan').text(identitas.nama_kecamatan);
                $('#nama-desa').text(identitas.nama_desa);

                // Tabel
                row.forEach(function(item) {
                    var hasNo = item.NO ? '' : ' style="font-weight:700;background:#f0fdf4"';
                    var tr = '' +
                        '<tr' + hasNo + '>' +
                            '<td class="text-center">' + (item.NO || '') + '</td>' +
                            '<td style="min-width:150px">' + (item.INDIKATOR || '') + '</td>' +
                            '<td class="text-center">' + (item.SKOR || '') + '</td>' +
                            '<td style="min-width:200px">' + (item.KETERANGAN || '') + '</td>' +
                            '<td>' + (item.KEGIATAN || '') + '</td>' +
                            '<td class="text-center">' + (item.NILAI || '') + '</td>' +
                            '<td class="text-center">' + (item.PUSAT || '') + '</td>' +
                            '<td class="text-center">' + (item.PROV || '') + '</td>' +
                            '<td class="text-center">' + (item.KAB || '') + '</td>' +
                            '<td class="text-center">' + (item.DESA || '') + '</td>' +
                            '<td class="text-center">' + (item.CSR || '') + '</td>' +
                            '<td class="text-center">' + (item.LAINNYA || '') + '</td>' +
                        '</tr>';
                    $('#tabel-daftar tbody').append(tr);
                });

            }).fail(function(xhr, status, error) {
                $('#idm-loading').hide();
                $('#status-error').show();
                $('#status-idm').hide();
                $('#error-message').text('Data IDM tahun ' + tahun + ' tidak ditemukan.');
            });

            function loadHighcharts(tahun, iks, ike, ikl) {
                Highcharts.chart('container', {
                    chart: {
                        type: 'pie',
                        options3d: { enabled: true, alpha: 45 },
                        style: { fontFamily: "'Plus Jakarta Sans', 'Nunito', sans-serif" }
                    },
                    title: {
                        text: 'Indeks Desa Membangun (IDM) ' + tahun,
                        style: { fontSize: '14px', fontWeight: '700', color: '#1C4D35' }
                    },
                    subtitle: {
                        text: 'SKOR : IKS, IKE, IKL',
                        style: { fontSize: '12px', color: '#64748b' }
                    },
                    colors: ['#2F855A', '#D4AF37', '#3B82F6'],
                    plotOptions: {
                        series: { colorByPoint: true },
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            showInLegend: true,
                            depth: 45,
                            innerSize: 70,
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.y:,.2f} / {point.percentage:.1f} %',
                                style: { fontSize: '11px' }
                            }
                        }
                    },
                    series: [{
                        name: 'SKOR',
                        shadow: 1,
                        border: 1,
                        data: [
                            ['IKS', parseFloat(iks)],
                            ['IKE', parseFloat(ike)],
                            ['IKL', parseFloat(ikl)]
                        ]
                    }]
                });
            }
        });
    </script>
@endpush
