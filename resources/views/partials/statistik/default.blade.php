{{-- Breadcrumb --}}
<nav class="breadcrumb-boja" aria-label="breadcrumb">
    <ol>
        <li><a href="{{ ci_route() }}"><i class="fas fa-home"></i> Beranda</a></li>
        <li>Data Statistik</li>
    </ol>
</nav>

{{-- Page Title --}}
<div class="stat-page-title">
    <h1>{{ $judul }}</h1>
</div>

{{-- Year Filter --}}
@if (isset($list_tahun))
<div class="stat-filter-bar">
    <label for="tahun"><i class="fas fa-calendar-alt"></i> Tahun</label>
    <select class="stat-filter-select" id="tahun" name="tahun">
        <option value="">Semua Tahun</option>
        @foreach ($list_tahun as $item_tahun)
            <option @selected($item_tahun == $selected_tahun) value="{{ $item_tahun }}">{{ $item_tahun }}</option>
        @endforeach
    </select>
</div>
@endif

{{-- Chart Section --}}
<div class="stat-chart-section">
    <div class="stat-chart-header">
        <h2><i class="fas fa-chart-pie"></i> Grafik {{ $heading }}</h2>
        <div class="stat-chart-actions">
            <button class="stat-chart-btn stat-switch-btn" data-type="column">
                <i class="fas fa-chart-bar"></i> Bar
            </button>
            <button class="stat-chart-btn stat-switch-btn active" data-type="pie">
                <i class="fas fa-chart-pie"></i> Pie
            </button>
            <a href="{{ ci_route("data-statistik.{$slug_aktif}.cetak.cetak") }}?tahun={{ $selected_tahun }}" class="stat-chart-btn stat-chart-btn-accent" title="Cetak" target="_blank">
                <i class="fas fa-print"></i> Cetak
            </a>
            <a href="{{ ci_route("data-statistik.{$slug_aktif}.cetak.unduh") }}?tahun={{ $selected_tahun }}" class="stat-chart-btn stat-chart-btn-outline" title="Unduh" target="_blank">
                <i class="fas fa-download"></i> Unduh
            </a>
        </div>
    </div>
    <div class="stat-chart-wrap" id="statistics"></div>
</div>

{{-- Table Section --}}
<div class="stat-table-section">
    <div class="stat-table-header">
        <h2><i class="fas fa-table"></i> Tabel {{ $heading }}</h2>
    </div>
    <div class="stat-table-wrap">
        <table class="stat-table" id="table-statistik">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Kelompok</th>
                    <th colspan="2">Jumlah</th>
                    <th colspan="2">Laki-laki</th>
                    <th colspan="2">Perempuan</th>
                </tr>
                <tr>
                    <th>Jiwa</th>
                    <th>%</th>
                    <th>Jiwa</th>
                    <th>%</th>
                    <th>Jiwa</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <p class="stat-update-info">
            <i class="fas fa-clock"></i> Diperbarui pada: {{ tgl_indo($last_update) }}
        </p>
    </div>
    <div class="stat-table-actions">
        <button class="stat-chart-btn stat-chart-btn-outline" id="showData">
            <i class="fas fa-list"></i> Selengkapnya...
        </button>
        <button class="stat-chart-btn stat-chart-btn-outline" id="showZero">
            <i class="fas fa-eye"></i> Tampilkan Nol
        </button>
    </div>
</div>

{{-- Bantuan / Peserta Section --}}
@if (setting('daftar_penerima_bantuan') && $bantuan)
<script>
    const bantuanUrl = '{{ ci_route('internal_api.peserta_bantuan', $key) }}?filter[tahun]={{ $selected_tahun ?? '' }}'
</script>
<input id="stat" type="hidden" value="{{ $key }}">
<div class="stat-table-section" style="margin-top:24px">
    <div class="stat-table-header">
        <h2><i class="fas fa-hand-holding-heart"></i> Daftar {{ $heading }}</h2>
    </div>
    <div class="stat-table">
        <table class="stat-table" id="peserta_program">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Program</th>
                    <th>Nama Peserta</th>
                    <th>Alamat</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endif

@push('scripts')
<script>
let dataStats = [];
let statCharts = null;
let showStatus = true;

function switchType(type, alpha) {
    if (!statCharts) return;
    statCharts.update({
        chart: { options3d: { alpha: alpha } },
        series: [{ type: type }]
    });
}

function showZeroValue(show) {
    if (show) {
        $('.zero').parent().show();
    } else {
        $('.zero').parent().hide();
    }
}

function showHideToggle() {
    $('#showData').click();
    showZeroValue(showStatus);
    showStatus = !showStatus;
    if (showStatus) {
        $('#showZero').html('<i class="fas fa-eye"></i> Tampilkan Nol');
    } else {
        $('#showZero').html('<i class="fas fa-eye-slash"></i> Sembunyikan Nol');
    }
}

