@extends('theme::layouts.right-sidebar')

@section('content')
    <nav class="breadcrumb-boja" aria-label="breadcrumb">
        <ol>
            <li><a href="{{ ci_route() }}"><i class="fas fa-home"></i> Beranda</a></li>
            @if (isset($parent))
                <li><a href="{{ ci_route('galeri') }}">Galeri</a></li>
                <li>{{ $title }}</li>
            @else
                <li>Galeri</li>
            @endif
        </ol>
    </nav>

    <div class="galeri-header">
        <div class="galeri-header-icon"><i class="fas fa-images"></i></div>
        <div>
            <h1 class="galeri-title">@if (isset($parent)) Album Galeri @else Album @endif {{ $title }}</h1>
            <p class="galeri-subtitle" id="galeri-count"></p>
        </div>
    </div>

    <div class="galeri-grid" id="galeri-list">
        <div class="galeri-loading">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Memuat galeri...</p>
        </div>
    </div>
    @include('theme::commons.pagination')
@endsection

@push('scripts')
    <script src="{{ theme_asset('js/pagination.js') }}"></script>
    <script type="text/javascript">
        var noGaleri = '{{ theme_asset('images/no-galery.png') }}';
        var notFoundW = 320, notFoundH = 308;

        function checkGaleriImg(img) {
            if (img.naturalWidth === notFoundW && img.naturalHeight === notFoundH) {
                img.onload = null;
                img.src = noGaleri;
            }
        }

        $(document).ready(function() {
            var parent = `{{ $parent }}`;
            var routeGaleri = `{{ ci_route('internal_api.galeri') }}`;
            var isChild = @json(isset($parent));
            var pageSizes = parent ? 12 : 6;

            if (parent) {
                routeGaleri += '/' + parent;
            }

            var loadGaleri = function(pageNumber) {
                $.ajax({
                    url: routeGaleri + '?sort=-tgl_upload&page[number]=' + pageNumber + '&page[size]=' + pageSizes,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {
                        displayGaleri(data);
                        initPagination(data);
                    }
                });
            };

            var displayGaleri = function(dataGaleri) {
                var el = document.getElementById('galeri-list');
                el.innerHTML = '';

                var total = dataGaleri.meta ? dataGaleri.meta.total : dataGaleri.data.length;
                var countEl = document.getElementById('galeri-count');
                if (countEl) {
                    countEl.textContent = total > 0 ? 'Menampilkan ' + total + ' album foto' : '';
                }

                if (!dataGaleri.data.length) {
                    el.innerHTML = '<div class="galeri-empty">' +
                        '<img src="' + noGaleri + '" alt="Belum ada galeri">' +
                        '<h3>Belum Ada Album Galeri</h3>' +
                        '<p>Album galeri foto akan ditampilkan di sini setelah ditambahkan oleh administrator desa.</p>' +
                    '</div>';
                    return;
                }

                dataGaleri.data.forEach(function(item) {
                    var a = item.attributes;
                    var hasImg = a.src_gambar && a.src_gambar.length > 0;
                    var imgSrc = hasImg ? a.src_gambar : noGaleri;
                    var card = document.createElement('div');
                    card.className = 'galeri-card';

                    if (isChild) {
                        // Child: FancyBox lightbox
                        card.innerHTML = '<a data-fancybox="gallery" data-src="' + (hasImg ? a.src_gambar : '') + '" data-caption="' + a.nama + '" class="galeri-card-link">' +
                            '<div class="galeri-card-img">' +
                                '<img src="' + imgSrc + '" alt="' + a.nama + '" onload="checkGaleriImg(this)" onerror="this.onerror=null;this.src=\'' + noGaleri + '\'" loading="lazy">' +
                                '<div class="galeri-card-overlay">' +
                                    '<i class="fas fa-search-plus"></i>' +
                                '</div>' +
                            '</div>' +
                            '<div class="galeri-card-info">' +
                                '<h4>' + a.nama + '</h4>' +
                            '</div>' +
                        '</a>';
                    } else {
                        // Parent: link to album detail
                        card.innerHTML = '<a href="' + a.url_detail + '" class="galeri-card-link">' +
                            '<div class="galeri-card-img">' +
                                '<img src="' + imgSrc + '" alt="' + a.nama + '" onload="checkGaleriImg(this)" onerror="this.onerror=null;this.src=\'' + noGaleri + '\'" loading="lazy">' +
                                '<div class="galeri-card-overlay">' +
                                    '<i class="fas fa-folder-open"></i>' +
                                    '<span>Lihat Album</span>' +
                                '</div>' +
                            '</div>' +
                            '<div class="galeri-card-info">' +
                                '<h4>' + a.nama + '</h4>' +
                            '</div>' +
                        '</a>';
                    }

                    el.appendChild(card);
                });
            };

            $('.pagination').on('click', '.btn-page', function() {
                loadGaleri($(this).data('page'));
            });
            loadGaleri(1);
        });
    </script>
@endpush
