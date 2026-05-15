@extends('theme::layouts.full-content')

@section('content')
    <nav class="breadcrumb-boja" aria-label="breadcrumb">
        <ol>
            <li><a href="{{ ci_route() }}"><i class="fas fa-home"></i> Beranda</a></li>
            <li>{{ ucwords(setting('sebutan_pemerintah_desa')) }}</li>
        </ol>
    </nav>

    <div class="boja-page-header">
        <div class="boja-page-header-text">
            <h1 class="boja-page-title"><i class="fas fa-landmark"></i> {{ ucwords(setting('sebutan_pemerintah_desa')) }}</h1>
            <p class="boja-page-subtitle">Daftar aparatur {{ strtolower(setting('sebutan_pemerintah_desa')) }} yang bertugas melayani masyarakat</p>
        </div>
    </div>

    <div id="pem-loading" style="text-align:center;padding:40px">
        <i class="fas fa-spinner fa-spin" style="color:var(--primary);font-size:1.4rem"></i>
        <p style="margin-top:8px;color:#6b7280;font-size:.88rem">Memuat data aparatur...</p>
    </div>

    <div class="pem-grid" id="pemerintah-list" style="display:none"></div>

    @include('theme::commons.pagination')
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            var mediaSosialPlatforms = [];
            try { mediaSosialPlatforms = JSON.parse(setting.media_sosial_pemerintah_desa) || []; } catch(e) {}

            function loadPemerintah(params) {
                params = params || {};
                var apiPemerintah = '{{ route('api.pemerintah') }}';
                var $list = $('#pemerintah-list');
                var $loading = $('#pem-loading');
                $loading.show();
                $list.hide();

                $.get(apiPemerintah, params, function(data) {
                    var pemerintah = data.data;
                    $list.empty();
                    $loading.hide();
                    $list.show();

                    if (!pemerintah.length) {
                        $list.html('<div class="pem-empty"><i class="fas fa-users-slash"></i><p>Data ' + setting.sebutan_pemerintah_desa + ' belum tersedia.</p></div>');
                        return;
                    }

                    pemerintah.forEach(function(item) {
                        var a = item.attributes;

                        // Social media icons
                        var socials = '';
                        var mediaSosialData = a.media_sosial || {};
                        mediaSosialPlatforms.forEach(function(platform) {
                            var link = mediaSosialData[platform];
                            if (link) {
                                socials += '<a href="' + link + '" target="_blank" class="pem-social-btn" title="' + platform + '"><i class="fab fa-' + platform + '"></i></a>';
                            }
                        });

                        // Attendance badge
                        var badge = '';
                        if (a.kehadiran == 1 || a.hari_libur == false) {
                            if (a.status_kehadiran === 'hadir') {
                                badge = '<span class="pem-badge pem-badge-hadir"><i class="fas fa-check-circle"></i> Hadir</span>';
                            } else if (a.status_kehadiran) {
                                badge = '<span class="pem-badge pem-badge-absent"><i class="fas fa-times-circle"></i> ' + a.status_kehadiran + '</span>';
                            }
                        }

                        var nip = '';
                        if (a.pamong_niap) {
                            nip = '<span class="pem-nip"><i class="fas fa-id-card"></i> ' + a.sebutan_nip_desa + ': ' + a.pamong_niap + '</span>';
                        }

                        var html = '<div class="pem-card">' +
                            '<div class="pem-card-photo-wrap">' +
                                '<img loading="lazy" src="' + (a.foto || '') + '" alt="' + a.nama + '" onerror="this.src=\'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect fill=%22%23e5e7eb%22 width=%22100%22 height=%22100%22/><text x=%2250%22 y=%2256%22 text-anchor=%22middle%22 fill=%22%239ca3af%22 font-size=%2232%22>👤</text></svg>\'">' +
                                badge +
                            '</div>' +
                            '<div class="pem-card-body">' +
                                '<h3 class="pem-name">' + a.nama + '</h3>' +
                                '<span class="pem-jabatan">' + a.nama_jabatan + '</span>' +
                                nip +
                                (socials ? '<div class="pem-socials">' + socials + '</div>' : '') +
                            '</div>' +
                        '</div>';

                        $list.append(html);
                    });

                    initPagination(data);
                });
            }

            $('.pagination').on('click', '.btn-page', function() {
                loadPemerintah({'page[number]': $(this).data('page')});
            });

            loadPemerintah();
        });
    </script>
@endpush