$(function() {
    // --- Peserta Bantuan DataTable ---
    @if (setting('daftar_penerima_bantuan') && $bantuan)
    if ($('#peserta_program').length) {
        var pesertaDatatable = $('#peserta_program').DataTable({
            processing: true,
            serverSide: true,
            order: [],
            ajax: {
                url: bantuanUrl,
                type: 'GET',
                data: function(row) {
                    return {
                        "page[size]": row.length,
                        "page[number]": (row.start / row.length) + 1,
                        "filter[search]": row.search.value,
                        "sort": (row.order[0]?.dir === "asc" ? "" : "-") + row.columns[row.order[0]?.column]?.name,
                    };
                },
                dataSrc: function(json) {
                    json.recordsTotal = json.meta.pagination.total;
                    json.recordsFiltered = json.meta.pagination.total;
                    return json.data;
                },
            },
            columns: [
                { data: null, orderable: false, searchable: false },
                { data: 'attributes.nama', name: 'nama' },
                { data: 'attributes.kartu_nama', name: 'kartu_nama' },
                { data: 'attributes.kartu_alamat', name: 'kartu_alamat', orderable: false, searchable: false },
            ],
            order: [1, 'asc'],
            language: {
                url: SITE_URL + "/assets/bootstrap/js/dataTables.indonesian.lang"
            }
        });
        pesertaDatatable.on('draw.dt', function() {
            var info = $('#peserta_program').DataTable().page.info();
            pesertaDatatable.column(0, { page: 'current' }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1 + info.start;
            });
        });
    }
    @endif

    // --- Load Statistik Data via AJAX ---
    $.ajax({
        url: `{{ ci_route('internal_api.statistik', $key) }}?tahun={{ $selected_tahun ?? '' }}`,
        method: 'get',
        beforeSend: function() { $('#showData').hide(); },
        success: function(json) {
            dataStats = json.data.map(function(item) {
                return {
                    id: item.id,
                    nama: item.attributes.nama,
                    jumlah: item.attributes.jumlah,
                    persen: item.attributes.persen,
                    laki: item.attributes.laki,
                    persen1: item.attributes.persen1,
                    perempuan: item.attributes.perempuan,
                    persen2: item.attributes.persen2
                };
            });

            // --- Populate Table ---
            var tbody = document.querySelector('#table-statistik tbody');
            var showBtn = false;
            dataStats.forEach(function(item, index) {
                var row = document.createElement('tr');
                if (index > 11 && !['666','777','888'].includes(item.id)) {
                    row.className = 'more';
                    showBtn = true;
                }
                var keys = ['id','nama','jumlah','persen','laki','persen1','perempuan','persen2'];
                keys.forEach(function(key) {
                    var cell = document.createElement('td');
                    var text = item[key];
                    var cls = 'text-right';
                    if (key === 'id') {
                        cls = 'text-center';
                        text = ['666','777','888'].includes(item.id) ? '' : (index + 1);
                    }
                    if (key === 'nama') cls = 'text-left';
                    if (key === 'jumlah' && item[key] <= 0 && !['666','777','888'].includes(item.id)) {
                        cls += ' zero';
                    }
                    cell.className = cls;
                    cell.textContent = text;
                    row.appendChild(cell);
                });
                tbody.appendChild(row);
            });
            if (showBtn) $('#showData').show();

            // --- Build Chart ---
            buildStatChart();
        }
    });

    // --- Build Highcharts ---
    function buildStatChart() {
        showZeroValue(false);
        var categories = [];
        var data = [];
        dataStats.forEach(function(stat) {
            if (stat.nama !== 'TOTAL' && stat.nama !== 'JUMLAH' && stat.nama !== 'PENERIMA') {
                categories.push(stat.nama);
                data.push([stat.nama, parseInt(stat.jumlah, 10)]);
            }
        });

        statCharts = new Highcharts.Chart({
            chart: {
                renderTo: 'statistics',
                options3d: { enabled: enable3d, alpha: 45, beta: 10 }
            },
            title: { text: null },
            yAxis: {
                showEmpty: false,
                title: { text: 'Jumlah Populasi' }
            },
            xAxis: { categories: categories },
            plotOptions: {
                series: { colorByPoint: true },
                column: {
                    pointPadding: -0.1,
                    borderWidth: 0,
                    showInLegend: false,
                    depth: 50,
                    viewDistance: 25
                },
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    showInLegend: true,
                    depth: 30,
                    innerSize: 30
                }
            },
            legend: { enabled: true },
            series: [{
                type: 'pie',
                name: 'Jumlah Populasi',
                shadow: 1,
                border: 1,
                data: data
            }]
        });
    }

    // --- Chart Type Switch ---
    $('.stat-switch-btn').on('click', function() {
        var chartType = $(this).data('type');
        var alpha = chartType === 'pie' ? 45 : 20;
        $(this).addClass('active').siblings('.stat-switch-btn').removeClass('active');
        switchType(chartType, alpha);
    });

    // --- Show More ---
    $('#showData').on('click', function() {
        $('tr.more').show();
        $(this).hide();
        showZeroValue(false);
    });

    // --- Toggle Zero ---
    $('#showZero').on('click', function() {
        showHideToggle();
    });

    // --- Year Filter ---
    $('#tahun').on('change', function() {
        var url = window.location.href.split('?')[0];
        window.location.href = url + '?tahun=' + $(this).val();
    });

    // --- Default chart type (from server) ---
    var _chartType = '{{ $default_chart_type ?? 'pie' }}';
    if (_chartType === 'column') {
        setTimeout(function() {
            $('.stat-switch-btn[data-type=column]').trigger('click');
        }, 1200);
    }
});
</script>
@endpush
