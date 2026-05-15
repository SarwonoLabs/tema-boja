@extends('theme::layouts.right-sidebar')

@section('content')
    {{-- ═══ Breadcrumb ═══ --}}
    <nav class="breadcrumb-boja" aria-label="breadcrumb">
        <ol>
            <li><a href="{{ ci_route() }}"><i class="fas fa-home"></i> Beranda</a></li>
            <li>Arsip Artikel</li>
        </ol>
    </nav>

    {{-- ═══ Page Header ═══ --}}
    <div class="arsip-header">
        <div class="arsip-header-text">
            <h1 class="arsip-title"><i class="fas fa-archive"></i> Arsip Situs Web</h1>
            <p class="arsip-subtitle">Kumpulan seluruh artikel dan berita {{ ucwords(strtolower(setting('sebutan_desa') . ' ' . ($desa['nama_desa'] ?? ''))) }}</p>
        </div>
    </div>

    {{-- ═══ DataTables Arsip ═══ --}}
    <div class="arsip-table-wrap">
        <div class="arsip-table-inner">
            <table id="arsip-artikel" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th width="20%">Tanggal</th>
                        <th>Judul Artikel</th>
                        <th width="14%">Penulis</th>
                        <th width="10%">Dibaca</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    {{-- ═══ Divider ═══ --}}
    @php
        $allAgenda = array_merge($hari_ini ?? [], $yad ?? [], $lama ?? []);
        $hasAgenda = count($allAgenda) > 0;
        $hasKomen  = !empty($komen) && count($komen) > 0;
    @endphp

    @if ($hasAgenda || $hasKomen)
        <div class="arsip-widget-divider">
            <div class="arsip-widget-divider-line"></div>
            <div class="arsip-widget-divider-icon"><i class="fas fa-stream"></i></div>
            <div class="arsip-widget-divider-line" style="background:linear-gradient(270deg,var(--primary-light),transparent)"></div>
        </div>

        {{-- ═══ Agenda + Komentar 2-Grid ═══ --}}
        <div class="agenda-komentar-row{{ !$hasAgenda ? ' ak-row-single' : '' }}{{ !$hasKomen ? ' ak-row-single-agenda' : '' }}">

            {{-- ── Kolom Kiri: Agenda ── --}}
            @if ($hasAgenda)
                <div class="ak-agenda">
                    <div class="ak-card">
                        <div class="ak-card-header">
                            <div class="ak-card-header-icon"><i class="fas fa-calendar-alt"></i></div>
                            <div>
                                <h3 class="ak-card-header-title">Agenda Kegiatan</h3>
                                <p class="ak-card-header-sub">Kegiatan & acara {{ setting('sebutan_desa') }}</p>
                            </div>
                        </div>

                        <div class="ak-agenda-viewport" id="arsipAgendaViewport">
                            <div class="ak-agenda-track" id="arsipAgendaTrack">
                                @php $agendaIdx = 0; @endphp
                                @foreach (['hari_ini' => ['badge' => 'Hari Ini', 'class' => 'ak-badge-today'], 'yad' => ['badge' => 'Mendatang', 'class' => 'ak-badge-upcoming'], 'lama' => ['badge' => 'Selesai', 'class' => 'ak-badge-past']] as $varName => $meta)
                                    @foreach ($$varName ?? [] as $agenda)
                                        <a href="{{ site_url('artikel/' . buat_slug($agenda)) }}" class="ak-agenda-item{{ $agendaIdx >= 2 ? ' ak-agenda-hidden' : '' }}" data-agenda-index="{{ $agendaIdx }}">
                                            <div class="ak-agenda-date">
                                                @php $parts = explode(' ', tgl_indo2($agenda['tgl_agenda'] ?? '')); @endphp
                                                <span class="ak-agenda-date-num">{{ $parts[1] ?? '' }}</span>
                                                <span class="ak-agenda-date-mon">{{ $parts[2] ?? '' }}</span>
                                            </div>
                                            <div class="ak-agenda-info">
                                                <span class="ak-agenda-badge {{ $meta['class'] }}">{{ $meta['badge'] }}</span>
                                                <h4>{{ $agenda['judul'] }}</h4>
                                                @if (!empty($agenda['lokasi_kegiatan']))
                                                    <span class="ak-agenda-loc"><i class="fas fa-map-marker-alt"></i> {{ $agenda['lokasi_kegiatan'] }}</span>
                                                @endif
                                            </div>
                                            <span class="ak-agenda-arrow"><i class="fas fa-chevron-right"></i></span>
                                        </a>
                                        @php $agendaIdx++; @endphp
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                        @if (count($allAgenda) > 2)
                            <div class="ak-agenda-nav">
                                <span class="ak-agenda-info" id="arsipAgendaInfo">1 - 2 dari {{ count($allAgenda) }}</span>
                                <div class="ak-agenda-nav-btns">
                                    <button id="arsipAgendaPrev" class="ak-agenda-btn" disabled><i class="fas fa-chevron-up"></i></button>
                                    <button id="arsipAgendaNext" class="ak-agenda-btn"><i class="fas fa-chevron-down"></i></button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- ── Kolom Kanan: Komentar Terbaru ── --}}
            @if ($hasKomen)
                <div class="ak-komentar">
                    <div class="ak-card">
                        <div class="ak-card-header">
                            <div class="ak-card-header-icon"><i class="fas fa-comments"></i></div>
                            <div>
                                <h3 class="ak-card-header-title">Komentar Terbaru</h3>
                                <p class="ak-card-header-sub">Tanggapan warga terhadap artikel {{ setting('sebutan_desa') }}</p>
                            </div>
                        </div>
                        <div class="ak-komentar-viewport" id="arsipKomentarViewport">
                            <div class="ak-komentar-track" id="arsipKomentarTrack">
                                @foreach ($komen as $i => $data)
                                    <div class="ak-komentar-item{{ $i >= 2 ? ' ak-komentar-hidden' : '' }}" data-komentar-index="{{ $i }}">
                                        <div class="ak-komentar-avatar">
                                            <span>{{ strtoupper(substr($data['owner'] ?? 'A', 0, 1)) }}</span>
                                        </div>
                                        <div class="ak-komentar-body">
                                            <div class="ak-komentar-quote">
                                                <i class="fas fa-quote-left ak-komentar-quote-icon"></i>
                                                <p>{{ potong_teks($data['komentar'], 100) }}</p>
                                            </div>
                                            <div class="ak-komentar-meta">
                                                <span class="ak-komentar-author"><i class="fas fa-user"></i> {{ $data['owner'] }}</span>
                                                <span class="ak-komentar-date"><i class="fas fa-clock"></i> {{ tgl_indo2($data['tgl_upload']) }}</span>
                                                <a href="{{ site_url('artikel/' . buat_slug($data)) }}" class="ak-komentar-link">Lihat <i class="fas fa-arrow-right"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if (count($komen) > 2)
                            <div class="ak-komentar-nav">
                                <span class="ak-komentar-info" id="arsipKomentarInfo">1 - 2 dari {{ count($komen) }}</span>
                                <div class="ak-komentar-nav-btns">
                                    <button id="arsipKomentarPrev" class="ak-komentar-btn" disabled><i class="fas fa-chevron-up"></i></button>
                                    <button id="arsipKomentarNext" class="ak-komentar-btn"><i class="fas fa-chevron-down"></i></button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    @endif
