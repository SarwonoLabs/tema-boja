@extends('theme::layouts.right-sidebar')
@include('theme::commons.asset_peta')

@section('content')
    <nav class="breadcrumb-boja" aria-label="breadcrumb">
        <ol>
            <li><a href="{{ site_url('/') }}"><i class="fas fa-home"></i> Beranda</a></li>
            <li><a href="{{ site_url('pembangunan') }}">Pembangunan</a></li>
            <li class="judul-pembangunan"></li>
        </ol>
    </nav>

    <h1 class="section-title judul-pembangunan"></h1>
    <div class="pembangunan-detail-wrap" id="detail-pembangunan"></div>
@endsection

@push('scripts')
    <script type="text/javascript">
        var noImage = '{{ theme_asset('images/no-image-article.svg') }}';
        var notFoundW = 320, notFoundH = 308;

        function checkNotFound(img) {
            if (img.naturalWidth === notFoundW && img.naturalHeight === notFoundH) {
                img.onload = null;
                img.src = noImage;
            }
        }

        $(document).ready(function() {
            var slug = '{{ $slug }}';

            function fotoSrc(src) {
                if (!src) return noImage;
                try {
                    var urlObj = new URL(src, window.location.origin);
                    var pathParam = urlObj.searchParams.get('path') || '';
                    if (pathParam.endsWith('/') || !/\.\w{2,5}$/.test(pathParam)) return noImage;
                } catch(e) {}
                return src;
            }

            function getMaxPersentase(dok) {
                if (!dok || !dok.length) return null;
                var max = 0;
                dok.forEach(function(d) {
                    var val = parseInt(String(d.persentase).replace('%', ''), 10);
                    if (!isNaN(val) && val > max) max = val;
                });
                return max;
            }

            function progressClass(val) {
                if (val >= 75) return 'progress-high';
                if (val >= 40) return 'progress-mid';
                if (val > 0)  return 'progress-low';
                return 'progress-none';
            }

            function loadPembangunan() {
                var api = '{{ route('api.pembangunan') }}';
                var el = $('#detail-pembangunan');
                el.html('<div style="text-align:center;padding:3rem;grid-column:1/-1;"><i class="fas fa-spinner fa-spin" style="font-size:1.5rem;color:var(--primary);"></i><p style="margin-top:.75rem;color:var(--text-muted);font-size:.9rem;">Memuat detail pembangunan...</p></div>');

                $.get(api, {'filter[slug]': slug}, function(response) {
                    el.empty();
                    if (response.data.length !== 1) {
                        el.html('<div class="empty-state-boja" style="grid-column:1/-1;"><i class="fas fa-hard-hat"></i><p>Data pembangunan tidak ditemukan.</p></div>');
                        return;
                    }

                    var p = response.data[0].attributes;
                    var dok = p.pembangunan_dokumentasi || [];
                    var persen = getMaxPersentase(dok);
                    var displayPersen = persen !== null ? persen : 0;
                    var lokasi = p.lokasi || p.alamat || '-';

                    $('.judul-pembangunan').text(p.judul);

                    // ===== Left: Main Info =====
                    var html = '<div class="pembangunan-detail-main">' +
                        '<div class="pembangunan-detail-hero">' +
                            '<img src="' + fotoSrc(p.foto) + '" alt="' + p.judul + '" onload="checkNotFound(this)" onerror="this.onerror=null;this.src=\'' + noImage + '\'">' +
                            '<div class="pembangunan-detail-hero-overlay">' +
                                '<h2>' + p.judul + '</h2>' +
                                '<div class="meta"><i class="fas fa-calendar-alt"></i> ' + p.tahun_anggaran + ' &nbsp;&middot;&nbsp; <i class="fas fa-map-marker-alt"></i> ' + lokasi + '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="pembangunan-detail-info">' +
                            '<div class="pembangunan-progress" style="margin-bottom:24px;">' +
                                '<div class="pembangunan-progress-header">' +
                                    '<span class="pembangunan-progress-label">Progress Pembangunan</span>' +
                                    '<span class="pembangunan-progress-value">' + displayPersen + '%</span>' +
                                '</div>' +
                                '<div class="pembangunan-progress-bar">' +
                                    '<div class="pembangunan-progress-fill ' + progressClass(displayPersen) + '" style="width:' + displayPersen + '%;"></div>' +
                                '</div>' +
                            '</div>' +
                            '<table class="pembangunan-detail-table">' +
                                '<tr><th><i class="fas fa-file-alt" style="color:var(--primary);margin-right:6px;"></i>Nama Kegiatan</th><td>' + p.judul + '</td></tr>' +
                                '<tr><th><i class="fas fa-map-marker-alt" style="color:var(--primary);margin-right:6px;"></i>Alamat</th><td>' + (p.alamat || '-') + '</td></tr>' +
                                '<tr><th><i class="fas fa-wallet" style="color:var(--primary);margin-right:6px;"></i>Sumber Dana</th><td>' + (Array.isArray(p.sumber_dana) ? p.sumber_dana.join(', ') : (p.sumber_dana || '-')) + '</td></tr>' +
                                '<tr><th><i class="fas fa-money-bill-wave" style="color:var(--primary);margin-right:6px;"></i>Anggaran</th><td>' + formatRupiah(p.anggaran) + '</td></tr>' +
                                '<tr><th><i class="fas fa-cube" style="color:var(--primary);margin-right:6px;"></i>Volume</th><td>' + (p.volume || '-') + '</td></tr>' +
                                '<tr><th><i class="fas fa-user-hard-hat" style="color:var(--primary);margin-right:6px;"></i>Pelaksana</th><td>' + (p.pelaksana_kegiatan || '-') + '</td></tr>' +
                                '<tr><th><i class="fas fa-calendar" style="color:var(--primary);margin-right:6px;"></i>Tahun</th><td>' + p.tahun_anggaran + '</td></tr>' +
                                '<tr><th><i class="fas fa-info-circle" style="color:var(--primary);margin-right:6px;"></i>Keterangan</th><td>' + (p.keterangan || '-') + '</td></tr>' +
                            '</table>' +
                        '</div>' +
                    '</div>';

                    // ===== Right: Sidebar =====
                    html += '<div class="pembangunan-detail-sidebar">';

                    // --- Dokumentasi ---
                    html += '<div class="pembangunan-detail-box">' +
                        '<div class="pembangunan-detail-box-header"><i class="fas fa-images"></i><h3>Dokumentasi Pembangunan</h3></div>' +
                        '<div class="pembangunan-detail-box-body">';

                    if (dok.length > 0) {
                        dok.forEach(function(d) {
                            var dPersen = String(d.persentase || '').replace('%', '');
                            html += '<div class="pembangunan-dok-item">' +
                                '<img src="' + fotoSrc(d.gambar) + '" alt="Dokumentasi ' + dPersen + '%" onload="checkNotFound(this)" onerror="this.onerror=null;this.src=\'' + noImage + '\'" loading="lazy">' +
                                '<div class="dok-label"><i class="fas fa-chart-line"></i> Progress ' + dPersen + '%</div>' +
                            '</div>';
                        });
                    } else {
                        html += '<div class="pembangunan-empty-dok"><i class="far fa-image" style="font-size:2rem;display:block;margin-bottom:8px;"></i>Belum ada dokumentasi.</div>';
                    }

                    html += '</div></div>';

                    // --- Peta ---
                    html += '<div class="pembangunan-detail-box">' +
                        '<div class="pembangunan-detail-box-header"><i class="fas fa-map-marked-alt"></i><h3>Lokasi Pembangunan</h3></div>' +
                        '<div id="map-pembangunan" class="pembangunan-map-container"></div>' +
                    '</div>';

                    html += '</div>';

                    el.html(html);
                    loadMap(p);
                });
            }

            function loadMap(p) {
                if (p.lat && p.lng) {
                    var posisi = [p.lat, p.lng];
                    var zoom = setting.default_zoom || 15;
                    var logo = L.icon({iconUrl: setting.icon_pembangunan_peta, iconSize: [30, 40], iconAnchor: [15, 40]});
                    var map = L.map('map-pembangunan', {maxZoom: setting.max_zoom_peta || 18, minZoom: setting.min_zoom_peta || 5}).setView(posisi, zoom);
                    getBaseLayers(map, setting.mapbox_key, setting.jenis_peta);
                    L.marker(posisi, {icon: logo}).addTo(map);
                } else {
                    $('#map-pembangunan').html('<div class="pembangunan-empty-dok"><i class="fas fa-map-marker-alt" style="font-size:2rem;display:block;margin-bottom:8px;"></i>Koordinat lokasi belum tersedia.</div>');
                }
            }

            loadPembangunan();
        });
    </script>
@endpush
