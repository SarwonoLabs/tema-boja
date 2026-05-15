@extends('theme::layouts.full-content')
@include('theme::commons.asset_sweetalert')
@include('theme::commons.asset_highcharts')

@section('content')
    {{-- ═══ Breadcrumb ═══ --}}
    <nav class="breadcrumb-boja" aria-label="breadcrumb">
        <ol>
            <li><a href="{{ ci_route() }}"><i class="fas fa-home"></i> Beranda</a></li>
            <li><a href="{{ ci_route('analisis') }}">Analisis</a></li>
            <li>Jawaban Analisis</li>
        </ol>
    </nav>

    {{-- ═══ Page Header ═══ --}}
    <div class="boja-page-header">
        <div class="boja-page-header-text">
            <h1 class="boja-page-title" id="indikator"><i class="fas fa-chart-pie"></i> Jawaban Analisis</h1>
            <p class="boja-page-subtitle">Grafik dan tabel jawaban responden</p>
        </div>
    </div>

    {{-- ═══ Chart Card ═══ --}}
    <div class="analisis-chart-card">
        <div class="analisis-chart-header"><i class="fas fa-chart-column"></i> Grafik Jawaban</div>
        <div class="analisis-chart-body">
            <div id="chart" style="width:100%;min-height:320px"></div>
        </div>
    </div>

    {{-- ═══ DataTable Jawaban ═══ --}}
    <div class="boja-table-wrap">
        <div class="analisis-section-title"><i class="fas fa-poll"></i> Tabel Jawaban Responden</div>
        <div class="boja-table-inner">
            <table class="display" id="table-jawaban" style="width:100%">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Jawaban</th>
                        <th width="16%">Jumlah Responden</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var chart;

        $.get("{{ route('api.analisis.indikator') . "?filter[id]={$params['filter']['id_indikator']}" }}", function(data) {
            var indikator = data && data.data && data.data[0] ? data.data[0].attributes.indikator : 'Indikator';
            $('#indikator').html('<i class="fas fa-chart-pie"></i> ' + indikator);
            printChart(indikator);
        });

        var tabelData = $('#table-jawaban').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: false,
            searching: false,
            ajax: {
                url: '{{ route('api.analisis.jawaban') }}',
                method: 'GET',
                data: function(row) {
                    return $.extend({}, @json($params), {
                        "page[size]": row.length,
                        "page[number]": (row.start / row.length) + 1
                    });
                },
                dataSrc: function(json) {
                    json.recordsTotal = json.meta.pagination.total;
                    json.recordsFiltered = json.meta.pagination.total;
                    return json.data;
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr.responseText);
                    Swal.fire('Error', 'Terjadi kesalahan saat memuat data.', 'error');
                }
            },
            columnDefs: [{
                targets: '_all',
                className: 'text-nowrap'
            }],
            columns: [
                { data: null, searchable: false, orderable: false },
                { data: 'attributes.jawaban', className: 'text-wrap' },
                { data: 'attributes.jml', className: 'text-center' }
            ],
            drawCallback: function(settings) {
                var api = this.api();

                api.column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = api.page.info().start + i + 1;
                });

                var chartCategories = [];
                var chartData = [];

                api.rows().data().each(function(row) {
                    chartCategories.push(row.attributes.jawaban);
                    chartData.push(row.attributes.jml);
                });

                updateChart(chartCategories, chartData);
            }
        });

        printChart();

        function printChart(indikator) {
            chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'chart',
                    border: 0,
                    type: 'column',
                    style: { fontFamily: "'Plus Jakarta Sans', 'Nunito', sans-serif" }
                },
                title: {
                    text: indikator || '',
                    style: { fontSize: '14px', fontWeight: '700', color: '#1C4D35' }
                },
                xAxis: {
                    title: { text: '' },
                    categories: [],
                    labels: { style: { fontSize: '11px' } }
                },
                yAxis: {
                    title: { text: 'Jumlah Populasi', style: { fontSize: '12px' } }
                },
                legend: {
                    layout: 'vertical',
                    enabled: false
                },
                colors: ['#2F855A', '#38A169', '#48BB78', '#68D391', '#9AE6B4', '#D4AF37', '#276749'],
                plotOptions: {
                    series: { colorByPoint: true, borderRadius: 5 },
                    column: { pointPadding: 0.1, borderWidth: 0 }
                },
                series: [{
                    shadow: 1,
                    border: 0,
                    data: []
                }]
            });
        }

        function updateChart(categories, data) {
            chart.xAxis[0].setCategories(categories);
            chart.series[0].setData(data);
        }
    </script>
@endpush
