@extends('theme::layouts.right-sidebar')
@include('theme::commons.asset_peta')

@section('content')
    <nav class="breadcrumb-boja" aria-label="breadcrumb">
        <ol>
            <li><a href="{{ site_url('/') }}"><i class="fas fa-home"></i> Beranda</a></li>
            <li>Pembangunan</li>
        </ol>
    </nav>

    <h1 class="section-title"><i class="fas fa-hard-hat"></i> Pembangunan</h1>

    <div class="pembangunan-grid" id="pembangunan-list"></div>
    @include('theme::commons.pagination')

    <div class="modal" id="modalLokasi" style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5>Lokasi Pembangunan</h5></div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
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

            function statusInfo(val) {
                if (val === null) return { text: 'Rencana', cls: 'status-rencana', icon: 'far fa-clipboard' };
                if (val >= 100)   return { text: 'Selesai', cls: 'status-selesai', icon: 'fas fa-check-circle' };
                return { text: 'Proses', cls: 'status-proses', icon: 'fas fa-spinner' };
            }

            function loadPembangunan(params) {
                params = params || {};
                var pageSize = {{ theme_config('jumlah_pembangunan_perhalaman') }};
                var api = '{{ route('api.pembangunan') }}?page[size]=' + pageSize;
                var el = $('#pembangunan-list');
                el.html('<div style="text-align:center;padding:3rem;"><i class="fas fa-spinner fa-spin" style="font-size:1.5rem;color:var(--primary);"></i><p style="margin-top:.75rem;color:var(--text-muted);font-size:.9rem;">Memuat data pembangunan...</p></div>');

                $.get(api, params, function(data) {
                    el.empty();
                    if (!data.data.length) {
                        el.html('<div class="empty-state-boja"><i class="fas fa-hard-hat"></i><p>Belum ada data pembangunan.</p></div>');
                        return;
                    }

                    data.data.forEach(function(item) {
                        var a = item.attributes;
                        var url = SITE_URL + 'pembangunan/' + a.slug;
                        var foto = fotoSrc(a.foto);
                        var dok = a.pembangunan_dokumentasi || [];
                        var persen = getMaxPersentase(dok);
                        var status = statusInfo(persen);
                        var displayPersen = persen !== null ? persen : 0;
                        var keterangan = a.keterangan || '-';
                        var lokasi = a.lokasi || a.alamat || '-';

                        var html = '<div class="pembangunan-card">' +
                            '<div class="pembangunan-card-img">' +
                                '<img src="' + foto + '" alt="' + a.judul + '" onload="checkNotFound(this)" onerror="this.onerror=null;this.src=\'' + noImage + '\'" loading="lazy">' +
                                '<span class="pembangunan-card-badge"><i class="fas fa-calendar-alt"></i> ' + a.tahun_anggaran + '</span>' +
                            '</div>' +
                            '<div class="pembangunan-card-body">' +
                                '<h4 class="pembangunan-card-title">' + a.judul + '</h4>' +
                                '<div class="pembangunan-card-meta">' +
                                    '<span><i class="fas fa-map-marker-alt"></i> ' + lokasi + '</span>' +
                                    '<span class="pembangunan-card-status ' + status.cls + '"><i class="' + status.icon + '"></i> ' + status.text + '</span>' +
                                '</div>' +
                                '<p class="pembangunan-card-desc">' + (keterangan.length > 120 ? keterangan.substring(0, 120) + '...' : keterangan) + '</p>' +
                                '<div class="pembangunan-progress">' +
                                    '<div class="pembangunan-progress-header">' +
                                        '<span class="pembangunan-progress-label">Progress</span>' +
                                        '<span class="pembangunan-progress-value">' + displayPersen + '%</span>' +
                                    '</div>' +
                                    '<div class="pembangunan-progress-bar">' +
                                        '<div class="pembangunan-progress-fill ' + progressClass(displayPersen) + '" style="width:' + displayPersen + '%;"></div>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="pembangunan-card-footer">' +
                                    '<a href="' + url + '" class="btn-detail"><i class="fas fa-eye"></i> Lihat Detail</a>' +
                                    (a.lat && a.lng ? '<button class="btn-lokasi" data-lat="' + a.lat + '" data-lng="' + a.lng + '" data-title="' + a.judul + '"><i class="fas fa-map-marked-alt"></i> Peta</button>' : '') +
                                '</div>' +
                            '</div>' +
                        '</div>';
                        el.append(html);
                    });

                    initPagination(data);
                });
            }

            $('.pagination').on('click', '.btn-page', function() {
                loadPembangunan({'page[number]': $(this).data('page')});
            });

            loadPembangunan();
        });
    </script>
@endpush
