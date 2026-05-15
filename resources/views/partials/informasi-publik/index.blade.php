@extends('theme::layouts.full-content')
@include('theme::commons.asset_sweetalert')

@section('content')
    {{-- ═══ Breadcrumb ═══ --}}
    <nav class="breadcrumb-boja" aria-label="breadcrumb">
        <ol>
            <li><a href="{{ ci_route() }}"><i class="fas fa-home"></i> Beranda</a></li>
            <li>Informasi Publik</li>
        </ol>
    </nav>

    {{-- ═══ Page Header ═══ --}}
    <div class="boja-page-header">
        <div class="boja-page-header-text">
            <h1 class="boja-page-title"><i class="fas fa-book-open"></i> Informasi Publik</h1>
            <p class="boja-page-subtitle">Dokumen dan informasi publik {{ ucwords(strtolower(setting('sebutan_desa') . ' ' . ($desa['nama_desa'] ?? ''))) }}</p>
        </div>
    </div>

    {{-- ═══ DataTable ═══ --}}
    <div class="boja-table-wrap">
        <div class="boja-table-inner">
            <table id="tabelData" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Judul Informasi</th>
                        <th width="10%">Tahun</th>
                        <th width="16%">Kategori</th>
                        <th width="16%">Tgl Upload</th>
                        <th width="10%">Aksi</th>
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
            var route = '{{ route("api.informasi-publik") }}';

            var tabelData = $('#tabelData').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ordering: true,
                ajax: {
                    url: route,
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
                        Swal.fire('Error', 'Terjadi kesalahan saat memuat data.', 'error');
                    }
                },
                columnDefs: [{
                    targets: '_all',
                    className: 'text-nowrap'
                }],
                columns: [
                    {
                        data: null,
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'nama',
                        name: 'nama',
                        className: 'text-wrap',
                        render: function(data, type, row) {
                            return '<span style="font-weight:600;color:#1f2937">' + row.attributes.nama + '</span>';
                        }
                    },
                    {
                        data: 'tahun',
                        name: 'tahun',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return '<span class="infpub-badge-tahun">' + row.attributes.tahun + '</span>';
                        }
                    },
                    {
                        data: 'kategori',
                        name: 'kategori',
                        render: function(data, type, row) {
                            return '<span class="infpub-badge-kategori">' + row.attributes.kategori + '</span>';
                        }
                    },
                    {
                        data: 'tgl_upload',
                        name: 'tgl_upload',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return '<span style="font-size:.8rem;color:#6b7280"><i class="fas fa-calendar-alt" style="margin-right:4px;opacity:.6"></i>' + row.attributes.tgl_upload + '</span>';
                        }
                    },
                    {
                        data: null,
                        searchable: false,
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (row.attributes.satuan || row.attributes.url) {
                                return '<button class="inv-btn-detail lihat-dokumen" data-nama="' + row.attributes.nama + '" data-url="' + (row.attributes.url || '') + '" data-file="' + (row.attributes.satuan || '') + '"><i class="fas fa-eye"></i> Lihat</button>';
                            }
                            return '<span style="color:#d1d5db;font-size:.8rem">—</span>';
                        }
                    }
                ],
                order: [[4, 'desc']],
                drawCallback: function(settings) {
                    var api = this.api();
                    api.column(0, { search: 'applied', order: 'applied' }).nodes().each(function(cell, i) {
                        cell.innerHTML = api.page.info().start + i + 1;
                    });
                }
            });

            // Event listener untuk tombol lihat dokumen
            $(document).on('click', '.lihat-dokumen', function() {
                var nama = $(this).data('nama');
                var file = $(this).data('file') || $(this).data('url');

                nama = nama.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');

                if (!file) {
                    Swal.fire('Error', 'File tidak ditemukan.', 'error');
                    return;
                }

                Swal.fire({
                    title: '<h4 style="margin-bottom:10px;font-family:Plus Jakarta Sans,sans-serif;color:#1C4D35">Lihat Dokumen</h4>',
                    html: '<div style="display:flex;flex-direction:column;align-items:center;width:100%;gap:15px">' +
                        '<iframe src="' + file + '" style="width:100%;min-height:400px;border:1px solid #e5e7eb;border-radius:10px"></iframe>' +
                        '<button class="inv-btn-detail unduh-dokumen" data-nama="' + nama + '" data-file="' + file + '" style="padding:8px 20px;font-size:.84rem">' +
                        '<i class="fas fa-download"></i> Unduh File</button>' +
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

                            if (pdfUrl.indexOf("drive.google.com") !== -1) {
                                var fileId = '';
                                if (pdfUrl.indexOf('/d/') !== -1) {
                                    fileId = pdfUrl.split('/d/')[1].split('/')[0];
                                } else if (pdfUrl.indexOf('id=') !== -1) {
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
