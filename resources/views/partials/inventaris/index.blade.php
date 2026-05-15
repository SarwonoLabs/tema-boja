@extends('theme::layouts.right-sidebar')

@section('content')
    {{-- ═══ Breadcrumb ═══ --}}
    <nav class="breadcrumb-boja" aria-label="breadcrumb">
        <ol>
            <li><a href="{{ ci_route() }}"><i class="fas fa-home"></i> Beranda</a></li>
            <li>Inventaris</li>
        </ol>
    </nav>

    {{-- ═══ Page Header ═══ --}}
    <div class="boja-page-header">
        <div class="boja-page-header-text">
            <h1 class="boja-page-title"><i class="fas fa-warehouse"></i> Inventaris {{ ucwords(setting('sebutan_desa')) }}</h1>
            <p class="boja-page-subtitle">Data aset dan barang milik {{ ucwords(strtolower(setting('sebutan_desa') . ' ' . ($desa['nama_desa'] ?? ''))) }}</p>
        </div>
    </div>

    {{-- ═══ Summary Cards — filled via JS ═══ --}}
    <div class="inv-summary-grid" id="invSummaryGrid" style="display:none">
        <div class="inv-summary-card">
            <div class="inv-summary-icon"><i class="fas fa-shopping-cart"></i></div>
            <div class="inv-summary-info">
                <div class="inv-summary-label">Dibeli Sendiri</div>
                <div class="inv-summary-value" id="invTotalPribadi">0</div>
            </div>
        </div>
        <div class="inv-summary-card">
            <div class="inv-summary-icon"><i class="fas fa-landmark"></i></div>
            <div class="inv-summary-info">
                <div class="inv-summary-label">Bantuan Pemerintah</div>
                <div class="inv-summary-value" id="invTotalPemerintah">0</div>
            </div>
        </div>
        <div class="inv-summary-card">
            <div class="inv-summary-icon"><i class="fas fa-building"></i></div>
            <div class="inv-summary-info">
                <div class="inv-summary-label">Bantuan Provinsi</div>
                <div class="inv-summary-value" id="invTotalProvinsi">0</div>
            </div>
        </div>
        <div class="inv-summary-card">
            <div class="inv-summary-icon"><i class="fas fa-city"></i></div>
            <div class="inv-summary-info">
                <div class="inv-summary-label">Bantuan Kabupaten</div>
                <div class="inv-summary-value" id="invTotalKabupaten">0</div>
            </div>
        </div>
        <div class="inv-summary-card">
            <div class="inv-summary-icon"><i class="fas fa-hand-holding-heart"></i></div>
            <div class="inv-summary-info">
                <div class="inv-summary-label">Sumbangan</div>
                <div class="inv-summary-value" id="invTotalSumbangan">0</div>
            </div>
        </div>
    </div>

    {{-- ═══ DataTable ═══ --}}
    <div class="boja-table-wrap">
        <div class="boja-table-inner">
            <table class="display" id="inventaris" style="width:100%">
                <thead>
                    <tr>
                        <th rowspan="3" style="vertical-align:middle">No</th>
                        <th rowspan="3" style="vertical-align:middle">Jenis Barang</th>
                        <th rowspan="3" style="vertical-align:middle;min-width:280px">Keterangan</th>
                        <th colspan="5" style="vertical-align:middle">Asal Barang</th>
                        <th rowspan="3" style="vertical-align:middle">Aksi</th>
                    </tr>
                    <tr>
                        <th rowspan="2">Dibeli Sendiri</th>
                        <th colspan="3">Bantuan</th>
                        <th rowspan="2">Sumbangan</th>
                    </tr>
                    <tr>
                        <th>Pemerintah</th>
                        <th>Provinsi</th>
                        <th>Kabupaten</th>
                    </tr>
                </thead>
                <tbody id="inventaris-tbody"></tbody>
                <tfoot id="inventaris-tfoot">
                    <tr>
                        <th colspan="3" style="text-align:right">Total</th>
                        <th class="pribadi text-center"></th>
                        <th class="pemerintah text-center"></th>
                        <th class="provinsi text-center"></th>
                        <th class="kabupaten text-center"></th>
                        <th class="sumbangan text-center"></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            var _url = `{{ ci_route('internal_api.inventaris') }}`;
            var _tbody = document.getElementById('inventaris-tbody');
            var _tfoot = document.getElementById('inventaris-tfoot');

            $.ajax({
                url: _url,
                type: 'GET',
                beforeSend: function() {
                    _tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:30px"><i class="fas fa-spinner fa-spin" style="color:var(--primary);font-size:1.2rem"></i> <span style="color:#6b7280;margin-left:8px">Memuat data inventaris...</span></td></tr>';
                },
                success: function(response) {
                    var rows = [];
                    var _total = { pribadi: 0, pemerintah: 0, provinsi: 0, kabupaten: 0, sumbangan: 0 };

                    response.data[0].attributes.forEach(function(el, key) {
                        rows.push('<tr>' +
                            '<td style="text-align:center;font-weight:600;color:#6b7280">' + (key + 1) + '</td>' +
                            '<td style="font-weight:600">' + el.jenis + '</td>' +
                            '<td>' + el.ket + '</td>' +
                            '<td style="text-align:center">' + el.pribadi + '</td>' +
                            '<td style="text-align:center">' + el.pemerintah + '</td>' +
                            '<td style="text-align:center">' + el.provinsi + '</td>' +
                            '<td style="text-align:center">' + el.kabupaten + '</td>' +
                            '<td style="text-align:center">' + el.sumbangan + '</td>' +
                            '<td style="text-align:center"><a href="' + el.url + '" class="inv-btn-detail" title="Lihat Data"><i class="fas fa-eye"></i> Detail</a></td>' +
                        '</tr>');
                        for (var i in _total) { _total[i] += el[i]; }
                    });

                    for (var i in _total) {
                        _tfoot.querySelector('th.' + i).innerHTML = '<strong>' + _total[i] + '</strong>';
                    }
                    _tbody.innerHTML = rows.join('');

                    // Fill summary cards
                    $('#invTotalPribadi').text(_total.pribadi);
                    $('#invTotalPemerintah').text(_total.pemerintah);
                    $('#invTotalProvinsi').text(_total.provinsi);
                    $('#invTotalKabupaten').text(_total.kabupaten);
                    $('#invTotalSumbangan').text(_total.sumbangan);
                    $('#invSummaryGrid').show();

                    setTimeout(function() {
                        $('#inventaris').DataTable({
                            columnDefs: [{ targets: [0, 8], orderable: false }],
                            order: [[1, 'asc']],
                            language: {
                                search: '<i class="fas fa-search"></i>',
                                searchPlaceholder: 'Cari barang...',
                                lengthMenu: 'Tampilkan _MENU_ data',
                                info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                                infoEmpty: 'Tidak ada data',
                                zeroRecords: 'Tidak ditemukan data yang cocok',
                                paginate: { first: '«', previous: '‹', next: '›', last: '»' }
                            },
                            drawCallback: function(settings) {
                                var api = this.api();
                                api.column(0, { search: 'applied', order: 'applied' }).nodes().each(function(cell, i) {
                                    cell.innerHTML = '<span style="font-weight:600;color:#6b7280">' + (api.page.info().start + i + 1) + '</span>';
                                });
                            }
                        });
                    }, 500);
                },
                dataType: 'json'
            });
        });
    </script>
@endpush
