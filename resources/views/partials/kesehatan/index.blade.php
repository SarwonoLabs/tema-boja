@extends('theme::layouts.full-content')
@include('theme::commons.asset_highcharts')

@section('content')
    <nav class="breadcrumb-boja" aria-label="breadcrumb">
        <ol>
            <li><a href="{{ ci_route() }}"><i class="fas fa-home"></i> Beranda</a></li>
            <li>{{ $title }}</li>
        </ol>
    </nav>

    {{-- ═══ Page Header ═══ --}}
    <div class="kes-header">
        <div class="kes-header-deco kes-header-deco-1"></div>
        <div class="kes-header-deco kes-header-deco-2"></div>
        <div class="kes-header-text">
            <h1 class="kes-title"><i class="fas fa-heartbeat"></i> {{ $title }}</h1>
            <p class="kes-subtitle">Data Pemantauan Kesehatan &amp; Pencegahan Stunting</p>
        </div>
    </div>

    {{-- ═══ Filter Bar ═══ --}}
    <div class="kes-filter-bar">
        <form class="kes-filter-form" action="" method="get">
            <div class="kes-filter-group">
                <label><i class="fas fa-calendar-alt"></i> Kuartal</label>
                <select name="kuartal" id="kuartal" required class="kes-filter-field">
                    @foreach (kuartal2() as $item)
                        <option value="{{ $item['ke'] }}" @selected($item['ke'] == $kuartal)>
                            Kuartal {{ $item['ke'] }} ({{ $item['bulan'] }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="kes-filter-group">
                <label><i class="fas fa-calendar"></i> Tahun</label>
                <select name="tahun" id="tahun" class="kes-filter-field">
                    @foreach ($dataTahun as $item)
                        <option value="{{ $item->tahun }}" @selected($item->tahun == $tahun)>{{ $item->tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div class="kes-filter-group">
                <label><i class="fas fa-clinic-medical"></i> Posyandu</label>
                <select name="id_posyandu" id="id_posyandu" class="kes-filter-field">
                    <option value="">Semua Posyandu</option>
                    @foreach ($posyandu as $item)
                        <option value="{{ $item->id }}" @selected($item->id == $idPosyandu)>{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="kes-filter-btn-wrap">
                <button type="submit" class="kes-filter-btn">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    {{-- ═══ Data Container ═══ --}}
    <div id="stunting-list" class="kes-content"></div>
@endsection

@push('scripts')
    <script type="text/javascript">
    (function($){
        'use strict';

        var apiUrl = '{{ ci_route("internal_api.stunting") }}';
        var scorecardUrl = '{{ ci_route("data-kesehatan.scorecard") }}';

        // ── Templates ──
        var widgetTpl = `@include('theme::partials.kesehatan.widget_item')`;

        var tplUmur = document.createElement('template');
        tplUmur.innerHTML = `@include('theme::partials.kesehatan.chart_stunting_umur')`;
        var umurNode = tplUmur.content.firstElementChild;

        var tplPosyandu = document.createElement('template');
        tplPosyandu.innerHTML = `@include('theme::partials.kesehatan.chart_stunting_posyandu')`;
        var posyanduNode = tplPosyandu.content.firstElementChild;

        var scorecardNode = document.createElement('div');

        // Kelompok umur
        var tplKelompokUmur = document.createElement('template');
        tplKelompokUmur.innerHTML = '<div class="kes-chart-card"><div class="kes-chart-card-header"><i class="fas fa-chart-pie"></i> Distribusi Kelompok Umur</div><div id="chart_kelompok_umur" style="min-height:380px;"></div></div>';
        var kelompokUmurNode = tplKelompokUmur.content.firstElementChild;

        // Status per umur
        var tplStatusPerUmur = document.createElement('template');
        tplStatusPerUmur.innerHTML = '<div class="kes-chart-row"><div class="kes-chart-card kes-chart-card-sm"><div id="chart_status_umur_0_5" style="min-height:340px;"></div></div><div class="kes-chart-card kes-chart-card-sm"><div id="chart_status_umur_6_11" style="min-height:340px;"></div></div><div class="kes-chart-card kes-chart-card-sm"><div id="chart_status_umur_12_23" style="min-height:340px;"></div></div></div>';
        var statusPerUmurNode = tplStatusPerUmur.content.firstElementChild;

        // ── Highcharts Boja Theme ──
        var chartColors = {
            primary: '#2F855A',
            primaryDark: '#276749',
            accent: '#D4AF37',
            blue: '#3B82F6',
            green: '#10B981',
            yellow: '#F59E0B',
            red: '#EF4444',
            purple: '#8B5CF6',
            orange: '#F97316',
            gray: '#9CA3AF'
        };

        var chartFontFamily = "'Plus Jakarta Sans', 'Nunito', sans-serif";

        function bojaChartOpts(opts) {
            return $.extend(true, {
                chart: {
                    style: { fontFamily: chartFontFamily },
                    backgroundColor: 'transparent'
                },
                title: {
                    style: { fontSize: '15px', fontWeight: '700', color: '#1C4D35' }
                },
                credits: { enabled: false }
            }, opts);
        }

        // ── Load Data ──
        function loadStunting(tahun, kuartal, idPosyandu) {
            var $list = $('#stunting-list');
            $list.html('<div class="kes-loading"><div class="kes-spinner"></div><span>Memuat data kesehatan...</span></div>');

            $.ajax({
                url: apiUrl,
                data: { tahun: tahun, kuartal: kuartal, idPosyandu: idPosyandu },
                type: 'GET',
                dataType: 'json',
                success: function(resp) {
                    $list.empty();
                    var attrs = resp.data[0].attributes;
                    var widgets = attrs.widgets;
                    var chartUmur = attrs.chartStuntingUmurData;
                    var chartPosyandu = attrs.chartStuntingPosyanduData;
                    var scorecard = attrs.scorecard;
                    var chartKelompokUmur = attrs.chartKelompokUmurData;
                    var chartStatusPerUmur = attrs.chartStatusPerUmurData;

                    // Section: Widgets
                    var $widgetWrap = $('<div class="kes-widget-grid"></div>');
                    $list.append($widgetWrap);
                    renderWidgets($widgetWrap, widgets);

                    // Section: Charts Umur (3 pie)
                    $list.append(umurNode);

                    // Section: Kelompok Umur
                    if (chartKelompokUmur) $list.append(kelompokUmurNode);

                    // Section: Status per Umur
                    if (chartStatusPerUmur) $list.append(statusPerUmurNode);

                    // Section: Posyandu
                    $list.append(posyanduNode);

                    // Section: Scorecard
                    $list.append(scorecardNode);

                    // Generate all charts
                    if (chartUmur) generateChartUmur(chartUmur);
                    if (chartKelompokUmur) generateChartKelompokUmur(chartKelompokUmur);
                    if (chartStatusPerUmur) generateChartStatusPerUmur(chartStatusPerUmur);
                    if (chartPosyandu) generateChartPosyandu(chartPosyandu);
                    if (scorecard) generateScorecard(scorecard);
                },
                error: function() {
                    $list.html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Gagal memuat data kesehatan.</div>');
                }
            });
        }

        // ── Render Widgets ──
        function renderWidgets($wrap, widgets) {
            var iconMap = {
                'bg-blue': { icon: 'fas fa-stethoscope', gradient: 'linear-gradient(135deg,#3B82F6,#2563EB)' },
                'bg-gray': { icon: 'fas fa-baby', gradient: 'linear-gradient(135deg,#6B7280,#4B5563)' },
                'bg-green': { icon: 'fas fa-heart', gradient: 'linear-gradient(135deg,#10B981,#059669)' },
                'bg-yellow': { icon: 'fas fa-exclamation-triangle', gradient: 'linear-gradient(135deg,#F59E0B,#D97706)' },
                'bg-red': { icon: 'fas fa-times-circle', gradient: 'linear-gradient(135deg,#EF4444,#DC2626)' }
            };

            widgets.forEach(function(w, idx) {
                var map = iconMap[w['bg-color']] || iconMap['bg-green'];
                var html =
                    '<div class="kes-widget-card" style="animation-delay:'+(idx*0.08)+'s">' +
                        '<div class="kes-widget-icon" style="background:'+map.gradient+'">' +
                            '<i class="'+map.icon+'"></i>' +
                        '</div>' +
                        '<div class="kes-widget-info">' +
                            '<span class="kes-widget-total">'+w.total+'</span>' +
                            '<span class="kes-widget-label">'+w.title+'</span>' +
                        '</div>' +
                    '</div>';
                $wrap.append(html);
            });
        }

        // ── Chart: Stunting per Umur (3 pie) ──
        function generateChartUmur(data) {
            data.forEach(function(item) {
                Highcharts.chart(item.id, bojaChartOpts({
                    chart: { type: 'pie' },
                    title: { text: item.title },
                    tooltip: { valueSuffix: '%' },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            colors: [chartColors.green, chartColors.red],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            dataLabels: {
                                enabled: true,
                                distance: -30,
                                format: '{point.y:,.1f}%',
                                style: { fontWeight: '600', color: '#fff', textOutline: '1px #333', fontSize: '12px' }
                            },
                            showInLegend: true
                        }
                    },
                    series: [{ type: 'pie', name: 'Persentase', colorByPoint: true, data: item.data }]
                }));
            });
        }

        // ── Chart: Kelompok Umur ──
        function generateChartKelompokUmur(data) {
            Highcharts.chart(data.id, bojaChartOpts({
                chart: { type: 'pie' },
                title: { text: data.title, style: { fontSize: '16px' } },
                tooltip: { pointFormat: '<b>{point.jumlah}</b> anak ({point.y:.1f}%)' },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        colors: [chartColors.blue, chartColors.purple, chartColors.orange],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b><br>{point.jumlah} anak ({point.y:.1f}%)',
                            style: { fontSize: '12px', fontWeight: '600' }
                        },
                        showInLegend: true
                    }
                },
                series: [{ name: 'Jumlah Anak', colorByPoint: true, data: data.data }]
            }));
        }

        // ── Chart: Status per Umur ──
        function generateChartStatusPerUmur(data) {
            data.forEach(function(item) {
                var colors = item.total > 0 ? [chartColors.green, chartColors.yellow, chartColors.red] : [chartColors.gray];
                Highcharts.chart(item.id, bojaChartOpts({
                    chart: { type: 'pie' },
                    title: { text: item.title, style: { fontSize: '13px' } },
                    subtitle: { text: 'Total: ' + item.total + ' anak', style: { fontSize: '11px' } },
                    tooltip: { pointFormat: '<b>{point.jumlah}</b> anak ({point.y:.1f}%)' },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            colors: colors,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            dataLabels: {
                                enabled: true,
                                format: item.total > 0 ? '<b>{point.name}</b><br>{point.jumlah} ({point.y:.1f}%)' : '<b>{point.name}</b>',
                                style: { fontSize: '11px' }
                            },
                            showInLegend: true
                        }
                    },
                    series: [{ name: 'Status', colorByPoint: true, data: item.data }]
                }));
            });
        }

        // ── Chart: Posyandu ──
        function generateChartPosyandu(data) {
            Highcharts.chart('chart_posyandu', bojaChartOpts({
                chart: { type: 'column', borderRadius: 6 },
                title: { text: 'Grafik Kasus Stunting per Posyandu' },
                xAxis: {
                    categories: data.categories,
                    labels: { style: { fontSize: '11px', fontFamily: chartFontFamily } }
                },
                yAxis: {
                    min: 0,
                    title: { text: 'Angka Kasus', style: { fontFamily: chartFontFamily } },
                    gridLineColor: '#E5E7EB'
                },
                plotOptions: {
                    column: {
                        borderRadius: 4,
                        borderWidth: 0,
                        groupPadding: 0.15
                    }
                },
                colors: [chartColors.blue, chartColors.green, chartColors.accent],
                series: data.data
            }));
        }

        // ── Scorecard ──
        function generateScorecard(scorecard) {
            $.post(scorecardUrl, { scorecard: scorecard }, function(html) {
                scorecardNode.innerHTML = '<div class="kes-scorecard-wrap">' + html + '</div>';
            });
        }

        // ── Init ──
        $(document).ready(function() {
            var tahun = $('#tahun').val();
            var kuartal = $('#kuartal').val();
            var idPosyandu = $('#id_posyandu').val();
            loadStunting(tahun, kuartal, idPosyandu);
        });

    })(jQuery);
    </script>
@endpush
