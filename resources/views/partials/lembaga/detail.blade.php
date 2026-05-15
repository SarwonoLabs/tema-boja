@extends('theme::layouts.full-content')

@section('content')
    @include('theme::commons.asset_sweetalert')

    {{-- ═══ Breadcrumb ═══ --}}
    <nav class="breadcrumb-boja" aria-label="breadcrumb">
        <ol>
            <li><a href="{{ ci_route() }}"><i class="fas fa-home"></i> Beranda</a></li>
            <li id="nav-tipe">Data {{ ucfirst($tipe) }}</li>
        </ol>
    </nav>

    {{-- Loading --}}
    <div id="kelompok-loading" style="text-align:center;padding:40px">
        <i class="fas fa-spinner fa-spin" style="color:var(--primary);font-size:1.4rem"></i>
        <p style="margin-top:8px;color:#6b7280;font-size:.88rem">Memuat data {{ $tipe }}...</p>
    </div>

    {{-- ═══ Content Wrapper ═══ --}}
    <div id="kelompok-wrapper" style="display:none"></div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var route = "{{ route('api.' . $tipe . '.detail', ['slug' => $slug]) }}";
            $.ajax({
                url: route,
                method: 'GET',
                success: function(data) {
                    var detail = data.data.attributes;
                    var pengurus = detail.pengurus;
                    var tipe = detail.tipe;
                    var gambar_desa = detail.logo;

                    $('#nav-tipe').text('Data ' + tipe);

                    // ── Page Header ──
                    var headerIcon = tipe.toLowerCase() === 'lembaga' ? 'fas fa-building' : 'fas fa-users';
                    var headerEl = '' +
                        '<div class="boja-page-header">' +
                            '<div class="boja-page-header-text">' +
                                '<h1 class="boja-page-title"><i class="' + headerIcon + '"></i> ' + detail.nama + '</h1>' +
                                '<p class="boja-page-subtitle">Data ' + tipe + ' — ' + detail.kategori + '</p>' +
                            '</div>' +
                        '</div>';

                    // ── Detail Info Card ──
                    var detailEl = '' +
                        '<div class="kel-detail-card">' +
                            '<div class="kel-detail-body">' +
                                '<div class="kel-detail-info">' +
                                    '<div class="kel-detail-row"><span class="kel-detail-label">Nama ' + tipe + '</span><span class="kel-detail-value">' + detail.nama + '</span></div>' +
                                    '<div class="kel-detail-row"><span class="kel-detail-label">Kode</span><span class="kel-detail-value">' + detail.kode + '</span></div>' +
                                    '<div class="kel-detail-row"><span class="kel-detail-label">Kategori</span><span class="kel-detail-value">' + detail.kategori + '</span></div>' +
                                    '<div class="kel-detail-row"><span class="kel-detail-label">No. SK Pendirian</span><span class="kel-detail-value">' + detail.no_sk_pendirian + '</span></div>' +
                                    '<div class="kel-detail-row"><span class="kel-detail-label">Keterangan</span><span class="kel-detail-value">' + (detail.keterangan || '-') + '</span></div>' +
                                '</div>' +
                                (gambar_desa ? '<div class="kel-detail-logo"><img src="' + gambar_desa + '" alt="Logo ' + tipe + '"></div>' : '') +
                            '</div>' +
                        '</div>';

                    // ── Pengurus Table ──
                    var pengurusEl = '' +
                        '<div class="boja-table-wrap">' +
                            '<div class="kel-section-title"><i class="fas fa-user-tie"></i> Daftar Pengurus</div>' +
                            '<div class="boja-table-inner">' +
                                '<table class="display" style="width:100%">' +
                                    '<thead><tr><th width="5%">No</th><th>Jabatan</th><th>Nama</th><th>Alamat</th></tr></thead>' +
                                    '<tbody>';

                    pengurus.forEach(function(d, i) {
                        pengurusEl += '<tr><td class="text-center">' + (i + 1) + '</td><td>' + d.nama_jabatan + '</td><td style="white-space:nowrap;font-weight:600">' + d.nama_penduduk + '</td><td>' + d.alamat_lengkap + '</td></tr>';
                    });

                    pengurusEl += '</tbody></table></div></div>';

                    // ── Anggota Table ──
                    var anggotaEl = '' +
                        '<div class="boja-table-wrap">' +
                            '<div class="kel-section-title"><i class="fas fa-user-friends"></i> Daftar Anggota</div>' +
                            '<div class="boja-table-inner">' +
                                '<table class="display" id="tabel-data" style="width:100%">' +
                                    '<thead><tr><th width="5%">No</th><th width="15%">No. Anggota</th><th>Nama</th><th>Alamat</th><th width="14%">Jenis Kelamin</th></tr></thead>' +
                                    '<tbody></tbody>' +
                                '</table>' +
                            '</div>' +
                        '</div>';

                    $('#kelompok-loading').hide();
                    $('#kelompok-wrapper').html(headerEl + detailEl + pengurusEl + anggotaEl).show();

                    anggotaTable();
                },
                error: function(xhr) {
                    $('#kelompok-loading').html('<div style="text-align:center;padding:40px;color:#ef4444"><i class="fas fa-exclamation-circle" style="font-size:1.4rem"></i><p style="margin-top:8px;font-size:.88rem">Gagal memuat data {{ $tipe }}.</p></div>');
                    console.error('AJAX Error:', xhr.responseText);
                    Swal.fire('Error', 'Terjadi kesalahan saat memuat data.', 'error');
                }
            });

            var anggotaTable = function() {
                $('#tabel-data').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    ordering: true,
                    ajax: {
                        url: '{{ route("api." . $tipe . ".anggota", ["slug" => $slug]) }}',
                        method: 'GET',
                        data: function(row) {
                            return {
                                "page[size]": row.length,
                                "page[number]": (row.start / row.length) + 1,
                                "filter[search]": row.search.value,
                                "sort": (row.order[0] && row.order[0].dir === "asc" ? "" : "-") + (row.columns[row.order[0] && row.order[0].column] ? row.columns[row.order[0].column].name : "")
                            };
                        },
                        dataSrc: function(json) {
                            json.recordsTotal = json.meta.pagination.total;
                            json.recordsFiltered = json.meta.pagination.total;
                            return json.data;
                        },
                        error: function(xhr) {
                            console.error('AJAX Error:', xhr.responseText);
                        }
                    },
                    columnDefs: [{
                        targets: '_all',
                        className: 'text-nowrap'
                    }],
                    columns: [
                        { data: null, searchable: false, orderable: false },
                        { data: 'no_anggota', name: 'no_anggota', render: function(d, t, row) { return row.attributes.no_anggota; } },
                        { data: 'nama', name: 'nama', className: 'text-wrap', render: function(d, t, row) { return '<span style="font-weight:600">' + row.attributes.anggota.nama + '</span>'; } },
                        { data: 'alamat', name: 'alamat', render: function(d, t, row) { return row.attributes.alamat_lengkap; } },
                        { data: 'jenis_kelamin', name: 'jenis_kelamin', className: 'text-center', render: function(d, t, row) { return row.attributes.sex; } }
                    ],
                    drawCallback: function(settings) {
                        var api = this.api();
                        api.column(0, { search: 'applied', order: 'applied' }).nodes().each(function(cell, i) {
                            cell.innerHTML = api.page.info().start + i + 1;
                        });
                    }
                });
            };
        });
    </script>
@endpush
