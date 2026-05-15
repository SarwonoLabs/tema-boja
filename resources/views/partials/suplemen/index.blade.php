@extends('theme::layouts.right-sidebar')
@include('theme::commons.asset_sweetalert')

@section('content')
    <nav class="breadcrumb-boja" aria-label="breadcrumb">
        <ol>
            <li><a href="{{ ci_route() }}"><i class="fas fa-home"></i> Beranda</a></li>
            <li id="breadcrumb-nama">Data Suplemen</li>
        </ol>
    </nav>

    <div class="boja-page-header">
        <div class="boja-page-header-text">
            <h1 class="boja-page-title"><i class="fas fa-database"></i> <span id="header-judul">Data Suplemen</span></h1>
            <p class="boja-page-subtitle" id="header-subtitle">Memuat data...</p>
        </div>
    </div>

    {{-- Rincian Data Suplemen --}}
    <div class="suplemen-detail-card">
        <div class="suplemen-detail-title">
            <i class="fas fa-info-circle"></i> Rincian Data Suplemen
        </div>
        <div class="suplemen-detail-body">
            <div class="suplemen-detail-row">
                <span class="suplemen-detail-label">Nama Data</span>
                <span class="suplemen-detail-value" id="nama">—</span>
            </div>
            <div class="suplemen-detail-row">
                <span class="suplemen-detail-label">Sasaran Terdata</span>
                <span class="suplemen-detail-value" id="sasaran">—</span>
            </div>
            <div class="suplemen-detail-row">
                <span class="suplemen-detail-label">Keterangan</span>
                <span class="suplemen-detail-value" id="keterangan">—</span>
            </div>
        </div>
    </div>

    {{-- Daftar Terdata --}}
    <div class="boja-table-wrap">
        <div class="suplemen-table-title">
            <i class="fas fa-users"></i> Daftar Terdata
        </div>
        <div class="boja-table-inner">
            <table id="tabelData" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Nama</th>
                        <th>Tempat Lahir</th>
                        <th>Jenis Kelamin</th>
                        <th>Alamat</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            var apiSuplemen = `{{ route('api.suplemen') }}`;
            var params = {
                "filter[slug]": `{{ $slug }}`
            };

            $.get(apiSuplemen, params, function(response) {
                var suplemen = response.data[0];

                if (!suplemen) {
                    Swal.fire('Error', 'Data tidak ditemukan.', 'error');
                    return;
                }

                var nama = suplemen.attributes.nama;
                $('#header-judul').text(nama);
                $('#header-subtitle').text('Daftar terdata ' + nama.toLowerCase());
                $('#breadcrumb-nama').text(nama);
                $('#nama').text(nama);
                $('#sasaran').text(suplemen.attributes.nama_sasaran);
                $('#keterangan').text(suplemen.attributes.keterangan || '-');

                loadAnggota(suplemen.id);
            });

            function loadAnggota(id) {
                var routeSuplemenAnggota = apiSuplemen + '/' + id;

                $('#tabelData').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    ordering: true,
                    ajax: {
                        url: routeSuplemenAnggota,
                        method: 'GET',
                        data: function(row) {
                            return {
                                "page[size]": row.length,
                                "page[number]": (row.start / row.length) + 1,
                                "filter[search]": row.search.value,
                                "sort": (row.order[0] && row.order[0].dir === "asc" ? "" : "-") + (row.columns[row.order[0] ? row.order[0].column : 0] ? row.columns[row.order[0].column].name : '')
                            };
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
                    columnDefs: [{ targets: '_all', className: 'text-nowrap' }],
                    columns: [
                        { data: null, searchable: false, orderable: false, className: 'text-center' },
                        { data: "attributes.terdata_nama", name: 'tweb_penduduk.nama' },
                        { data: "attributes.tempatlahir", name: 'tweb_penduduk.tempatlahir' },
                        { data: "attributes.sex", name: 'tweb_penduduk.sex' },
                        { data: "attributes.alamat", name: 'tweb_penduduk.alamat', orderable: false }
                    ],
                    order: [[1, 'asc']],
                    drawCallback: function(settings) {
                        var api = this.api();
                        api.column(0, { search: 'applied', order: 'applied' }).nodes().each(function(cell, i) {
                            cell.innerHTML = api.page.info().start + i + 1;
                        });
                    },
                    language: {
                        search: '<i class="fas fa-search"></i>',
                        searchPlaceholder: 'Cari nama, alamat...',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ terdata',
                        infoEmpty: 'Tidak ada data terdata',
                        zeroRecords: 'Tidak ditemukan',
                        processing: '<i class="fas fa-spinner fa-spin" style="color:var(--primary);font-size:1.2rem"></i> Memuat...',
                        paginate: { first: '«', previous: '‹', next: '›', last: '»' }
                    }
                });
            }
        });
    </script>
@endpush
