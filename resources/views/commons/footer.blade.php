@php
    $social_media = [
        'facebook' => ['icon' => 'fa-facebook-f', 'link' => null],
        'twitter' => ['icon' => 'fa-twitter', 'link' => null],
        'instagram' => ['icon' => 'fa-instagram', 'link' => null],
        'telegram' => ['icon' => 'fa-telegram', 'link' => null],
        'whatsapp' => ['icon' => 'fa-whatsapp', 'link' => null],
        'youtube' => ['icon' => 'fa-youtube', 'link' => null],
    ];
@endphp

@foreach ($sosmed as $social)
    @if ($social['link'])
        @php
            $social_media[strtolower($social['nama'])]['link'] = $social['link'];
        @endphp
    @endif
@endforeach

{{-- APBDes di atas footer --}}
@if ($transparansi)
<div class="container" style="max-width:1200px;margin:0 auto">
    @include('theme::partials.apbdesa', $transparansi)
</div>
@endif

<footer class="footer-boja">
    {{-- Wave separator --}}
    <div class="footer-wave">
        <svg viewBox="0 0 1440 70" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 70L48 60C96 50 192 30 288 22C384 14 480 18 576 28C672 38 768 54 864 54C960 54 1056 38 1152 28C1248 18 1344 12 1392 10L1440 8V70H0Z" fill="#1E2D4E"/>
        </svg>
    </div>

    <div class="footer-main">
        <div class="container">
            {{-- Brand & Tagline --}}
            <div class="footer-brand-row">
                <div class="footer-brand-left">
                    <div class="footer-logo-wrap">
                        <img src="{{ gambar_desa($desa['logo']) }}" alt="{{ $nama_desa }}" class="footer-logo-img">
                    </div>
                    <div>
                        <h3 class="footer-desa-name">{{ $desa['nama_desa'] }}</h3>
                        <p class="footer-desa-loc">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ ucfirst(setting('sebutan_kecamatan_singkat')) }} {{ ucwords($desa['nama_kecamatan']) }}, {{ ucfirst(setting('sebutan_kabupaten_singkat')) }} {{ ucwords($desa['nama_kabupaten']) }}
                        </p>
                        {{-- Social media --}}
                        <div class="footer-social-row">
                            @foreach ($social_media as $key => $sm)
                                @if ($sm['link'])
                                    <a href="{{ $sm['link'] }}" target="_blank" rel="noopener" title="{{ ucfirst($key) }}" class="footer-soc footer-soc-{{ $key }}">
                                        <i class="fab {{ $sm['icon'] }}"></i>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-divider"></div>

            {{-- Columns --}}
            <div class="footer-columns">
                {{-- Kontak --}}
                <div class="footer-col">
                    <h4 class="footer-col-title"><i class="fas fa-map-pin"></i> Kontak</h4>
                    <ul class="footer-info-list">
                        @if ($desa['alamat_kantor'])
                            <li><i class="fas fa-map-marker-alt"></i> <span>{{ $desa['alamat_kantor'] }}</span></li>
                        @endif
                        @if ($desa['email_desa'])
                            <li><i class="fas fa-envelope"></i> <span>{{ $desa['email_desa'] }}</span></li>
                        @endif
                        @if ($desa['telepon'])
                            <li><i class="fas fa-phone-alt"></i> <span>{{ $desa['telepon'] }}</span></li>
                        @endif
                    </ul>
                </div>

                {{-- Navigasi --}}
                <div class="footer-col">
                    <h4 class="footer-col-title"><i class="fas fa-compass"></i> Navigasi</h4>
                    <ul class="footer-nav-list">
                        <li><a href="{{ site_url('/') }}">Beranda</a></li>
                        @if (menu_tema())
                            @foreach (array_slice(menu_tema(), 0, 6) as $menu)
                                <li><a href="{{ $menu['link_url'] }}">{!! $menu['nama'] !!}</a></li>
                            @endforeach
                        @endif
                    </ul>
                </div>

                {{-- Pintasan --}}
                <div class="footer-col">
                    <h4 class="footer-col-title"><i class="fas fa-link"></i> Pintasan</h4>
                    <ul class="footer-nav-list">
                        <li><a href="{{ site_url('arsip') }}">Arsip Artikel</a></li>
                        <li><a href="{{ site_url('peta') }}">Peta {{ ucfirst(setting('sebutan_desa')) }}</a></li>
                        <li><a href="{{ site_url('data-statistik') }}">Data Statistik</a></li>
                        <li><a href="{{ site_url('galeri') }}">Galeri</a></li>
                        <li><a href="{{ site_url('data-wilayah') }}">Data Wilayah</a></li>
                    </ul>
                </div>
            </div>

            @if (setting('tte'))
                <div class="footer-bsre-row">
                    <img src="{{ asset('assets/images/bsre.png?v', false) }}" alt="BSrE">
                </div>
            @endif
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="footer-bottom-bar">
        <div class="container">
            <div class="footer-bottom-inner">
                <p>&copy; {{ date('Y') }} <strong>{{ $nama_desa }}</strong>. Hak cipta dilindungi.</p>
                <p class="footer-bottom-right">
                    <span class="footer-powered-badge">
                        <i class="fas fa-code"></i>
                        {{ $themeName }} {{ $themeVersion }} &middot;
                        <a href="https://opensid.my.id" target="_blank" rel="noopener">OpenSID {{ ambilVersi() }}</a>
                        @if (file_exists('mitra'))
                            &middot; Hosting <a href="https://my.idcloudhost.com/aff.php?aff=3172" rel="noopener noreferrer" target="_blank">
                                <img src="{{ base_url('/assets/images/Logo-IDcloudhost.png') }}" alt="IDCloudHost" class="footer-idch-logo">
                            </a>
                        @endif
                    </span>
                </p>
            </div>
        </div>
    </div>
</footer>