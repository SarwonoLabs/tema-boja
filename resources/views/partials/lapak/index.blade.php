@extends('theme::layouts.right-sidebar')
@include('theme::commons.asset_peta')

@section('content')
    <nav class="breadcrumb-boja" aria-label="breadcrumb">
        <ol>
            <li><a href="{{ site_url() }}"><i class="fas fa-home"></i> Beranda</a></li>
            <li>Lapak</li>
        </ol>
    </nav>

    <div class="lapak-header">
        <div class="lapak-header-icon"><i class="fas fa-store"></i></div>
        <div>
            <h1 class="lapak-title">Lapak {{ ucwords(setting('sebutan_desa')) }}</h1>
            <p class="lapak-subtitle" id="lapak-count"></p>
        </div>
    </div>

    {{-- Filter / Search --}}
    <div class="lapak-filter">
        <div class="lapak-filter-inner">
            <div class="lapak-filter-field">
                <i class="fas fa-tags"></i>
                <select class="form-control select2" id="id_kategori" name="id_kategori">
                    <option selected value="">Semua Kategori</option>
                </select>
            </div>
            <div class="lapak-filter-field lapak-filter-search">
                <i class="fas fa-search"></i>
                <input type="text" id="search" name="search" maxlength="50" class="form-control" placeholder="Cari produk...">
            </div>
            <button type="button" id="btn-cari" class="lapak-btn-cari"><i class="fas fa-search"></i> Cari</button>
            <button type="button" id="btn-semua" class="lapak-btn-reset" style="display:none;"><i class="fas fa-redo"></i> Semua</button>
        </div>
    </div>

    {{-- Product Grid --}}
    <div class="lapak-grid" id="produk-list">
        <div class="lapak-loading">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Memuat produk...</p>
        </div>
    </div>
    @include('theme::commons.pagination')

    {{-- Modal Lokasi --}}
    <div class="lapak-modal-overlay" id="modalLokasi">
        <div class="lapak-modal">
            <div class="lapak-modal-header">
                <h5 class="lapak-modal-title">Lokasi Penjual</h5>
                <button type="button" class="lapak-modal-close" id="closeModalLokasi">&times;</button>
            </div>
            <div class="lapak-modal-body"></div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('theme::commons.asset_select2')
    <script src="{{ theme_asset('js/pagination.js') }}"></script>
    <script type="text/javascript">
        var noProduct = '{{ theme_asset('images/no-galery.png') }}';
        var notFoundW = 320, notFoundH = 308;

        function checkProdukImg(img) {
            if (img.naturalWidth === notFoundW && img.naturalHeight === notFoundH) {
                img.onload = null;
                img.src = noProduct;
            }
        }

        $(document).ready(function() {
            var routeProduk = '{{ route('api.lapak.produk') }}';
            var routeKategori = '{{ route('api.lapak.kategori') }}';

            // Load kategori
            $.get(routeKategori, function(data) {
                data.data.forEach(function(item) {
                    $('#id_kategori').append('<option value="' + item.id + '">' + item.attributes.kategori + '</option>');
                });
                if ($.fn.select2) {
                    $('.select2').select2({ width: '100%', placeholder: 'Semua Kategori' });
                }
            });

            function loadProduk(params) {
                params = params || {};
                params['page[size]'] = params['page[size]'] || 12;
                params['page[number]'] = params['page[number]'] || 1;

                var el = document.getElementById('produk-list');
                el.innerHTML = '<div class="lapak-loading"><i class="fas fa-spinner fa-spin"></i><p>Memuat produk...</p></div>';

                $.get(routeProduk, params, function(data) {
                    el.innerHTML = '';

                    var total = data.meta ? data.meta.total : data.data.length;
                    var countEl = document.getElementById('lapak-count');
                    if (countEl) {
                        countEl.textContent = total > 0 ? total + ' produk tersedia' : '';
                    }

                    if (!data.data.length) {
                        el.innerHTML = '<div class="lapak-empty">' +
                            '<img src="' + noProduct + '" alt="Tidak ada produk">' +
                            '<h3>Produk Tidak Ditemukan</h3>' +
                            '<p>Belum ada produk yang tersedia saat ini. Silakan cek kembali nanti.</p>' +
                        '</div>';
                        return;
                    }

                    data.data.forEach(function(item) {
                        var a = item.attributes;
                        var fotoArr = a.foto || [];
                        var firstFoto = fotoArr.length > 0 ? fotoArr[0] : noProduct;
                        var pelapakNama = (a.pelapak && a.pelapak.penduduk && a.pelapak.penduduk.nama) ? a.pelapak.penduduk.nama : 'Admin';
                        var hargaDiskon = a.harga_diskon ? formatRupiah(a.harga_diskon) : 'Hubungi Penjual';
                        var hargaAwal = a.harga ? formatRupiah(a.harga) : '';
                        var showDiskon = (a.harga && a.harga_diskon && a.harga != a.harga_diskon);
                        var kategori = (a.kategori && a.kategori.kategori) ? a.kategori.kategori : '';
                        var deskripsi = a.deskripsi || '';
                        if (deskripsi.length > 100) deskripsi = deskripsi.substring(0, 100) + '...';
                        var satuan = a.satuan || 'pcs';
                        var pesanWa = a.pesan_wa || '';

                        var lat = (a.pelapak && a.pelapak.lat) ? a.pelapak.lat : '';
                        var lng = (a.pelapak && a.pelapak.lng) ? a.pelapak.lng : '';
                        var zoom = (a.pelapak && a.pelapak.zoom) ? a.pelapak.zoom : 10;

                        var card = document.createElement('div');
                        card.className = 'lapak-card';

                        var fotoSlides = '';
                        if (fotoArr.length > 1) {
                            fotoArr.forEach(function(f, i) {
                                fotoSlides += '<div class="lapak-slide' + (i === 0 ? ' active' : '') + '">' +
                                    '<img src="' + f + '" alt="' + a.nama + '" onload="checkProdukImg(this)" onerror="this.onerror=null;this.src=\'' + noProduct + '\'" loading="lazy">' +
                                '</div>';
                            });
                        }

                        card.innerHTML =
                            '<div class="lapak-card-img">' +
                                (fotoArr.length > 1 ?
                                    '<div class="lapak-slider" data-index="0">' + fotoSlides +
                                        '<button class="lapak-slider-btn prev" onclick="slideLapak(this,-1)"><i class="fas fa-chevron-left"></i></button>' +
                                        '<button class="lapak-slider-btn next" onclick="slideLapak(this,1)"><i class="fas fa-chevron-right"></i></button>' +
                                        '<div class="lapak-slider-dots">' + fotoArr.map(function(f,i){ return '<span class="dot' + (i===0?' active':'') + '"></span>'; }).join('') + '</div>' +
                                    '</div>'
                                :
                                    '<img src="' + firstFoto + '" alt="' + a.nama + '" onload="checkProdukImg(this)" onerror="this.onerror=null;this.src=\'' + noProduct + '\'" loading="lazy">'
                                ) +
                                (showDiskon ? '<span class="lapak-badge-diskon"><i class="fas fa-tag"></i> Diskon</span>' : '') +
                                (kategori ? '<span class="lapak-badge-kategori">' + kategori + '</span>' : '') +
                            '</div>' +
                            '<div class="lapak-card-body">' +
                                '<h4 class="lapak-card-title">' + a.nama + '</h4>' +
                                '<div class="lapak-card-price">' +
                                    '<span class="price-now">' + hargaDiskon + '</span>' +
                                    (showDiskon ? '<span class="price-old">' + hargaAwal + '</span>' : '') +
                                    '<span class="price-unit">/ ' + satuan + '</span>' +
                                '</div>' +
                                (deskripsi ? '<p class="lapak-card-desc">' + deskripsi + '</p>' : '') +
                                '<div class="lapak-card-seller">' +
                                    '<i class="fas fa-user-circle"></i>' +
                                    '<span>' + pelapakNama + '</span>' +
                                    '<i class="fas fa-check-circle verified"></i>' +
                                '</div>' +
                            '</div>' +
                            '<div class="lapak-card-footer">' +
                                (pesanWa ? '<a href="' + pesanWa + '" target="_blank" rel="noopener noreferrer" class="lapak-btn-wa"><i class="fab fa-whatsapp"></i> Beli</a>' : '') +
                                (lat && lng ? '<button type="button" class="lapak-btn-lokasi" data-lat="' + lat + '" data-lng="' + lng + '" data-zoom="' + zoom + '" data-title="Lokasi ' + pelapakNama + '"><i class="fas fa-map-marker-alt"></i></button>' : '') +
                            '</div>';

                        el.appendChild(card);
                    });

                    initPagination(data);
                });
            }

            function getFilterParams() {
                var params = {};
                var kat = $('#id_kategori').val();
                var search = $('#search').val();
                if (kat) params['filter[id_produk_kategori]'] = kat;
                if (search) params['filter[search]'] = search;
                return params;
            }

            $('#btn-cari').on('click', function() {
                var params = getFilterParams();
                loadProduk(params);
                $('#btn-semua').show();
            });

            $('#search').on('keypress', function(e) {
                if (e.which === 13) { e.preventDefault(); $('#btn-cari').trigger('click'); }
            });

            $('#btn-semua').on('click', function() {
                $('#id_kategori').val('').trigger('change');
                $('#search').val('');
                loadProduk();
                $(this).hide();
            });

            $('.pagination').on('click', '.btn-page', function() {
                var params = getFilterParams();
                params['page[number]'] = $(this).data('page');
                loadProduk(params);
            });

            loadProduk();

            // Modal Lokasi Peta — jQuery custom modal (no Bootstrap JS)
            $(document).on('click', '.lapak-btn-lokasi', function() {
                var btn = $(this);
                var modal = $('#modalLokasi');
                modal.find('.lapak-modal-title').text(btn.data('title') || 'Lokasi Penjual');
                modal.find('.lapak-modal-body').html('<div id="map-lapak" style="width:100%;height:350px;border-radius:10px;"></div>');
                modal.addClass('active');
                $('body').css('overflow', 'hidden');

                setTimeout(function() {
                    var posisi = [btn.data('lat'), btn.data('lng')];
                    var zoom = btn.data('zoom') || 10;

                    if (window.mapLapak) { window.mapLapak.remove(); }

                    window.mapLapak = L.map('map-lapak', {
                        maxZoom: setting.max_zoom_peta || 18,
                        minZoom: setting.min_zoom_peta || 5
                    }).setView(posisi, zoom);

                    getBaseLayers(window.mapLapak, setting.mapbox_key, setting.jenis_peta);

                    var markerIcon = L.icon({ iconUrl: setting.icon_lapak_peta || setting.icon_pembangunan_peta, iconSize: [30, 40], iconAnchor: [15, 40] });
                    L.marker(posisi, { icon: markerIcon }).addTo(window.mapLapak);
                    L.control.scale().addTo(window.mapLapak);
                    window.mapLapak.invalidateSize();
                }, 300);
            });

            function closeModal() {
                $('#modalLokasi').removeClass('active');
                $('body').css('overflow', '');
                if (window.mapLapak) { window.mapLapak.remove(); window.mapLapak = null; }
            }

            $('#closeModalLokasi').on('click', closeModal);
            $('#modalLokasi').on('click', function(e) {
                if ($(e.target).is('.lapak-modal-overlay')) closeModal();
            });
        });

        // Slider helper
        function slideLapak(btn, dir) {
            var slider = btn.closest('.lapak-slider');
            var slides = slider.querySelectorAll('.lapak-slide');
            var dots = slider.querySelectorAll('.dot');
            var idx = parseInt(slider.dataset.index || 0) + dir;
            if (idx < 0) idx = slides.length - 1;
            if (idx >= slides.length) idx = 0;
            slider.dataset.index = idx;
            slides.forEach(function(s, i) { s.classList.toggle('active', i === idx); });
            dots.forEach(function(d, i) { d.classList.toggle('active', i === idx); });
        }
    </script>
@endpush