@endsection

@push('scripts')
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            // ═══ DataTables ═══
            var arsip = $('#arsip-artikel').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ordering: true,
                language: {
                    search: '<i class="fas fa-search"></i>',
                    searchPlaceholder: 'Cari artikel...',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ - _END_ dari _TOTAL_ artikel',
                    infoEmpty: 'Tidak ada data',
                    infoFiltered: '(disaring dari _MAX_ total)',
                    zeroRecords: 'Tidak ditemukan artikel yang cocok',
                    paginate: { first: '«', previous: '‹', next: '›', last: '»' },
                    processing: '<div style="display:flex;align-items:center;gap:8px;justify-content:center"><i class="fas fa-spinner fa-spin" style="color:var(--primary)"></i> Memuat data...</div>'
                },
                ajax: {
                    url: `{{ ci_route('internal_api.arsip') }}`,
                    method: 'get',
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
                    { data: null, orderable: false, className: 'text-center', width: '5%' },
                    {
                        data: "attributes.tgl_upload_local",
                        name: "tgl_upload",
                        render: function(data) {
                            return '<span class="arsip-date"><i class="fas fa-calendar-day"></i> ' + data + '</span>';
                        }
                    },
                    {
                        data: function(data) {
                            return '<a href="' + data.attributes.url_slug + '">' + data.attributes.judul + '</a>';
                        },
                        name: "judul",
                        orderable: false
                    },
                    {
                        data: "attributes.author.nama",
                        name: "id_user",
                        defaultContent: '-',
                        searchable: false,
                        orderable: false,
                        render: function(data) {
                            if (!data) return '<span style="color:#9ca3af">-</span>';
                            return '<span class="arsip-author"><i class="fas fa-user-edit"></i> ' + data + '</span>';
                        }
                    },
                    {
                        data: "attributes.hit",
                        name: "hit",
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            return '<span class="arsip-read-badge"><i class="fas fa-eye"></i> ' + (data || 0) + '</span>';
                        }
                    },
                ],
                order: [[1, 'desc']]
            });

            arsip.on('draw.dt', function() {
                var PageInfo = $('#arsip-artikel').DataTable().page.info();
                arsip.column(0, { page: 'current' }).nodes().each(function(cell, i) {
                    cell.innerHTML = '<span style="font-weight:600;color:#6b7280">' + (i + 1 + PageInfo.start) + '</span>';
                });
            });

            // ═══ Agenda Slider ═══
            (function() {
                var $track = $('#arsipAgendaTrack');
                if (!$track.length) return;
                var $items = $track.find('.ak-agenda-item');
                var total = $items.length;
                if (total <= 2) return;
                var page = 0;
                var perPage = 2;
                var maxPage = Math.ceil(total / perPage) - 1;
                var $prev = $('#arsipAgendaPrev');
                var $next = $('#arsipAgendaNext');
                var $info = $('#arsipAgendaInfo');

                function render() {
                    var start = page * perPage;
                    var end = Math.min(start + perPage, total);
                    $items.addClass('ak-agenda-hidden');
                    $items.slice(start, end).removeClass('ak-agenda-hidden');
                    $info.text((start + 1) + ' - ' + end + ' dari ' + total);
                    $prev.prop('disabled', page === 0);
                    $next.prop('disabled', page >= maxPage);
                }
                $prev.on('click', function() { if (page > 0) { page--; render(); } });
                $next.on('click', function() { if (page < maxPage) { page++; render(); } });
            })();

            // ═══ Komentar Slider ═══
            (function() {
                var $track = $('#arsipKomentarTrack');
                if (!$track.length) return;
                var $items = $track.find('.ak-komentar-item');
                var total = $items.length;
                if (total <= 2) return;
                var page = 0;
                var perPage = 2;
                var maxPage = Math.ceil(total / perPage) - 1;
                var $prev = $('#arsipKomentarPrev');
                var $next = $('#arsipKomentarNext');
                var $info = $('#arsipKomentarInfo');

                function render() {
                    var start = page * perPage;
                    var end = Math.min(start + perPage, total);
                    $items.addClass('ak-komentar-hidden');
                    $items.slice(start, end).removeClass('ak-komentar-hidden');
                    $info.text((start + 1) + ' - ' + end + ' dari ' + total);
                    $prev.prop('disabled', page === 0);
                    $next.prop('disabled', page >= maxPage);
                }
                $prev.on('click', function() { if (page > 0) { page--; render(); } });
                $next.on('click', function() { if (page < maxPage) { page++; render(); } });
            })();
        });
    </script>
@endpush
