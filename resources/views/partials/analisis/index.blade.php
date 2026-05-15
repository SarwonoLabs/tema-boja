@extends('theme::layouts.full-content')
@include('theme::commons.asset_sweetalert')

@section('content')
    {{-- ═══ Breadcrumb ═══ --}}
    <nav class="breadcrumb-boja" aria-label="breadcrumb">
        <ol>
            <li><a href="{{ ci_route() }}"><i class="fas fa-home"></i> Beranda</a></li>
            <li>Analisis</li>
        </ol>
    </nav>

    {{-- ═══ Page Header ═══ --}}
    <div class="boja-page-header">
        <div class="boja-page-header-text">
            <h1 class="boja-page-title"><i class="fas fa-chart-bar"></i> Data Analisis Desa</h1>
            <p class="boja-page-subtitle">Agregasi data hasil analisis dan pendataan desa</p>
        </div>
    </div>

    {{-- ═══ Detail & Selector Card ═══ --}}
    <div class="analisis-control-card">
        <div class="analisis-control-header">
            <i class="fas fa-sliders-h"></i> Pilih Data Analisis
        </div>
        <div class="analisis-control-body">
            <div class="analisis-select-wrap">
                <label for="master" class="analisis-select-label">Analisis:</label>
                <select class="analisis-select" id="master" name="master"></select>
            </div>
            <div class="analisis-meta">
                <div class="analisis-meta-item">
                    <span class="analisis-meta-label"><i class="fas fa-clipboard-list"></i> Pendataan</span>
                    <span class="analisis-meta-value" id="pendataan-detail">-</span>
                </div>
                <div class="analisis-meta-item">
                    <span class="analisis-meta-label"><i class="fas fa-user-friends"></i> Subjek</span>
                    <span class="analisis-meta-value" id="subjek-detail">-</span>
                </div>
                <div class="analisis-meta-item">
                    <span class="analisis-meta-label"><i class="fas fa-calendar-alt"></i> Tahun</span>
                    <span class="analisis-meta-value" id="tahun-detail">-</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ DataTable Indikator ═══ --}}
    <div class="boja-table-wrap">
        <div class="analisis-section-title"><i class="fas fa-list-ol"></i> Daftar Indikator</div>
        <div class="boja-table-inner">
            <table class="display" id="table-indikator" style="width:100%">
                <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th>Indikator</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var routeApiMaster = '{{ route('api.analisis.master') }}';

            $.get(routeApiMaster, function(data) {
                var $masterSelect = $('#master');

                data.data.forEach(function(item) {
                    $masterSelect.append('<option value="' + item.id + '">' + item.attributes.master + ' (' + item.attributes.tahun + ')</option>');
                });

                $masterSelect.on('change', function() {
                    var selectedId = $(this).val();
                    var selectedItem = null;
                    data.data.forEach(function(item) {
                        if (item.id === selectedId) selectedItem = item;
                    });

                    if (selectedItem) {
                        $('#pendataan-detail').text(selectedItem.attributes.master || '-');
                        $('#subjek-detail').text(selectedItem.attributes.subjek || '-');
                        $('#tahun-detail').text(selectedItem.attributes.tahun || '-');
                    }
                });

                var firstItem = data.data[0];
                if (firstItem) {
                    $('#pendataan-detail').text(firstItem.attributes.master || '-');
                    $('#subjek-detail').text(firstItem.attributes.subjek || '-');
                    $('#tahun-detail').text(firstItem.attributes.tahun || '-');
                    $masterSelect.val(firstItem.id).trigger('change');
                }
            });

            var routeApiIndikator = '{{ route('api.analisis.indikator') }}';

            var tabelData = $('#table-indikator').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ordering: false,
                searching: false,
                ajax: {
                    url: routeApiIndikator,
                    method: 'GET',
                    data: function(row) {
                        return {
                            "filter[id_master]": $('#master').val(),
                            "page[size]": row.length,
                            "page[number]": (row.start / row.length) + 1
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
                columnDefs: [{
                    targets: '_all',
                    className: 'text-nowrap'
                }],
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'attributes.indikator',
                        name: 'attributes.indikator',
                        className: 'text-wrap',
                        render: function(data, type, row) {
                            var url = '/jawaban_analisis?filter[id_indikator]=' + row.id + '&filter[subjek_tipe]=' + row.attributes.subjek_tipe + '&filter[id_periode]=' + row.attributes.id_periode;
                            return '<a href="' + url + '" class="analisis-indikator-link"><i class="fas fa-chart-pie"></i> ' + row.attributes.indikator + '</a>';
                        }
                    }
                ],
                drawCallback: function(settings) {
                    var api = this.api();
                    api.column(0, {
                        search: 'applied',
                        order: 'applied'
                    }).nodes().each(function(cell, i) {
                        cell.innerHTML = api.page.info().start + i + 1;
                    });
                }
            });

            $('#master').on('change', function() {
                tabelData.ajax.reload();
            });
        });
    </script>
@endpush
