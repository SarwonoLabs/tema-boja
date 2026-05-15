@extends('theme::layouts.right-sidebar')
@include('theme::commons.asset_sweetalert')

@section('content')
    {{-- ═══ Breadcrumb ═══ --}}
    <nav class="breadcrumb-boja" aria-label="breadcrumb">
        <ol>
            <li><a href="{{ ci_route() }}"><i class="fas fa-home"></i> Beranda</a></li>
            <li>Produk Hukum</li>
        </ol>
    </nav>

    {{-- ═══ Page Header ═══ --}}
    <div class="boja-page-header">
        <div class="boja-page-header-text">
            <h1 class="boja-page-title"><i class="fas fa-gavel"></i> Produk Hukum</h1>
            <p class="boja-page-subtitle">Dokumen peraturan dan produk hukum {{ ucwords(strtolower(setting('sebutan_desa') . ' ' . ($desa['nama_desa'] ?? ''))) }}</p>
        </div>
    </div>

    {{-- ═══ Filter Bar ═══ --}}
    <div class="prodhuk-filter-bar">
        <div class="prodhuk-filter-group">
            <label class="prodhuk-filter-label"><i class="fas fa-calendar-alt"></i> Tahun</label>
            <select class="prodhuk-filter-select" id="list_tahun" name="tahun">
                <option selected="" value="">Semua Tahun</option>
            </select>
        </div>
        <div class="prodhuk-filter-group">
            <label class="prodhuk-filter-label"><i class="fas fa-bookmark"></i> Jenis Peraturan</label>
            <select class="prodhuk-filter-select" id="list_kategori" name="kategori">
                <option selected="" value="">Semua Jenis</option>
            </select>
        </div>
    </div>

    {{-- ═══ DataTable ═══ --}}
    <div class="boja-table-wrap">
        <div class="boja-table-inner">
            <table class="display" id="tabelData" style="width:100%">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Judul Produk Hukum</th>
                        <th width="18%">Jenis Peraturan</th>
                        <th width="10%">Tahun</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tfoot></tfoot>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // ═══ Populate Filter: Tahun ═══
            $.get('{{ route('api.tahun-produk-hukum') }}', function(data) {
                var sel = $('#list_tahun');
                data.data.forEach(function(item) { sel.append('<option value="' + item + '">' + item + '</option>'); });
            });

            // ═══ Populate Filter: Kategori ═══
            $.get('{{ route('api.kategori-produk-hukum') }}', function(data) {
                var sel = $('#list_kategori');
                data.data.forEach(function(item) { sel.append('<option value="' + item.id + '">' + item.attributes.nama + '</option>'); });
            });

            // ═══ DataTable ═══
            var tabelData = $('#tabelData').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ordering: true,
                language: {
                    search: '<i class="fas fa-search"></i>',
                    searchPlaceholder: 'Cari produk hukum...',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ - _END_ dari _TOTAL_ dokumen',
                    infoEmpty: 'Tidak ada data',
                    infoFiltered: '(disaring dari _MAX_ total)',
                    zeroRecords: 'Tidak ditemukan dokumen yang cocok',
                    paginate: { first: '«', previous: '‹', next: '›', last: '»' },
                    processing: '<div style="display:flex;align-items:center;gap:8px;justify-content:center"><i class="fas fa-spinner fa-spin" style="color:var(--primary)"></i> Memuat data...</div>'
                },
                ajax: {
                    url: `{{ route('api.produk-hukum') }}`,
                    method: 'GET',
                    data: function(row) {
                        var tahun = $('#list_tahun').val();
                        var kategori = $('#list_kategori').val();
                        var params = {
                            "page[size]": row.length,
                            "page[number]": (row.start / row.length) + 1,
                            "filter[search]": row.search.value,
                            "sort": (row.order[0]?.dir === "asc" ? "" : "-") + row.columns[row.order[0]?.column]?.name
                        };
                        if (tahun) params['filter[tahun]'] = tahun;
                        if (kategori) params['filter[kategori]'] = kategori;
                        return params;
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
                columns: [
                    {
                        data: null, searchable: false, orderable: false, className: 'text-center',
                        render: function() { return ''; }
                    },
                    {
                        data: 'nama', name: 'nama',
                        render: function(data, type, row) {
                            return '<span style="font-weight:600">' + row.attributes.nama + '</span>';
                        }
                    },
                    {
                        data: 'kategori', name: 'kategori',
                        render: function(data, type, row) {
                            return '<span class="prodhuk-badge-kategori"><i class="fas fa-tag"></i> ' + row.attributes.kategori + '</span>';
                        }
                    },
                    {
                        data: 'tahun', name: 'tahun', className: 'text-center',
                        render: function(data, type, row) {
                            return '<span class="prodhuk-badge-tahun"><i class="fas fa-calendar"></i> ' + row.attributes.tahun + '</span>';
                        }
                    },
                    {
                        data: null, searchable: false, orderable: false, className: 'text-center',
                        render: function(data, type, row) {
                            if (row.attributes.satuan || row.attributes.url) {
                                return '<button class="prodhuk-btn-view lihat-dokumen" data-nama="' + row.attributes.nama + '" data-url="' + row.attributes.url + '" data-file="' + row.attributes.satuan + '"><i class="fas fa-file-alt"></i> Lihat</button>';
                            }
                            return '<span style="color:#9ca3af;font-size:.8rem">-</span>';
                        }
                    }
                ],
                order: [[3, 'desc']],
                drawCallback: function() {
                    var api = this.api();
                    api.column(0, { search: 'applied', order: 'applied' }).nodes().each(function(cell, i) {
                        cell.innerHTML = '<span style="font-weight:600;color:#6b7280">' + (api.page.info().start + i + 1) + '</span>';
                    });
                }
            });

            // ═══ Filter Change ═══
            $(document).on('change', '#list_tahun, #list_kategori', function() {
                tabelData.ajax.reload();
            });

            // ═══ View Document Modal ═══
            $(document).on('click', '.lihat-dokumen', function() {
                var nama = $(this).data('nama');
                var file = $(this).data('file');

                nama = nama.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');

                if (!file) {
                    Swal.fire('Error', 'File tidak ditemukan.', 'error');
                    return;
                }

                Swal.fire({
                    title: '<h4 style="margin-bottom: 10px;">Lihat Dokumen</h4>',
                    html: '<div style="display:flex;flex-direction:column;align-items:center;width:100%;gap:15px">' +
                        '<iframe src="' + file + '" style="width:100%;min-height:400px;border:1px solid #ddd;border-radius:8px"></iframe>' +
                        '<button class="prodhuk-btn-view unduh-dokumen" data-nama="' + nama + '" data-file="' + file + '" style="padding:10px 24px;font-size:.85rem"><i class="fas fa-download"></i> Unduh File</button>' +
                        '</div>',
                    width: '60%',
                    heightAuto: true,
                    showCloseButton: true,
                    showConfirmButton: false,
                    showCancelButton: false,
                    didOpen: function() {
                        $(".unduh-dokumen").on("click", function(e) {
                            e.preventDefault();
                            var pdfUrl = $(this).data("file");
                            var fileName = $(this).data("nama") || "document.pdf";

                            if (pdfUrl.includes("drive.google.com")) {
                                var fileId = '';
                                if (pdfUrl.includes('/d/')) {
                                    fileId = pdfUrl.split('/d/')[1].split('/')[0];
                                } else if (pdfUrl.includes('id=')) {
                                    var urlParams = new URLSearchParams(new URL(pdfUrl).search);
                                    fileId = urlParams.get('id');
                                }
                                if (fileId) {
                                    pdfUrl = 'https://drive.google.com/uc?export=download&id=' + fileId;
                                }
                            }

                            var link = $("<a>").attr("href", pdfUrl).attr("download", fileName).css("display", "none").appendTo("body");
                            link[0].click();
                            link.remove();
                        });
                    }
                });
            });
        });
    </script>
@endpush
