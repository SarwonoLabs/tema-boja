@extends('theme::layouts.right-sidebar')
@include('core::admin.layouts.components.asset_numeral')

@section('content')
    <nav class="breadcrumb-boja" aria-label="breadcrumb">
        <ol>
            <li><a href="{{ ci_route() }}"><i class="fas fa-home"></i> Beranda</a></li>
            <li><a href="{{ site_url('inventaris') }}">Inventaris</a></li>
            <li>{{ $judul }}</li>
        </ol>
    </nav>

    <div class="boja-page-header">
        <div class="boja-page-header-text">
            <h1 class="boja-page-title"><i class="fas fa-tools"></i> {{ $judul }}</h1>
            <p class="boja-page-subtitle">Data inventaris peralatan dan mesin {{ ucwords(strtolower(setting('sebutan_desa') . ' ' . ($desa['nama_desa'] ?? ''))) }}</p>
        </div>
    </div>

    <div class="boja-table-wrap">
        <div class="boja-table-inner">
            <table id="inventaris" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th rowspan="2" style="vertical-align:middle">No</th>
                        <th rowspan="2" style="vertical-align:middle">Nama Barang</th>
                        <th rowspan="2" style="vertical-align:middle">Kode Barang / Nomor Registrasi</th>
                        <th rowspan="2" style="vertical-align:middle">Merk/Type</th>
                        <th rowspan="2" style="vertical-align:middle">Tahun Pembelian</th>
                        <th colspan="2">Nomor</th>
                        <th rowspan="2" style="vertical-align:middle">Asal Usul</th>
                        <th rowspan="2" style="vertical-align:middle">Harga (Rp)</th>
                    </tr>
                    <tr>
                        <th>Polisi</th>
                        <th>BPKB</th>
                    </tr>
                </thead>
                <tbody id="inventaris-tbody"></tbody>
                <tfoot id="inventaris-tfoot">
                    <tr>
                        <th colspan="8" style="text-align:right">Total:</th>
                        <th class="total" style="text-align:right"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            var _url = `{{ ci_route('internal_api.inventaris-peralatan') }}`;
            var _tbody = document.getElementById('inventaris-tbody');
            var _tfoot = document.getElementById('inventaris-tfoot');
            $.ajax({
                url: _url, type: 'GET',
                beforeSend: function() { _tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:30px"><i class="fas fa-spinner fa-spin" style="color:var(--primary)"></i> Memuat data...</td></tr>'; },
                success: function(response) {
                    var rows = [], _total = 0;
                    if (response.data.length) {
                        response.data.forEach(function(el, key) {
                            var a = el.attributes;
                            rows.push('<tr><td style="text-align:center;font-weight:600;color:#6b7280">' + (key+1) + '</td><td style="font-weight:600">' + a.nama_barang + '</td><td>' + a.kode_barang + '<br>' + a.register + '</td><td>' + a.merk + '</td><td style="text-align:center">' + a.tahun_pengadaan + '</td><td>' + a.no_polisi + '</td><td>' + a.no_bpkb + '</td><td>' + a.asal + '</td><td style="text-align:right">' + a.harga_format + '</td></tr>');
                            _total += a.harga;
                        });
                        _tfoot.querySelector('th.total').innerHTML = numeral(_total).format();
                        _tbody.innerHTML = rows.join('');
                    } else { _tfoot.remove(); _tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:24px;color:#6b7280">Belum ada data</td></tr>'; }
                    setTimeout(function() {
                        $('#inventaris').DataTable({
                            columnDefs: [{ targets: [0], orderable: false }], order: [[1, 'asc']],
                            language: { search: '<i class="fas fa-search"></i>', searchPlaceholder: 'Cari...', lengthMenu: 'Tampilkan _MENU_ data', info: '_START_ - _END_ dari _TOTAL_', infoEmpty: 'Tidak ada data', zeroRecords: 'Tidak ditemukan', paginate: { first: '«', previous: '‹', next: '›', last: '»' } }
                        });
                    }, 500);
                },
                dataType: 'json'
            });
        });
    </script>
@endpush
