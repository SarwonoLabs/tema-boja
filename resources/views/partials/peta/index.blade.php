@extends('theme::template')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('bootstrap/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/leaflet-measure-path.css') }}">
    <link rel="stylesheet" href="{{ asset('css/MarkerCluster.css') }}">
    <link rel="stylesheet" href="{{ asset('css/MarkerCluster.Default.css') }}">
    <link rel="stylesheet" href="{{ asset('css/leaflet.groupedlayercontrol.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/leaflet.fullscreen.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/peta.css') }}">
    <style>
        /* ---- Peta Boja Elegant ---- */
        /* Sembunyikan navbar, hero header dan footer di halaman peta */
        .navbar-boja,.mobile-panel,.hero-boja,.ticker-bar,.qa-ticker,.category-bar-boja,.footer-boja,.back-to-top{display:none !important;}
        body{padding-top:0 !important;margin-top:0 !important;overflow:hidden;}
        .peta-wrapper{position:relative;overflow:hidden;border-radius:0;margin-top:0;width:100%;height:100vh;}

        /* ---- Loading Overlay ---- */
        .peta-loading{
            position:absolute;inset:0;z-index:1100;
            background:linear-gradient(135deg,#f0faf0 0%,#e8f5e8 100%);
            display:flex;flex-direction:column;align-items:center;justify-content:center;
            transition:opacity .6s ease,visibility .6s ease;
        }
        .peta-loading.loaded{opacity:0;visibility:hidden;pointer-events:none;}
        .peta-loading-spinner{
            width:50px;height:50px;border-radius:50%;
            border:3px solid #e8f0e8;border-top-color:#2F855A;
            animation:petaSpin 1s linear infinite;
        }
        @keyframes petaSpin{to{transform:rotate(360deg)}}
        .peta-loading p{
            margin-top:16px;font-family:'Plus Jakarta Sans',sans-serif;
            font-size:.9rem;font-weight:600;color:#2F855A;
        }

        /* ---- Peta Navbar ---- */
        .peta-navbar{
            position:absolute;top:0;left:0;right:0;z-index:1000;
            background:linear-gradient(135deg,#2F855A 0%,#276749 60%,#1C4D35 100%);
            box-shadow:0 4px 24px rgba(28,77,53,.25),0 1px 3px rgba(0,0,0,.08);
            display:flex;align-items:center;justify-content:space-between;
            padding:0 24px;height:56px;
            transition:all .4s cubic-bezier(.4,0,.2,1);
        }

        /* Left — logo + title */
        .peta-navbar-left{display:flex;align-items:center;gap:0;}
        .peta-navbar-brand{display:flex;align-items:center;gap:14px;}
        .peta-navbar-logo{
            width:36px;height:36px;border-radius:10px;overflow:hidden;
            background:rgba(255,255,255,.18);
            display:flex;align-items:center;justify-content:center;flex-shrink:0;
            border:1.5px solid rgba(255,255,255,.25);
            transition:transform .25s ease;
        }
        .peta-navbar-logo:hover{transform:scale(1.05);}
        .peta-navbar-logo img{width:100%;height:100%;object-fit:cover;border-radius:9px;}
        .peta-navbar-logo i{color:#fff;font-size:.95rem;}
        .peta-navbar-info h1{
            font-family:'Plus Jakarta Sans',sans-serif;
            font-size:.9rem;font-weight:700;color:#fff;
            margin:0;line-height:1.3;letter-spacing:.01em;
            text-shadow:0 1px 2px rgba(0,0,0,.1);
        }
        .peta-navbar-info p{
            font-size:.68rem;color:rgba(255,255,255,.7);margin:1px 0 0;
            font-weight:500;letter-spacing:.3px;
        }

        /* Right — action buttons */
        .peta-navbar-right{display:flex;align-items:center;gap:6px;}

        .peta-navbar-btn{
            display:flex;align-items:center;gap:7px;
            padding:7px 14px;border-radius:9px;
            font-family:'Plus Jakarta Sans',sans-serif;
            font-size:.76rem;font-weight:600;
            text-decoration:none !important;transition:all .25s ease;
            border:none;cursor:pointer;line-height:1;
            color:rgba(255,255,255,.85);
            background:rgba(255,255,255,.12);
            border:1.5px solid rgba(255,255,255,.18);
        }
        .peta-navbar-btn:hover{
            background:rgba(255,255,255,.22);color:#fff;
            border-color:rgba(255,255,255,.35);
            transform:translateY(-1px);
        }
        .peta-navbar-btn:active{transform:translateY(0);background:rgba(255,255,255,.28);}
        .peta-navbar-btn i{font-size:.72rem;}
        .peta-navbar-btn.active{
            background:rgba(255,255,255,.25);color:#fff;
            border-color:rgba(255,255,255,.4);
        }

        /* Separator dot */
        .peta-navbar-sep{
            width:3px;height:3px;border-radius:50%;
            background:rgba(255,255,255,.3);flex-shrink:0;
        }

        /* Hide default Leaflet fullscreen button — we use navbar button */
        .leaflet-control-fullscreen{display:none !important;}

        /* ---- Floating Coordinates Bar ---- */
        .peta-coords{
            position:fixed;bottom:10px;left:50%;transform:translateX(-50%);z-index:1000;
            background:rgba(26,32,44,.75);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);
            border-radius:12px;padding:8px 18px;
            font-family:'JetBrains Mono',monospace;font-size:.72rem;
            color:rgba(255,255,255,.85);letter-spacing:.5px;
            display:flex;align-items:center;gap:16px;
            box-shadow:0 4px 20px rgba(0,0,0,.2);
            border:1px solid rgba(255,255,255,.08);
        }
        .peta-coords span{display:flex;align-items:center;gap:5px;}
        .peta-coords i{color:#68D391;font-size:.65rem;}

        #map{width:100%;height:calc(100vh - 56px) !important;border-radius:0;margin-top:56px;}

        /* Fullscreen mode — navbar hidden, map full */
        .peta-wrapper.fullscreen-mode .peta-navbar{
            transform:translateY(-100%);pointer-events:none;opacity:0;
        }
        .peta-wrapper.fullscreen-mode #map{
            height:100vh !important;margin-top:0;
        }
        .peta-wrapper.fullscreen-mode .peta-coords{bottom:10px;}
        /* Floating exit-fullscreen button */
        .peta-exit-fs{
            position:fixed;top:16px;right:16px;z-index:1001;
            display:none;align-items:center;gap:8px;
            padding:10px 18px;border-radius:12px;
            background:rgba(26,32,44,.8);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);
            color:#fff;font-family:'Plus Jakarta Sans',sans-serif;
            font-size:.78rem;font-weight:600;cursor:pointer;
            border:1px solid rgba(255,255,255,.12);
            box-shadow:0 8px 24px rgba(0,0,0,.25);
            transition:all .25s ease;
        }
        .peta-exit-fs:hover{background:rgba(26,32,44,.92);transform:translateY(-1px);}
        .peta-exit-fs i{font-size:.72rem;}
        .peta-wrapper.fullscreen-mode .peta-exit-fs{display:flex;}

        /* ---- Fix Double Popup ---- */
        .leaflet-tooltip{pointer-events:none;transition:opacity .2s ease;}
        .leaflet-popup-pane ~ .leaflet-tooltip-pane .leaflet-tooltip{opacity:0 !important;}

        /* ---- Popup Elegant ---- */
        #map .leaflet-popup-content-wrapper{
            border-radius:16px !important;
            box-shadow:0 12px 48px rgba(0,0,0,.16),0 2px 8px rgba(0,0,0,.08) !important;
            padding:0 !important;overflow:hidden;
            border:1px solid rgba(255,255,255,.3);
        }
        #map .leaflet-popup-content{
            margin:0 !important;padding:18px 20px 16px !important;
            font-family:'Nunito',sans-serif;font-size:.88rem;color:#2d3748;
            min-width:230px;max-width:320px;
            height:auto;overflow-y:auto;
        }
        #map .leaflet-popup-tip-container{filter:drop-shadow(0 2px 4px rgba(0,0,0,.1));}
        #map .leaflet-popup-tip{border-top-color:#fff;}
        #map .leaflet-popup-close-button{
            top:10px !important;right:12px !important;
            font-size:18px !important;color:#a0aec0 !important;
            width:30px !important;height:30px !important;
            display:flex;align-items:center;justify-content:center;
            background:rgba(0,0,0,.04) !important;border-radius:10px !important;
            transition:all .2s ease !important;
        }
        #map .leaflet-popup-close-button:hover{
            color:#fff !important;background:rgba(229,62,62,.85) !important;
        }

        /* Popup heading */
        #map .leaflet-popup-content .firstHeading,
        #map .leaflet-popup-content h5{
            font-family:'Plus Jakarta Sans',sans-serif;
            font-size:.95rem;font-weight:700;color:#1a202c;
            margin:0 0 12px 0;padding-bottom:10px;
            border-bottom:2px solid #e8f0e8;
            display:flex;align-items:center;gap:8px;
        }
        #map .leaflet-popup-content .firstHeading::before{
            content:'\f279';font-family:'Font Awesome 5 Free';font-weight:900;
            color:#2F855A;font-size:.85rem;
        }

        /* Popup buttons */
        #map .leaflet-popup-content .btn-social{
            border-radius:12px !important;
            font-size:.82rem !important;
            padding:10px 16px !important;
            margin-bottom:8px !important;
            border:none !important;
            transition:all .25s ease !important;
            display:flex !important;align-items:center;gap:10px;
            text-align:left !important;
            letter-spacing:.2px;
        }
        #map .leaflet-popup-content .bg-navy{
            background:linear-gradient(135deg,#2F855A,#276749) !important;
            color:#fff !important;
            box-shadow:0 3px 10px rgba(47,133,90,.2) !important;
        }
        #map .leaflet-popup-content .bg-navy:hover{
            background:linear-gradient(135deg,#276749,#1C4D35) !important;
            transform:translateY(-2px);
            box-shadow:0 6px 16px rgba(47,133,90,.35) !important;
        }
        #map .leaflet-popup-content .bg-navy i{
            width:20px;text-align:center;
        }

        /* Collapse sections */
        .leaflet-popup-content [id^="collapseStat"]{display:none;}
        .leaflet-popup-content [id^="collapseStat"] .card.card-body{
            max-height:40vh;overflow-y:auto;padding:8px 4px 8px 10px;
            border-radius:10px !important;background:#fafff8;
            border:1px solid #e8f0e8;
        }
        .leaflet-popup-content [id^="collapseStat"] .card.card-body::-webkit-scrollbar{width:5px;}
        .leaflet-popup-content [id^="collapseStat"] .card.card-body::-webkit-scrollbar-track{background:transparent;}
        .leaflet-popup-content [id^="collapseStat"] .card.card-body::-webkit-scrollbar-thumb{background:#c6d8c6;border-radius:4px;}
        .leaflet-popup-content [id^="collapseStat"] ul{
            list-style:none;padding:0;margin:0;
        }
        .leaflet-popup-content [id^="collapseStat"] ul li{
            padding:0;border-bottom:1px solid #edf5ed;
        }
        .leaflet-popup-content [id^="collapseStat"] ul li:last-child{border-bottom:none;}
        .leaflet-popup-content [id^="collapseStat"] ul li a{
            color:#2d6b3f;text-decoration:none;font-size:.82rem;
            transition:all .2s ease;display:block;padding:8px 10px;border-radius:8px;
        }
        .leaflet-popup-content [id^="collapseStat"] ul li a:hover{
            color:#1C4D35;background:#e8f5e8;
        }

        /* ---- Kantor Desa FA marker ---- */
        .kantor-desa-divicon{background:none !important;border:none !important;}
        .kantor-desa-marker{
            display:flex;align-items:center;justify-content:center;
            width:44px;height:44px;border-radius:50%;
            background:linear-gradient(135deg,#2F855A,#276749);
            box-shadow:0 4px 16px rgba(47,133,90,.45);
            color:#fff;font-size:1.15rem;
            border:3px solid #fff;
            animation:kantorPulse 2.5s ease-in-out infinite;
        }
        @keyframes kantorPulse{
            0%,100%{box-shadow:0 4px 16px rgba(47,133,90,.45);}
            50%{box-shadow:0 4px 16px rgba(47,133,90,.45),0 0 0 10px rgba(47,133,90,.1);}
        }
        /* Kantor desa popup */
        .kantor_desa .leaflet-popup-content-wrapper{
            border-top:4px solid #2F855A !important;
        }

        /* ---- Leaflet Controls Elegant ---- */
        .leaflet-control-layers{
            border-radius:14px !important;
            box-shadow:0 8px 32px rgba(0,0,0,.12) !important;
            border:1px solid rgba(255,255,255,.4) !important;
            background:rgba(255,255,255,.92) !important;
            backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);
        }
        .leaflet-control-layers-expanded{padding:8px 12px !important;}
        .leaflet-control-layers label{font-size:.82rem;padding:3px 0;}

        .leaflet-control-zoom{
            border-radius:14px !important;overflow:hidden;
            box-shadow:0 4px 16px rgba(0,0,0,.1) !important;
            border:1px solid rgba(255,255,255,.4) !important;
        }
        .leaflet-control-zoom a{
            width:36px !important;height:36px !important;line-height:36px !important;
            font-size:16px !important;color:#2d3748 !important;
            background:rgba(255,255,255,.92) !important;
            backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);
            border:none !important;transition:all .2s ease !important;
        }
        .leaflet-control-zoom a:hover{
            background:#2F855A !important;color:#fff !important;
        }
        .leaflet-control-zoom-in{border-bottom:1px solid #e8f0e8 !important;}

        /* ---- Legend Elegant ---- */
        .info.legend{
            border-radius:14px !important;
            box-shadow:0 8px 32px rgba(0,0,0,.1) !important;
            padding:12px 16px !important;
            background:rgba(255,255,255,.92) !important;
            backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);
            border:1px solid rgba(255,255,255,.4) !important;
        }

        /* ---- Marker Cluster ---- */
        .marker-cluster-small,.marker-cluster-medium,.marker-cluster-large{
            border-radius:50% !important;transition:all .3s ease;
        }
        .marker-cluster-small{background:rgba(47,133,90,.15) !important;}
        .marker-cluster-small div{
            background:linear-gradient(135deg,#2F855A,#38a169) !important;
            color:#fff !important;font-weight:700 !important;font-size:.8rem !important;
            box-shadow:0 2px 8px rgba(47,133,90,.3);
        }
        .marker-cluster-medium{background:rgba(212,175,55,.15) !important;}
        .marker-cluster-medium div{
            background:linear-gradient(135deg,#D4AF37,#c9a02d) !important;
            color:#fff !important;font-weight:700 !important;font-size:.8rem !important;
            box-shadow:0 2px 8px rgba(212,175,55,.3);
        }
        .marker-cluster-large{background:rgba(229,62,62,.15) !important;}
        .marker-cluster-large div{
            background:linear-gradient(135deg,#e53e3e,#c53030) !important;
            color:#fff !important;font-weight:700 !important;font-size:.8rem !important;
            box-shadow:0 2px 8px rgba(229,62,62,.3);
        }

        /* ---- QR Code ---- */
        #qrcode{
            background:rgba(255,255,255,.88) !important;backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);
            border-radius:14px !important;
            box-shadow:0 4px 20px rgba(0,0,0,.1) !important;
            padding:6px !important;border:1px solid rgba(255,255,255,.4) !important;
        }
        #qrcode img{border-radius:10px;width:56px;height:56px;}

        /* ---- Leaflet Scale ---- */
        .leaflet-control-scale-line{
            border-radius:6px !important;
            background:rgba(26,32,44,.65) !important;
            color:rgba(255,255,255,.9) !important;
            border-color:rgba(255,255,255,.3) !important;
            font-size:.68rem !important;padding:2px 8px !important;
            backdrop-filter:blur(8px);
        }

        /* Table styling */

        /* ---- Leaflet Scale ---- */
        table{table-layout:fixed;white-space:normal !important;}
        td{word-wrap:break-word;}
        .persil{min-width:350px;}
        .persil td{padding-right:1rem;}
        .persil h4{font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;color:#1a202c;}

        /* ---- Bootstrap 3 modal fix ---- */
        .modal-backdrop{z-index:9998 !important;}
        .modal{z-index:9999 !important;}
        .modal-content{
            border-radius:16px;overflow:hidden;
            box-shadow:0 20px 60px rgba(0,0,0,.2);
            border:none;
        }
        .modal-header{
            border-bottom:1px solid #e8f0e8;padding:18px 24px;
            background:linear-gradient(135deg,#f8fdf8,#f0faf0);
        }
        .modal-header .modal-title{
            font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;
            font-size:1rem;color:#1a202c;
        }
        .modal-header .close{font-size:24px;opacity:.5;transition:opacity .2s;}
        .modal-header .close:hover{opacity:1;}

        /* ---- Responsive ---- */
        @media(max-width:768px){
            .peta-navbar{height:50px;padding:0 14px;}
            .peta-navbar-logo{width:32px;height:32px;border-radius:9px;}
            .peta-navbar-info h1{font-size:.82rem;}
            .peta-navbar-info p{display:none;}
            .peta-navbar-brand{gap:10px;}
            .peta-navbar-btn span{display:none;}
            .peta-navbar-btn{padding:7px 9px;}
            .peta-navbar-sep{display:none;}
            #map{height:calc(100vh - 50px) !important;margin-top:50px;}
            .peta-wrapper.fullscreen-mode #map{height:100vh !important;margin-top:0;}
            .peta-coords{font-size:.65rem;padding:6px 14px;gap:12px;bottom:8px;}
        }
        @media(max-width:480px){
            .peta-navbar{height:46px;padding:0 10px;}
            .peta-navbar-info h1{font-size:.78rem;}
            .peta-navbar-logo{width:28px;height:28px;border-radius:8px;}
            #map{height:calc(100vh - 46px) !important;margin-top:46px;}
            .peta-wrapper.fullscreen-mode #map{height:100vh !important;margin-top:0;}
            .peta-coords{display:none;}
        }
    </style>
@endpush

@section('layout')
    <div class="peta-wrapper">
        {{-- Loading Overlay --}}
        <div class="peta-loading" id="petaLoading">
            <div class="peta-loading-spinner"></div>
            <p>Memuat peta wilayah...</p>
        </div>

        {{-- Peta Navbar --}}
        <nav class="peta-navbar" id="petaNavbar">
            <div class="peta-navbar-left">
                <div class="peta-navbar-brand">
                    <div class="peta-navbar-logo">
                        @if(identitas('logo'))
                            <img src="{{ gambar_desa(identitas('logo')) }}" alt="Logo Desa">
                        @else
                            <i class="fa fa-map"></i>
                        @endif
                    </div>
                    <div class="peta-navbar-info">
                        <h1>Peta {{ ucwords(setting('sebutan_desa')) }} {{ ucwords(identitas('nama_desa')) }}</h1>
                        <p>Peta Interaktif Wilayah Desa</p>
                    </div>
                </div>
            </div>
            <div class="peta-navbar-right">
                <button type="button" class="peta-navbar-btn" id="btnFullscreen" title="Mode Layar Penuh">
                    <i class="fa fa-expand"></i> <span>Layar Penuh</span>
                </button>
                <div class="peta-navbar-sep"></div>
                <a href="{{ ci_route('') }}" class="peta-navbar-btn" title="Kembali ke Beranda">
                    <i class="fa fa-home"></i> <span>Beranda</span>
                </a>
            </div>
        </nav>

        {{-- Exit Fullscreen Button (muncul saat fullscreen) --}}
        <button type="button" class="peta-exit-fs" id="btnExitFs">
            <i class="fa fa-compress"></i> Keluar Layar Penuh
        </button>

        {{-- Map Container --}}
        <div id="map">
            <div class="leaflet-top leaflet-left">
                <div id="isi_popup" style="visibility: hidden;">
                    <div id="content">
                        <h5 id="firstHeading" class="firstHeading"></h5>
                        <div id="bodyContent"></div>
                    </div>
                </div>
                <div id="isi_popup_dusun"></div>
                <div id="isi_popup_rw"></div>
                <div id="isi_popup_rt"></div>
            </div>
            <div class="leaflet-bottom leaflet-left">
                <div id="qrcode">
                    <div class="panel-body-lg">
                        <a href="https://github.com/OpenSID/OpenSID">
                            <img src="{{ to_base64(GAMBAR_QRCODE) }}" alt="OpenSID">
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Floating Coordinates Bar --}}
        <div class="peta-coords" id="petaCoords">
            <span><i class="fa fa-crosshairs"></i> <span id="coordLat">-</span>, <span id="coordLng">-</span></span>
            <span><i class="fa fa-search-plus"></i> Zoom: <span id="coordZoom">-</span></span>
        </div>
    </div>

    <div class="modal fade" id="modalKecil" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
        <div class="modal-dialog modal-sm">
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title' id='myModalLabel'></h4>
                </div>
                <div class="fetched-data"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSedang" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
        <div class="modal-dialog">
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title' id='myModalLabel'></h4>
                </div>
                <div class="fetched-data"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalBesar" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
        <div class="modal-dialog modal-lg">
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title' id='myModalLabel'><i class='fa fa-exclamation-triangle text-red'></i></h4>
                </div>
                <div class="fetched-data"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('theme::commons.asset_highcharts')
    <script src="{{ theme_asset('js/helper.js') }}"></script>
    <script>
        (function() {
            let infoWindow;
            window.onload = function() {
                const _url = `{{ ci_route('internal_api.peta') }}`
                // remove elemen lain selain peta
                $('.peta-wrapper').siblings('.container').remove()
                $.get(_url, {}, function(json) {
                    generatePopupDesa(json.data[0].attributes)
                    generatePopupDusun(json.data[0].attributes)
                    generatePopupRw(json.data[0].attributes)
                    generatePopupRt(json.data[0].attributes)
                    generatePeta(json.data[0].attributes)

                    $('#isi_popup_dusun').remove();
                    $('#isi_popup_rw').remove();
                    $('#isi_popup_rt').remove();
                    $('#isi_popup').remove();
                    $('.spinner-grow').parent().remove()
                })

                const generatePopupDesa = function(data) {
                    let _listLink = [],
                        _elmPopup
                    const _link = '{{ ci_route('statistik_web.chart_gis_desa') }}'
                    _elmPopup = document.getElementById('isi_popup')
                    _elmPopup.querySelector('#content').querySelector('#firstHeading').innerHTML = `Wilayah {{ ucwords(setting('sebutan_desa')) }} ${data.desa.nama_desa}`
                    const _title = `Statistik Penduduk {{ ucwords(setting('sebutan_desa')) }} ${capitalizeFirstCharacterOfEachWord(data.desa.nama_desa)}`
                    // statistik penduduk
                    if (data.pengaturan.includes('Statistik Penduduk')) {
                        _listLink = []
                        for (let key in data.list_ref) {
                            _listLink.push(`<li><a href="${_link}/${key}/${data.desa.nama_desa.replace(/\s+/g, '_')}" data-remote="false" data-toggle="modal" data-target="#modalSedang" data-title="Statistik Penduduk ${_title}" >${data.list_ref[key]}</a></li>`)
                        }
                        const _listStatistikPenduduk = `<p><a href="#collapseStatPenduduk" class="btn btn-social bg-navy btn-sm btn-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" title="Statistik Penduduk" data-target="#collapseStatPenduduk" aria-expanded="false" aria-controls="collapseStatPenduduk"><i class="fa fa-bar-chart"></i>&nbsp;&nbsp;Statistik Penduduk&nbsp;&nbsp;</a></p>
          <div class="box-body no-padding" id="collapseStatPenduduk" style="display: none;">
            <div class="card card-body">
              <ul>
              ${_listLink.join('')}
              </ul>
            </div>
          </div>`
                        _elmPopup.querySelector('#content').querySelector('#bodyContent').innerHTML += _listStatistikPenduduk
                    }
                    // statistik bantuan
                    if (data.pengaturan.includes('Statistik Bantuan')) {
                        _listLink = []
                        for (let key in data.list_bantuan) {
                            _listLink.push(`<li><a href="${_link}/${key}/${data.desa.nama_desa.replace(/\s+/g, '_')}" data-remote="false" data-toggle="modal" data-target="#modalSedang" data-title="Statistik Bantuan ${_title}">${data.list_bantuan[key]}</a></li>`)
                        }
                        const _listStatistikBantuan = `<p><a href="#collapseStatBantuan" class="btn btn-social bg-navy btn-sm btn-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" title="Statistik Bantuan" data-target="#collapseStatBantuan" aria-expanded="false" aria-controls="collapseStatBantuan"><i class="fa fa-heart"></i>&nbsp;&nbsp;Statistik Bantuan&nbsp;&nbsp;</a></p>
          <div class="box-body no-padding" id="collapseStatBantuan" style="display: none;">
            <div class="card card-body">
              <ul>
              ${_listLink.join('')}
              </ul>
            </div>
          </div>`
                        _elmPopup.querySelector('#content').querySelector('#bodyContent').innerHTML += _listStatistikBantuan
                    }
                    // statistik aparatur
                    if (data.pengaturan.includes('Aparatur Desa')) {
                        _elmPopup.querySelector('#content').querySelector('#bodyContent').innerHTML +=
                            `<p><a href="{{ ci_route('load_aparatur_desa') }}" class="btn btn-social bg-navy btn-sm btn-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" data-title="{{ ucwords(setting('sebutan_pemerintah_desa')) }}" data-remote="false" data-toggle="modal" data-target="#modalKecil"><i class="fa fa-user"></i>&nbsp;&nbsp;{{ ucwords(setting('sebutan_pemerintah_desa')) }}&nbsp;&nbsp;</a></p>`
                    }
                }
                const generatePopupDusun = function(data) {
                    const _elmPopup = document.getElementById('isi_popup_dusun')
                    const _link = '{{ ci_route('statistik_web.chart_gis_dusun') }}'
                    const _title = `{{ ucwords(setting('sebutan_desa')) }} ${capitalizeFirstCharacterOfEachWord(data.desa.nama_desa)}`
                    const _wilayah = {
                        level: 1,
                        key: 'dusun',
                        sebutan: "{{ ucwords(setting('sebutan_kepala_dusun')) }}",
                        div_parent: 'isi_popup_dusun'
                    }
                    _elmPopup.innerHTML = generatePopupElement(data, data.pengaturan, data.dusun_gis, _link, _title, _wilayah)
                }
                const generatePopupRw = function(data) {
                    const _elmPopup = document.getElementById('isi_popup_rw')
                    const _link = '{{ ci_route('statistik_web.chart_gis_rw') }}'
                    const _title = `{{ ucwords(setting('sebutan_dusun')) }}`
                    const _wilayah = {
                        level: 2,
                        key: 'rw',
                        sebutan: "RW",
                        div_parent: 'isi_popup_rw'
                    }
                    _elmPopup.innerHTML = generatePopupElement(data, data.pengaturan, data.rw_gis, _link, _title, _wilayah)
                }
                const generatePopupRt = function(data) {
                    const _elmPopup = document.getElementById('isi_popup_rt')
                    const _link = '{{ ci_route('statistik_web.chart_gis_rt') }}'
                    const _title = `{{ ucwords(setting('sebutan_dusun')) }}`
                    const _wilayah = {
                        level: 3,
                        key: 'rt',
                        sebutan: "RT",
                        div_parent: 'isi_popup_rt'
                    }
                    _elmPopup.innerHTML = generatePopupElement(data, data.pengaturan, data.rt_gis, _link, _title, _wilayah)
                }
                const generatePopupElement = function(data, pengaturan, gis, _link, _title, _wilayah) {
                    let _listLink = [],
                        _params, _newTitle
                    let _parentElementHTML = ``,
                        _elemenHTML, _contentHTML = ``,
                        _listStatistikPenduduk, _listStatistikBantuan

                    for (let _key in gis) {
                        _elemenHTML = ``
                        _contentHTML = ``
                        switch (_wilayah['key']) {
                            case 'dusun':
                                _params = (gis[_key]['dusun'] || '').replace(/\s+/g, '_')
                                _newTitle = `${_title} ${capitalizeFirstCharacterOfEachWord(gis[_key]['dusun'])}`
                                break;
                            case 'rw':
                                _params = `${(gis[_key]['dusun'] || '').replace(/\s+/g, '_')}/${(gis[_key]['rw'] || '').replace(/\s+/g, '_')}`
                                _newTitle = `RW ${capitalizeFirstCharacterOfEachWord(gis[_key]['rw'])} ${_title} ${capitalizeFirstCharacterOfEachWord(gis[_key]['dusun'])}`
                                break;
                            case 'rt':
                                _params = `${(gis[_key]['dusun'] || '').replace(/\s+/g, '_')}/${(gis[_key]['rw'] || '').replace(/\s+/g, '_')}/${(gis[_key]['rt'] || '').replace(/\s+/g, '_')}`
                                _newTitle = `RT ${capitalizeFirstCharacterOfEachWord(gis[_key]['rt'])} RW ${capitalizeFirstCharacterOfEachWord(gis[_key]['rw'])} ${_title} ${capitalizeFirstCharacterOfEachWord(gis[_key]['dusun'])}`
                                break;
                        }

                        // statistik penduduk
                        if (pengaturan.includes('Statistik Penduduk')) {
                            _listLink = []
                            for (let key in data.list_ref) {
                                _listLink.push(`<li><a href="${_link}/${key}/${_params}" data-remote="false" data-toggle="modal" data-target="#modalSedang" data-title="Statistik Penduduk ${_newTitle}" >${data.list_ref[key]}</a></li>`)
                            }
                            _listStatistikPenduduk = `<p><a href="#collapseStatPenduduk" class="btn btn-social bg-navy btn-sm btn-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" title="Statistik Penduduk" data-target="#collapseStatPenduduk" aria-expanded="false" aria-controls="collapseStatPenduduk"><i class="fa fa-bar-chart"></i>&nbsp;&nbsp;Statistik Penduduk&nbsp;&nbsp;</a></p>
            <div class="box-body no-padding" id="collapseStatPenduduk" style="display: none;">
              <div class="card card-body">
                <ul>
                ${_listLink.join('')}
                </ul>
              </div>
            </div>`
                            _contentHTML += _listStatistikPenduduk
                        }
                        // statistik bantuan
                        if (pengaturan.includes('Statistik Bantuan')) {
                            _listLink = []
                            for (let key in data.list_bantuan) {
                                _listLink.push(`<li><a href="${_link}/${key}/${_params}" data-remote="false" data-toggle="modal" data-target="#modalSedang" data-title="Statistik Bantuan ${_newTitle}">${data.list_bantuan[key]}</a></li>`)
                            }
                            _listStatistikBantuan = `<p><a href="#collapseStatBantuan" class="btn btn-social bg-navy btn-sm btn-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" title="Statistik Bantuan" data-target="#collapseStatBantuan" aria-expanded="false" aria-controls="collapseStatBantuan"><i class="fa fa-heart"></i>&nbsp;&nbsp;Statistik Bantuan&nbsp;&nbsp;</a></p>
            <div class="box-body no-padding" id="collapseStatBantuan" style="display: none;">
              <div class="card card-body">
                <ul>
                ${_listLink.join('')}
                </ul>
              </div>
            </div>`
                            _contentHTML += _listStatistikBantuan
                        }
                        // statistik aparatur
                        if (pengaturan.includes('Aparatur Desa')) {
                            _contentHTML +=
                                `<p><a href="{{ ci_route('load_aparatur_wilayah') }}/${gis[_key]['id_kepala']}/${_wilayah['level']}" class="btn btn-social bg-navy btn-sm btn-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" data-title="{{ ucwords(setting('sebutan_kepala_dusun')) . ' ' . $dusun['dusun'] }}" data-remote="false" data-toggle="modal" data-target="#modalKecil"><i class="fa fa-user"></i>&nbsp;&nbsp;${_wilayah['sebutan']}&nbsp;&nbsp;</a></p>`
                        }
                        _elemenHTML = `
          <div id="${_wilayah['div_parent']}_${_key}" style="visibility: hidden;">
            <div id="content">
              <h5 id="firstHeading" class="firstHeading">Wilayah ${_newTitle}</h5>
                <div id="bodyContent">
                  ${_contentHTML}
                </div>
            </div>
          </div>`
                        _parentElementHTML += _elemenHTML
                    }
                    return _parentElementHTML
                }
                const generatePeta = function(data) {
                    let posisi = [-1.0546279422758742, 116.71875000000001];
                    let zoom = 10;
                    let wilayah_desa;
                    let options = {
                        maxZoom: {{ setting('max_zoom_peta') }},
                        minZoom: {{ setting('min_zoom_peta') }},
                        fullscreenControl: {
                            position: 'topright'
                        }
                    }
                    if (data.desa['lat'] && data.desa['lng']) {
                        posisi = [data.desa['lat'], data.desa['lng']]
                        zoom = data.desa['zoom'] ?? 10
                    } else if (data.desa['path']) {
                        wilayah_desa = data.desa['path'];
                        posisi = wilayah_desa[0][0];
                        zoom = data.desa['zoom'] ?? 10
                    }
                    //Inisialisasi tampilan peta
                    const mymap = L.map('map', options).setView(posisi, zoom);
                    if (data.desa['path']) {
                        mymap.fitBounds(JSON.parse(data.desa.path));
                    }

                    //Menampilkan overlayLayers Peta Semua Wilayah
                    let marker_desa = [];
                    let marker_dusun = [];
                    let marker_rw = [];
                    let marker_rt = [];
                    let marker_area = [];
                    let marker_garis = [];
                    let marker_lokasi = [];
                    let markers = new L.MarkerClusterGroup();
                    let markersList = [];
                    let marker_legend = [];
                    let mark_desa = [];
                    let mark_covid = [];

                    // deklarasi variabel
                    let all_area = JSON.stringify(data.area)
                    let all_garis = JSON.stringify(data.garis)
                    let all_lokasi = JSON.stringify(data.lokasi)
                    let all_lokasi_pembangunan = JSON.stringify(data.lokasi_pembangunan)
                    let LOKASI_SIMBOL_LOKASI = '{{ base_url(LOKASI_SIMBOL_LOKASI) }}';
                    let favico_desa = '{{ favico_desa() }}';
                    let LOKASI_FOTO_AREA = '{{ base_url(LOKASI_FOTO_AREA) }}';
                    let LOKASI_FOTO_GARIS = '{{ base_url(LOKASI_FOTO_GARIS) }}';
                    let LOKASI_FOTO_LOKASI = '{{ base_url(LOKASI_FOTO_LOKASI) }}';
                    let LOKASI_GALERI = '{{ base_url(LOKASI_GALERI) }}';
                    let info_pembangunan = '{{ ci_route('pembangunan') }}';
                    let all_persil = JSON.stringify(data.persil)
                    let TAMPIL_LUAS = '{{ setting('tampil_luas_peta') }}';
                    let PENGATURAN_WILAYAH = '{!! SebutanDesa(setting('default_tampil_peta_wilayah')) ?: [] !!}';
                    let PENGATURAN_INFRASTRUKTUR = '{!! SebutanDesa(setting('default_tampil_peta_infrastruktur')) ?: [] !!}';
                    let WILAYAH_INFRASTRUKTUR = PENGATURAN_WILAYAH.concat(PENGATURAN_INFRASTRUKTUR);

                    //OVERLAY WILAYAH DESA
                    if (data.desa['path']) {
                        set_marker_desa_content(marker_desa, data.desa, "{{ ucwords(setting('sebutan_desa')) }} ${data.desa['nama_desa']}", "{{ favico_desa() }}", '#isi_popup');
                    }

                    //OVERLAY WILAYAH DUSUN
                    if (data.dusun_gis) {
                        set_marker_multi_content(marker_dusun, JSON.stringify(data.dusun_gis), '{{ ucwords(setting('sebutan_dusun')) }}', 'dusun', '#isi_popup_dusun_', '{{ favico_desa() }}');
                    }

                    //OVERLAY WILAYAH RW
                    if (data.rw_gis) {
                        set_marker_content(marker_rw, JSON.stringify(data.rw_gis), 'RW', 'rw', '#isi_popup_rw_', '{{ favico_desa() }}');
                    }

                    //OVERLAY WILAYAH RT
                    if (data.rt_gis) {
                        set_marker_content(marker_rt, JSON.stringify(data.rt_gis), 'RT', 'rt', '#isi_popup_rt_', '{{ favico_desa() }}');
                    }

                    //Menampilkan overlayLayers Peta Semua Wilayah
                    let overlayLayers = overlayWil(
                        marker_desa,
                        marker_dusun,
                        marker_rw,
                        marker_rt,
                        "{{ ucwords(setting('sebutan_desa')) }}",
                        "{{ ucwords(setting('sebutan_dusun')) }}",
                        true,
                        TAMPIL_LUAS.toString()
                    );

                    //Menampilkan BaseLayers Peta
                    let baseLayers = getBaseLayers(mymap, "{{ setting('mapbox_key') }}", "{{ setting('jenis_peta') }}");

                    //Geolocation IP Route/GPS
                    geoLocation(mymap);

                    //Menambahkan zoom scale ke peta
                    L.control.scale().addTo(mymap);

                    //Mencetak peta ke PNG
                    cetakPeta(mymap);

                    //Menambahkan Legenda Ke Peta
                    let legenda_desa = L.control({ position: 'bottomright' });
                    let legenda_dusun = L.control({ position: 'bottomright' });
                    let legenda_rw = L.control({ position: 'bottomright' });
                    let legenda_rt = L.control({ position: 'bottomright' });

                    mymap.on('overlayadd', function(eventLayer) {
                        if (eventLayer.name === 'Peta Wilayah Desa') {
                            setlegendPetaDesa(legenda_desa, mymap, data.desa, '{{ ucwords(setting('sebutan_desa')) }}', data.desa['nama_desa']);
                        }
                        if (eventLayer.name === 'Peta Wilayah Dusun') {
                            setlegendPeta(legenda_dusun, mymap, JSON.stringify(data.dusun_gis), '{{ ucwords(setting('sebutan_dusun')) }}', 'dusun', '', '');
                        }
                        if (eventLayer.name === 'Peta Wilayah RW') {
                            setlegendPeta(legenda_rw, mymap, JSON.stringify(data.rw_gis), 'RW', 'rw', '{{ ucwords(setting('sebutan_dusun')) }}');
                        }
                        if (eventLayer.name === 'Peta Wilayah RT') {
                            setlegendPeta(legenda_rt, mymap, JSON.stringify(data.rt_gis), 'RT', 'rt', 'RW');
                        }
                    });

                    mymap.on('overlayremove', function(eventLayer) {
                        if (eventLayer.name === 'Peta Wilayah Desa') { mymap.removeControl(legenda_desa); }
                        if (eventLayer.name === 'Peta Wilayah Dusun') { mymap.removeControl(legenda_dusun); }
                        if (eventLayer.name === 'Peta Wilayah RW') { mymap.removeControl(legenda_rw); }
                        if (eventLayer.name === 'Peta Wilayah RT') { mymap.removeControl(legenda_rt); }
                    });

                    // Menampilkan OverLayer Area, Garis, Lokasi plus Lokasi Pembangunan
                    let layerCustom = tampilkan_layer_area_garis_lokasi_plus(
                        mymap,
                        all_area,
                        all_garis,
                        all_lokasi,
                        all_lokasi_pembangunan,
                        LOKASI_SIMBOL_LOKASI,
                        favico_desa,
                        LOKASI_FOTO_AREA,
                        LOKASI_FOTO_GARIS,
                        LOKASI_FOTO_LOKASI,
                        LOKASI_GALERI,
                        info_pembangunan,
                        all_persil,
                        TAMPIL_LUAS.toString()
                    );

                    L.control.layers(baseLayers, overlayLayers, {
                        position: 'topleft',
                        collapsed: true
                    }).addTo(mymap);
                    L.control.groupedLayers('', layerCustom, {
                        groupCheckboxes: true,
                        position: 'topleft',
                        collapsed: true
                    }).addTo(mymap);
                    let labelCheckbox
                    $('input[type=checkbox]').each(function() {
                        labelCheckbox = $(this).next().text().trim()
                        if (WILAYAH_INFRASTRUKTUR.includes(labelCheckbox)) {
                            $(this).click();
                        }
                        if (labelCheckbox == 'Letter C-Desa') {
                            if (data.tampilkan_cdesa != 1) {
                                $(this).parent().remove()
                            }
                        }
                    });

                    // ===== FIX: Ganti marker kantor desa (favicon) dengan ikon FA =====
                    mymap.eachLayer(function(layer) {
                        if (layer.feature && layer.feature.properties && layer.feature.properties.name === 'kantor_desa') {
                            var latlng = layer.getLatLng();
                            var faIcon = L.divIcon({
                                html: '<div class="kantor-desa-marker"><i class="fa fa-university"></i></div>',
                                className: 'kantor-desa-divicon',
                                iconSize: [40, 40],
                                iconAnchor: [20, 40],
                                popupAnchor: [0, -36]
                            });
                            layer.setIcon(faIcon);
                            // Ganti popup content jadi lebih informatif
                            layer.unbindPopup();
                            layer.bindPopup(
                                '<div style="text-align:center;padding:6px 0;">' +
                                '<i class="fa fa-university" style="font-size:1.6rem;color:#2F855A;margin-bottom:6px;display:block;"></i>' +
                                '<strong style="font-size:.92rem;color:#1a202c;">Kantor {{ ucwords(setting("sebutan_desa")) }}</strong><br>' +
                                '<span style="font-size:.8rem;color:#718096;">{{ ucwords(identitas("nama_desa")) }}</span>' +
                                '</div>',
                                { className: 'kantor_desa', maxWidth: 220 }
                            );
                            // Hapus tooltip dari marker kantor desa (cegah double)
                            layer.unbindTooltip();
                        }
                    });

                    // ===== FIX: Hilangkan tooltip saat popup terbuka =====
                    mymap.on('popupopen', function() {
                        $('.leaflet-tooltip-pane').css('opacity', '0');
                    });
                    mymap.on('popupclose', function() {
                        $('.leaflet-tooltip-pane').css('opacity', '1');
                    });

                    // ===== Coordinates Bar — tampilkan posisi kursor & zoom =====
                    var coordLat = document.getElementById('coordLat');
                    var coordLng = document.getElementById('coordLng');
                    var coordZoom = document.getElementById('coordZoom');
                    if (coordZoom) coordZoom.textContent = mymap.getZoom();
                    mymap.on('mousemove', function(e) {
                        if (coordLat) coordLat.textContent = e.latlng.lat.toFixed(6);
                        if (coordLng) coordLng.textContent = e.latlng.lng.toFixed(6);
                    });
                    mymap.on('zoomend', function() {
                        if (coordZoom) coordZoom.textContent = mymap.getZoom();
                    });

                    // ===== Hilangkan loading overlay =====
                    var loader = document.getElementById('petaLoading');
                    if (loader) {
                        loader.style.opacity = '0';
                        setTimeout(function(){ loader.style.display = 'none'; }, 600);
                    }

                    // ===== Fullscreen toggle dari navbar =====
                    var wrapper = document.querySelector('.peta-wrapper');
                    var btnFs = document.getElementById('btnFullscreen');
                    var btnExitFs = document.getElementById('btnExitFs');
                    var fsIcon = btnFs ? btnFs.querySelector('i') : null;
                    var fsText = btnFs ? btnFs.querySelector('span') : null;

                    function enterFullscreen() {
                        wrapper.classList.add('fullscreen-mode');
                        if (fsIcon) { fsIcon.className = 'fa fa-compress'; }
                        if (fsText) { fsText.textContent = 'Keluar'; }
                        btnFs.classList.add('active');
                        mymap.invalidateSize();
                    }
                    function exitFullscreen() {
                        wrapper.classList.remove('fullscreen-mode');
                        if (fsIcon) { fsIcon.className = 'fa fa-expand'; }
                        if (fsText) { fsText.textContent = 'Layar Penuh'; }
                        btnFs.classList.remove('active');
                        mymap.invalidateSize();
                    }

                    if (btnFs) {
                        btnFs.addEventListener('click', function() {
                            if (wrapper.classList.contains('fullscreen-mode')) {
                                exitFullscreen();
                            } else {
                                enterFullscreen();
                            }
                        });
                    }
                    if (btnExitFs) {
                        btnExitFs.addEventListener('click', function() {
                            exitFullscreen();
                        });
                    }
                    // ESC key keluar fullscreen
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape' && wrapper.classList.contains('fullscreen-mode')) {
                            exitFullscreen();
                        }
                    });
                }

            }; //EOF window.onload

        })();

        // ===== FIX: Custom collapse toggle (tanpa Bootstrap data-toggle="collapse") =====
        document.addEventListener("click", function(e) {
            var btn = e.target.closest("a[data-target]");
            if (!btn) return;

            var targetSelector = btn.getAttribute("data-target");
            if (!targetSelector || !targetSelector.startsWith("#collapseStat")) return;

            e.preventDefault();
            e.stopPropagation();

            var popup = btn.closest(".leaflet-popup-content");
            if (!popup) return;

            var target = popup.querySelector(targetSelector);
            if (!target) return;

            var isOpen = target.style.display === "block";

            // Tutup semua collapse dalam popup ini
            popup.querySelectorAll('[id^="collapseStat"]').forEach(function(el) {
                el.style.display = "none";
            });

            // Toggle yang diklik
            if (!isOpen) {
                target.style.display = "block";
            }
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
    <script src="{{ asset('js/Leaflet.fullscreen.min.js') }}"></script>
    <script src="{{ asset('js/turf.min.js') }}"></script>
    <script src="{{ asset('js/leaflet-providers.js') }}"></script>
    <script src="{{ asset('js/L.Control.Locate.min.js') }}"></script>
    <script src="{{ asset('js/leaflet-measure-path.js') }}"></script>
    <script src="{{ asset('js/leaflet.markercluster.js') }}"></script>
    <script src="{{ asset('js/leaflet.groupedlayercontrol.min.js') }}"></script>
    <script src="{{ asset('js/leaflet.browser.print.js') }}"></script>
    <script src="{{ asset('js/leaflet.browser.print.utils.js') }}"></script>
    <script src="{{ asset('js/leaflet.browser.print.sizes.js') }}"></script>
    <script src="{{ asset('js/dom-to-image.min.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
@endpush
