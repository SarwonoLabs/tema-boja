@extends('theme::template')
@php
    $title = !empty($judul_kategori) ? $judul_kategori : 'Artikel Terkini';
    $slug = 'terkini';
    if (is_array($title)) {
        $slug = $title['slug'] ?? '';
        $title = $title['kategori'] ?? $title;
    }
    // Deteksi kategori aktif dari URL: artikel/kategori/{slug}
    $segments = request()->segments();
    $kategoriIdx = array_search('kategori', $segments);
    $isKategoriPage = ($kategoriIdx !== false);
    $activeKategoriSlug = $isKategoriPage && isset($segments[$kategoriIdx + 1]) ? $segments[$kategoriIdx + 1] : '';
@endphp

@section('layout')
    {{-- ========================================
         Main Content Area (Menu Desa now in Hero, Info now in Header ticker)
         ======================================== --}}

    <div id="mainContent" class="container layout-boja">
        <div class="layout-main layout-sidebar-left">
            {{-- Content Area --}}
            <main class="content-area">
                {{-- Sekilas Data Desa — Compact Stats Banner --}}
                @if (empty($cari))
                    @php
                        $totalPendudukBanner = $stat_widget['total'] ?? null;
                    @endphp
                    @if ($totalPendudukBanner)
                    <div class="desa-stats-banner" data-animate="fadeInUp">
                        <div class="dsb-header">
                            <div class="dsb-header-left">
                                <span class="dsb-icon"><i class="fas fa-chart-line"></i></span>
                                <div>
                                    <h3>Data Kependudukan</h3>
                                    <p>Statistik penduduk {{ ucfirst(setting('sebutan_desa')) }} {{ $desa['nama_desa'] }}</p>
                                </div>
                            </div>
                            <a href="{{ site_url('data-statistik') }}" class="dsb-link">Selengkapnya <i class="fas fa-arrow-right"></i></a>
                        </div>
                        <div class="dsb-counters">
                            <div class="dsb-counter-card dsb-total">
                                <div class="dsb-counter-ring">
                                    <i class="fas fa-users"></i>
                                </div>
                                <span class="dsb-counter-num" data-target="{{ $totalPendudukBanner['jumlah'] }}">0</span>
                                <span class="dsb-counter-label">Jiwa</span>
                            </div>
                            <div class="dsb-counter-divider"></div>
                            <div class="dsb-counter-card dsb-laki">
                                <div class="dsb-counter-ring">
                                    <i class="fas fa-male"></i>
                                </div>
                                <span class="dsb-counter-num" data-target="{{ $totalPendudukBanner['laki'] }}">0</span>
                                <span class="dsb-counter-label">Laki-laki</span>
                            </div>
                            <div class="dsb-counter-divider"></div>
                            <div class="dsb-counter-card dsb-perempuan">
                                <div class="dsb-counter-ring">
                                    <i class="fas fa-female"></i>
                                </div>
                                <span class="dsb-counter-num" data-target="{{ $totalPendudukBanner['perempuan'] }}">0</span>
                                <span class="dsb-counter-label">Perempuan</span>
                            </div>
                        </div>
                        <div class="dsb-quicklinks">
                            <a href="{{ site_url('data-statistik/agama') }}" class="dsb-qlink">
                                <span class="dsb-qlink-dot" style="background:#D97706"></span>
                                Agama
                            </a>
                            <a href="{{ site_url('data-statistik/pekerjaan') }}" class="dsb-qlink">
                                <span class="dsb-qlink-dot" style="background:#0891B2"></span>
                                Pekerjaan
                            </a>
                            <a href="{{ site_url('data-statistik/pendidikan-dalam-kk') }}" class="dsb-qlink">
                                <span class="dsb-qlink-dot" style="background:#7C3AED"></span>
                                Pendidikan
                            </a>
                            <a href="{{ site_url('data-statistik/rentang-umur') }}" class="dsb-qlink">
                                <span class="dsb-qlink-dot" style="background:#EA580C"></span>
                                Umur
                            </a>
                            <a href="{{ site_url('data-statistik/status-perkawinan') }}" class="dsb-qlink">
                                <span class="dsb-qlink-dot" style="background:#DC2626"></span>
                                Perkawinan
                            </a>
                            <a href="{{ site_url('data-wilayah') }}" class="dsb-qlink">
                                <span class="dsb-qlink-dot" style="background:#0D9488"></span>
                                Wilayah
                            </a>
                        </div>
                    </div>
                    @endif
                @endif

                {{-- Section Title --}}
                <div class="section-header">
                    <h3 class="section-title"><i class="fas fa-newspaper"></i> {{ $title }}</h3>
                    <a href="{{ site_url('arsip') }}" class="btn btn-sm btn-outline">Arsip <i class="fas fa-arrow-right"></i></a>
                </div>

                {{-- Category Tabs --}}
                @if (!empty($menu_kiri) && count($menu_kiri) > 0)
                    <div class="category-tabs">
                        <a href="{{ site_url('/') }}" class="cat-tab{{ !$isKategoriPage ? ' active' : '' }}">Semua</a>
                        @foreach (array_slice($menu_kiri, 0, 5) as $cat)
                            <a href="{{ site_url('artikel/kategori/' . $cat['slug']) }}" class="cat-tab{{ $activeKategoriSlug == $cat['slug'] ? ' active' : '' }}">{{ $cat['kategori'] }}</a>
                        @endforeach
                    </div>
                @endif

                {{-- Article Grid --}}
                @if ($artikel->count() > 0)
                    <div class="article-grid">
                        @foreach ($artikel as $post)
                            @include('theme::partials.artikel.list', ['post' => $post])
                        @endforeach
                    </div>
                    <div class="pagination-boja">
                        @include('theme::commons.paging', ['paging' => $links])
                    </div>

                    {{-- Komentar Terbaru — Slider (tampil 3, next/prev) --}}
                    @if (!empty($komen) && count($komen) > 0)
                        <div class="komentar-inline-card" data-animate="fadeInUp">
                            <div class="komentar-inline-header">
                                <span class="komentar-inline-icon"><i class="fas fa-comments"></i></span>
                                <h4>Komentar Terbaru</h4>
                            </div>
                            <div class="komentar-inline-viewport" id="komentarViewport">
                                <div class="komentar-inline-track" id="komentarTrack">
                                    @foreach ($komen as $ki => $km)
                                        <a href="{{ site_url('artikel/' . buat_slug($km)) }}" class="komentar-inline-item{{ $ki >= 3 ? ' komentar-inline-hidden' : '' }}" data-komentar-index="{{ $ki }}">
                                            <div class="komentar-inline-avatar">
                                                <span>{{ strtoupper(substr($km['owner'] ?? 'A', 0, 1)) }}</span>
                                            </div>
                                            <div class="komentar-inline-body">
                                                <span class="komentar-inline-name">{{ $km['owner'] }}</span>
                                                <p class="komentar-inline-text">{{ potong_teks($km['komentar'], 80) }}</p>
                                                <span class="komentar-inline-date"><i class="fas fa-clock"></i> {{ tgl_indo2($km['tgl_upload']) }}</span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            @if (count($komen) > 3)
                                <div class="komentar-inline-nav">
                                    <span class="komentar-inline-info" id="komentarInfo">1 - 3 dari {{ count($komen) }}</span>
                                    <div class="komentar-inline-nav-btns">
                                        <button id="komentarPrev" class="komentar-inline-btn" disabled><i class="fas fa-chevron-up"></i></button>
                                        <button id="komentarNext" class="komentar-inline-btn"><i class="fas fa-chevron-down"></i></button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                @else
                    @include('theme::partials.artikel.empty', ['title' => $title])
                @endif

            </main>

            {{-- Sidebar — Sebelah Kiri --}}
            <aside class="sidebar-area">
                @include('theme::partials.sidebar')
            </aside>
        </div>
    </div>

    {{-- ========================================
         SECTION: Galeri Desa (Full-width carousel)
         ======================================== --}}
    @php
        $galeriWidget = null;
        if ($widgetAktif) {
            foreach ($widgetAktif as $w) {
                if ($w['isi'] === 'galeri') { $galeriWidget = $w; break; }
            }
        }
    @endphp
    @if ($galeriWidget && !empty($w_gal))
        @php
            $galeriValid = collect($w_gal)->filter(fn($d) => is_file(LOKASI_GALERI . 'sedang_' . $d['gambar']))->take(3);
        @endphp
        @if ($galeriValid->count() > 0)
            <section class="section-fullwidth section-galeri-desa">
                <div class="container">
                    <div class="section-header-center">
                        <span class="section-icon"><i class="fas fa-camera-retro"></i></span>
                        <h2 class="section-title-big">Galeri {{ ucfirst(setting('sebutan_desa')) }}</h2>
                        <p class="section-subtitle">Dokumentasi kegiatan dan aktivitas {{ strtolower(setting('sebutan_desa')) }}</p>
                    </div>

                    <div class="galeri-desa-wrap" data-animate="fadeInUp">
                        <div class="galeri-desa-grid">
                            @foreach ($galeriValid as $data)
                                <a href="{{ route('web.galeri.detail', $data['id']) }}" class="galeri-desa-slide">
                                    <img src="{{ AmbilGaleri($data['gambar'], 'sedang') }}" alt="Album : {{ $data['nama'] }}" loading="lazy">
                                    <div class="galeri-desa-overlay">
                                        <span class="galeri-desa-title"><i class="fas fa-images"></i> {{ $data['nama'] }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="galeri-desa-footer">
                            <span class="galeri-desa-count"><i class="fas fa-layer-group"></i> {{ $galeriValid->count() }} Album Dokumentasi</span>
                            <a href="{{ site_url('galeri') }}" class="galeri-desa-viewall">Lihat Semua Galeri <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @endif

    {{-- ========================================
         SECTION: Kehadiran + Peta Desa + Agenda
         Left: Kehadiran Perangkat | Right: Peta Wilayah + Lokasi Kantor + Agenda
         ======================================== --}}
    <section class="section-fullwidth section-info-desa">
        <div class="container">
            <div class="section-header-center">
                <span class="section-icon"><i class="fas fa-chart-bar"></i></span>
                <h2 class="section-title-big">Informasi {{ ucfirst(setting('sebutan_desa')) }}</h2>
                <p class="section-subtitle">Kehadiran perangkat dan peta wilayah {{ strtolower(setting('sebutan_desa')) }}</p>
            </div>

            <div class="info-desa-row">
                {{-- LEFT — Kehadiran Perangkat + Statistik Pengunjung --}}
                <div class="info-desa-left">
                    <div class="kehadiran-panel" data-animate="fadeInLeft">
                        {{-- Header --}}
                        <div class="kehadiran-panel-header">
                            <div class="kehadiran-panel-icon">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div>
                                <h3>Kehadiran Perangkat</h3>
                                <p>Status kehadiran hari ini</p>
                            </div>
                            @if (setting('tampilkan_kehadiran') != '0')
                                <a href="{{ ci_route('kehadiran') }}" class="kehadiran-panel-login" title="Login Kehadiran" target="_blank">
                                    <i class="fas fa-sign-in-alt"></i>
                                </a>
                            @endif
                        </div>

                        {{-- Body --}}
                        <div class="kehadiran-panel-body">
                            @php
                                $perangkat = $aparatur_desa['daftar_perangkat'] ?? [];
                            @endphp

                            @if (count($perangkat) > 0)
                                {{-- Summary counter --}}
                                @php
                                    $totalHadir = 0; $totalIzin = 0; $totalBelum = 0;
                                    foreach ($perangkat as $p) {
                                        if (!$tampilkan_status_kehadiran) continue;
                                        if ($p['kehadiran'] == 1 && $p['status_kehadiran'] == 'hadir') $totalHadir++;
                                        elseif ($p['kehadiran'] == 1 && $p['tanggal'] == date('Y-m-d') && $p['status_kehadiran'] != 'hadir') $totalIzin++;
                                        else $totalBelum++;
                                    }
                                @endphp
                                <div class="kehadiran-summary">
                                    <div class="kehadiran-summary-item summary-hadir">
                                        <span class="summary-num">{{ $totalHadir }}</span>
                                        <span class="summary-label">Hadir</span>
                                    </div>
                                    <div class="kehadiran-summary-item summary-izin">
                                        <span class="summary-num">{{ $totalIzin }}</span>
                                        <span class="summary-label">Izin</span>
                                    </div>
                                    <div class="kehadiran-summary-item summary-belum">
                                        <span class="summary-num">{{ $totalBelum }}</span>
                                        <span class="summary-label">Belum</span>
                                    </div>
                                </div>

                                {{-- List (scrollable, shows ~6 at a time) --}}
                                <div class="kehadiran-panel-list">
                                    @foreach ($perangkat as $i => $data)
                                        @php
                                            if (!$tampilkan_status_kehadiran) {
                                                $statusClass = 'libur'; $statusLabel = 'Libur'; $statusIcon = 'fas fa-moon';
                                            } elseif ($data['kehadiran'] == 1) {
                                                if ($data['status_kehadiran'] == 'hadir') {
                                                    $statusClass = 'hadir'; $statusLabel = 'Hadir'; $statusIcon = 'fas fa-check-circle';
                                                } elseif ($data['tanggal'] == date('Y-m-d') && $data['status_kehadiran'] != 'hadir') {
                                                    $statusClass = 'izin'; $statusLabel = ucwords($data['status_kehadiran']); $statusIcon = 'fas fa-info-circle';
                                                } else {
                                                    $statusClass = 'belum'; $statusLabel = 'Belum Rekam'; $statusIcon = 'fas fa-clock';
                                                }
                                            } else {
                                                $statusClass = 'belum'; $statusLabel = 'Belum Rekam'; $statusIcon = 'fas fa-clock';
                                            }
                                        @endphp
                                        <div class="kehadiran-panel-item status-row-{{ $statusClass }}" style="animation-delay:{{ $i * 0.06 }}s">
                                            <div class="kehadiran-avatar-wrap">
                                                <img src="{{ $data['foto'] }}" alt="{{ $data['nama'] }}" class="kehadiran-avatar" loading="lazy">
                                                <span class="kehadiran-dot status-{{ $statusClass }}"></span>
                                            </div>
                                            <div class="kehadiran-info">
                                                <span class="kehadiran-nama">{{ $data['nama'] }}</span>
                                                <span class="kehadiran-jabatan">{{ $data['jabatan'] }}</span>
                                            </div>
                                            <div class="kehadiran-status-col">
                                                <span class="kehadiran-badge badge-{{ $statusClass }}" title="{{ $statusLabel }}">
                                                    <i class="{{ $statusIcon }}"></i>
                                                    <span class="badge-text">{{ $statusLabel }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="kehadiran-empty">
                                    <i class="fas fa-user-clock"></i>
                                    <p>Belum ada data perangkat</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Jam Kerja (di bawah kehadiran) --}}
                    @if (!empty($jam_kerja))
                        <div class="jamkerja-panel" data-animate="fadeInLeft">
                            <div class="jamkerja-panel-header">
                                <div class="jamkerja-panel-icon">
                                    <i class="fas fa-business-time"></i>
                                </div>
                                <div>
                                    <h3>Jam Kerja</h3>
                                    <p>Jadwal layanan kantor {{ setting('sebutan_desa') }}</p>
                                </div>
                            </div>
                            <div class="jamkerja-panel-body">
                                <table class="jamkerja-table">
                                    <thead>
                                        <tr>
                                            <th>Hari</th>
                                            <th>Mulai</th>
                                            <th>Selesai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($jam_kerja as $jk)
                                            @php
                                                $isToday = (strtolower($jk->nama_hari) === strtolower(\Carbon\Carbon::now()->translatedFormat('l')));
                                            @endphp
                                            <tr class="{{ $isToday ? 'jamkerja-today' : '' }} {{ !$jk->status ? 'jamkerja-libur' : '' }}">
                                                <td>
                                                    @if ($isToday)<span class="jamkerja-today-dot"></span>@endif
                                                    {{ $jk->nama_hari }}
                                                </td>
                                                @if ($jk->status)
                                                    <td>{{ \Carbon\Carbon::parse($jk->jam_masuk)->format('H:i') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($jk->jam_keluar)->format('H:i') }}</td>
                                                @else
                                                    <td colspan="2"><span class="jamkerja-badge-libur">Libur</span></td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- RIGHT — Peta Wilayah + Lokasi Kantor --}}
                <div class="info-desa-right">
                    {{-- Peta Wilayah --}}
                    <div class="info-map-card" data-animate="fadeInRight">
                        <div class="info-map-card-header">
                            <div class="info-map-card-icon"><i class="fas fa-map-marked-alt"></i></div>
                            <div class="info-map-card-text">
                                <h3>Peta Wilayah</h3>
                                <p>Batas wilayah {{ strtolower(setting('sebutan_desa')) }} {{ $desa['nama_desa'] }}</p>
                            </div>
                            <a href="{{ site_url('data-wilayah') }}" class="info-map-card-link">
                                Data Wilayah <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div class="info-map-card-body">
                            @if (!empty($desa['lat']) && !empty($desa['lng']))
                                <div id="map_wilayah_trio" class="info-map-leaflet"></div>
                            @else
                                <div class="info-map-empty">
                                    <i class="fas fa-map"></i>
                                    <p>Data peta belum tersedia</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Lokasi Kantor --}}
                    <div class="info-map-card" data-animate="fadeInRight">
                        <div class="info-map-card-header">
                            <div class="info-map-card-icon"><i class="fas fa-building"></i></div>
                            <div class="info-map-card-text">
                                <h3>Lokasi Kantor</h3>
                                <p>Titik koordinat kantor {{ strtolower(setting('sebutan_desa')) }}</p>
                            </div>
                        </div>
                        <div class="info-map-card-body">
                            @if (!empty($desa['lat']) && !empty($desa['lng']))
                                <div id="map_kantor_trio" class="info-map-leaflet"></div>
                            @else
                                <div class="info-map-empty">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <p>Data lokasi belum tersedia</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Agenda Kegiatan (sejajar grid kiri) --}}
                    @php
                        $allAgenda = array_merge($hari_ini ?? [], $yad ?? [], $lama ?? []);
                        $hasAgenda = count($allAgenda) > 0;
                    @endphp
                    @if ($hasAgenda)
                        <div class="info-map-card info-agenda-card" data-animate="fadeInRight">
                            <div class="info-map-card-header">
                                <div class="info-map-card-icon"><i class="fas fa-calendar-alt"></i></div>
                                <div class="info-map-card-text">
                                    <h3>Agenda Kegiatan</h3>
                                    <p>Kegiatan & acara {{ strtolower(setting('sebutan_desa')) }}</p>
                                </div>
                            </div>
                            <div class="info-agenda-viewport" id="akAgendaViewport">
                                <div class="info-agenda-track" id="akAgendaTrack">
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
                                    <span class="ak-agenda-info" id="akAgendaInfo">1 - 2 dari {{ count($allAgenda) }}</span>
                                    <div class="ak-agenda-nav-btns">
                                        <button id="akAgendaPrev" class="ak-agenda-btn" disabled><i class="fas fa-chevron-up"></i></button>
                                        <button id="akAgendaNext" class="ak-agenda-btn"><i class="fas fa-chevron-down"></i></button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
    (function(){

        // ─── Komentar Vertical Slider (tampil 3, geser 1) ───
        (function(){
            var items = document.querySelectorAll('#komentarTrack .komentar-inline-item');
            var prevBtn = document.getElementById('komentarPrev');
            var nextBtn = document.getElementById('komentarNext');
            var infoEl = document.getElementById('komentarInfo');
            if (!items.length || items.length <= 3) return;

            var total = items.length;
            var perPage = 3;
            var current = 0;

            function render() {
                items.forEach(function(item, i) {
                    if (i >= current && i < current + perPage) {
                        item.classList.remove('komentar-inline-hidden');
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0)';
                    } else {
                        item.classList.add('komentar-inline-hidden');
                    }
                });
                if (prevBtn) prevBtn.disabled = (current === 0);
                if (nextBtn) nextBtn.disabled = (current + perPage >= total);
                if (infoEl) {
                    var s = current + 1;
                    var e = Math.min(current + perPage, total);
                    infoEl.textContent = s + ' - ' + e + ' dari ' + total;
                }
            }

            function slideToKomentar(newCurrent, direction) {
                var visible = [];
                items.forEach(function(item, i) {
                    if (i >= current && i < current + perPage) visible.push(item);
                });
                var outOffset = direction === 'next' ? '-16px' : '16px';
                visible.forEach(function(item) {
                    item.style.transition = 'all .3s ease';
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(' + outOffset + ')';
                });

                setTimeout(function() {
                    current = newCurrent;
                    items.forEach(function(item) {
                        item.classList.add('komentar-inline-hidden');
                        item.style.transition = 'none';
                        item.style.opacity = '0';
                        item.style.transform = 'translateY(' + (direction === 'next' ? '16px' : '-16px') + ')';
                    });
                    items.forEach(function(item, i) {
                        if (i >= current && i < current + perPage) {
                            item.classList.remove('komentar-inline-hidden');
                        }
                    });
                    void document.body.offsetHeight;
                    setTimeout(function() {
                        items.forEach(function(item, i) {
                            if (i >= current && i < current + perPage) {
                                item.style.transition = 'all .35s cubic-bezier(.4,0,.2,1)';
                                item.style.opacity = '1';
                                item.style.transform = 'translateY(0)';
                            }
                        });
                        if (prevBtn) prevBtn.disabled = (current === 0);
                        if (nextBtn) nextBtn.disabled = (current + perPage >= total);
                        if (infoEl) {
                            var s = current + 1, e = Math.min(current + perPage, total);
                            infoEl.textContent = s + ' - ' + e + ' dari ' + total;
                        }
                    }, 30);
                }, 280);
            }

            if (nextBtn) nextBtn.addEventListener('click', function() {
                if (current + perPage < total) slideToKomentar(current + 1, 'next');
            });
            if (prevBtn) prevBtn.addEventListener('click', function() {
                if (current > 0) slideToKomentar(current - 1, 'prev');
            });

            // Auto slide setiap 5 detik
            var autoTimer = setInterval(function() {
                if (current + perPage < total) {
                    slideToKomentar(current + 1, 'next');
                } else {
                    slideToKomentar(0, 'next');
                }
            }, 5000);

            var komentarCard = document.querySelector('.komentar-inline-card');
            if (komentarCard) {
                komentarCard.addEventListener('mouseenter', function() { clearInterval(autoTimer); });
                komentarCard.addEventListener('mouseleave', function() {
                    autoTimer = setInterval(function() {
                        if (current + perPage < total) slideToKomentar(current + 1, 'next');
                        else slideToKomentar(0, 'next');
                    }, 5000);
                });
            }

            render();
        })();

        // ─── Agenda Vertical Slider (tampil 2, geser 1 dari bawah ke atas) ───
        (function(){
            var items = document.querySelectorAll('#akAgendaTrack .ak-agenda-item');
            var prevBtn = document.getElementById('akAgendaPrev');
            var nextBtn = document.getElementById('akAgendaNext');
            var infoEl = document.getElementById('akAgendaInfo');
            if (!items.length || items.length <= 2) return;

            var total = items.length;
            var perPage = 2;
            var current = 0;

            function render() {
                items.forEach(function(item, i) {
                    if (i >= current && i < current + perPage) {
                        item.classList.remove('ak-agenda-hidden');
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0)';
                    } else {
                        item.classList.add('ak-agenda-hidden');
                    }
                });
                if (prevBtn) prevBtn.disabled = (current === 0);
                if (nextBtn) nextBtn.disabled = (current + perPage >= total);
                if (infoEl) {
                    var s = current + 1;
                    var e = Math.min(current + perPage, total);
                    infoEl.textContent = s + ' - ' + e + ' dari ' + total;
                }
            }

            function slideToAgenda(newCurrent, direction) {
                var visible = [];
                items.forEach(function(item, i) {
                    if (i >= current && i < current + perPage) visible.push(item);
                });
                var outOffset = direction === 'next' ? '-20px' : '20px';
                visible.forEach(function(item) {
                    item.style.transition = 'all .3s ease';
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(' + outOffset + ')';
                });

                setTimeout(function() {
                    current = newCurrent;
                    items.forEach(function(item) {
                        item.classList.add('ak-agenda-hidden');
                        item.style.transition = 'none';
                        item.style.opacity = '0';
                        item.style.transform = 'translateY(' + (direction === 'next' ? '20px' : '-20px') + ')';
                    });
                    items.forEach(function(item, i) {
                        if (i >= current && i < current + perPage) {
                            item.classList.remove('ak-agenda-hidden');
                        }
                    });
                    void document.body.offsetHeight;
                    setTimeout(function() {
                        items.forEach(function(item, i) {
                            if (i >= current && i < current + perPage) {
                                item.style.transition = 'all .35s cubic-bezier(.4,0,.2,1)';
                                item.style.opacity = '1';
                                item.style.transform = 'translateY(0)';
                            }
                        });
                        if (prevBtn) prevBtn.disabled = (current === 0);
                        if (nextBtn) nextBtn.disabled = (current + perPage >= total);
                        if (infoEl) {
                            var s = current + 1, e = Math.min(current + perPage, total);
                            infoEl.textContent = s + ' - ' + e + ' dari ' + total;
                        }
                    }, 30);
                }, 280);
            }

            if (nextBtn) nextBtn.addEventListener('click', function() {
                if (current + perPage < total) slideToAgenda(current + 1, 'next');
            });
            if (prevBtn) prevBtn.addEventListener('click', function() {
                if (current > 0) slideToAgenda(current - 1, 'prev');
            });

            // Auto slide setiap 5 detik
            var autoTimer = setInterval(function() {
                if (current + perPage < total) {
                    slideToAgenda(current + 1, 'next');
                } else {
                    slideToAgenda(0, 'next');
                }
            }, 5000);

            // Pause on hover
            var agendaCard = document.querySelector('.info-agenda-card');
            if (agendaCard) {
                agendaCard.addEventListener('mouseenter', function() { clearInterval(autoTimer); });
                agendaCard.addEventListener('mouseleave', function() {
                    autoTimer = setInterval(function() {
                        if (current + perPage < total) {
                            slideToAgenda(current + 1, 'next');
                        } else {
                            slideToAgenda(0, 'next');
                        }
                    }, 5000);
                });
            }

            render();
        })();

        // ─── Peta Wilayah (polygon) + Peta Kantor ───
        @if (!empty($desa['lat']) && !empty($desa['lng']))
        (function(){
            var posisi = [{{ $desa['lat'] }}, {{ $desa['lng'] }}];
            var zoom = {{ $desa['zoom'] ?: 10 }};
            var options = { maxZoom: {{ setting('max_zoom_peta') }}, minZoom: {{ setting('min_zoom_peta') }}, zoomControl: false };

            // Style polygon wilayah (sama seperti esensi_premium)
            var style_polygon = {
                stroke: true,
                color: '#FF0000',
                opacity: 1,
                weight: 2,
                fillColor: '#8888dd',
                fillOpacity: 0.5
            };

            // ─── Peta Wilayah ───
            var mapWilayah = L.map('map_wilayah_trio', options).setView(posisi, zoom);
            getBaseLayers(mapWilayah, "{{ setting('mapbox_key') }}", "{{ setting('jenis_peta') }}");
            L.control.zoom({ position: 'bottomright' }).addTo(mapWilayah);
            @if (!empty($desa['path']))
                var polygon_desa = {!! $desa['path'] !!};
                if (polygon_desa) {
                    var kantor_desa = L.polygon(polygon_desa, style_polygon)
                        .bindTooltip("Wilayah {{ ucwords(setting('sebutan_desa')) }}")
                        .addTo(mapWilayah);
                    mapWilayah.fitBounds(kantor_desa.getBounds());
                }
            @endif

            // ─── Peta Kantor (marker + popup) ───
            var mapKantor = L.map('map_kantor_trio', options).setView(posisi, zoom + 3);
            getBaseLayers(mapKantor, "{{ setting('mapbox_key') }}", "{{ setting('jenis_peta') }}");
            L.control.zoom({ position: 'bottomright' }).addTo(mapKantor);
            L.marker(posisi).addTo(mapKantor)
                .bindPopup('<strong>Kantor {{ $desa['nama_desa'] }}</strong><br>{{ $desa['alamat_kantor'] }}')
                .openPopup();
        })();
        @endif

        // ─── Animate on Scroll: Info Desa Section ───
        (function(){
            var observer = new IntersectionObserver(function(entries){
                entries.forEach(function(entry){
                    if(entry.isIntersecting){
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {threshold:0.15});

            document.querySelectorAll('[data-animate]').forEach(function(el){
                observer.observe(el);
            });

            // Animate kehadiran items on scroll
            var itemObserver = new IntersectionObserver(function(entries){
                entries.forEach(function(entry){
                    if(entry.isIntersecting){
                        entry.target.classList.add('item-visible');
                        itemObserver.unobserve(entry.target);
                    }
                });
            }, {threshold:0.05});

            document.querySelectorAll('.kehadiran-panel-item').forEach(function(item){
                itemObserver.observe(item);
            });

            // ─── Animate Pengunjung Counter (count up) ───
            var pengunjungObserver = new IntersectionObserver(function(entries){
                entries.forEach(function(entry){
                    if(entry.isIntersecting){
                        entry.target.querySelectorAll('.pengunjung-stat-num').forEach(function(el){
                            var target = parseInt(el.getAttribute('data-target')) || 0;
                            var duration = 1500;
                            var startTime = null;
                            function step(timestamp){
                                if(!startTime) startTime = timestamp;
                                var progress = Math.min((timestamp - startTime) / duration, 1);
                                var eased = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                                var current = Math.floor(eased * target);
                                el.textContent = current.toLocaleString('id-ID');
                                if(progress < 1) requestAnimationFrame(step);
                                else el.textContent = target.toLocaleString('id-ID');
                            }
                            requestAnimationFrame(step);
                        });
                        entry.target.classList.add('pengunjung-animated');
                        pengunjungObserver.unobserve(entry.target);
                    }
                });
            }, {threshold:0.3});

            var pengunjungEl = document.querySelector('.pengunjung-card');
            if(pengunjungEl) pengunjungObserver.observe(pengunjungEl);

            // ─── Animate DSB Counter (Sekilas Data Desa banner) ───
            var dsbObserver = new IntersectionObserver(function(entries){
                entries.forEach(function(entry){
                    if(entry.isIntersecting){
                        entry.target.querySelectorAll('.dsb-counter-num').forEach(function(el){
                            var target = parseInt(el.getAttribute('data-target')) || 0;
                            var duration = 1800;
                            var startTime = null;
                            function step(timestamp){
                                if(!startTime) startTime = timestamp;
                                var progress = Math.min((timestamp - startTime) / duration, 1);
                                var eased = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                                var current = Math.floor(eased * target);
                                el.textContent = current.toLocaleString('id-ID');
                                if(progress < 1) requestAnimationFrame(step);
                                else el.textContent = target.toLocaleString('id-ID');
                            }
                            requestAnimationFrame(step);
                        });
                        dsbObserver.unobserve(entry.target);
                    }
                });
            }, {threshold:0.2});

            var dsbEl = document.querySelector('.dsb-counters');
            if(dsbEl) dsbObserver.observe(dsbEl);
        })();
    })();
    </script>
    @endpush
@endsection