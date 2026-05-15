@php
    // Gunakan hero.jpg bawaan tema jika admin belum mengatur gambar latar sendiri
    // $latar_website adalah full URL — cek apakah masih URL default sistem
    $bg_header = (! empty($latar_website) && strpos($latar_website, 'assets/front/css/images/latar_website.jpg') === false)
        ? $latar_website
        : theme_asset('images/hero.jpg');
    $isHome = empty(request()->segment(1)) || request()->segment(1) === 'first';
@endphp

@if ($isHome)
{{-- Hero Section — Boja: Clean & Bold --}}
<section class="hero-boja" style="background-image: url({{ $bg_header }});">
    <div class="hero-overlay"></div>
    <div class="hero-inner container">
        <div class="hero-text">
            <div class="hero-badge">
                <img src="{{ gambar_desa($desa['logo']) }}" alt="{{ $desa['nama_desa'] }}" class="hero-badge-logo">
                <span>Website Resmi</span>
            </div>
            <h1 class="hero-title">{{ ucfirst(setting('sebutan_desa')) }} {{ $desa['nama_desa'] }}</h1>
            <p class="hero-location">
                <i class="fas fa-map-marker-alt"></i>
                {{ ucfirst(setting('sebutan_kecamatan_singkat')) }} {{ ucwords($desa['nama_kecamatan']) }},
                {{ ucfirst(setting('sebutan_kabupaten_singkat')) }} {{ ucwords($desa['nama_kabupaten']) }}@if ($desa['provinsi']),
                {{ ucwords($desa['provinsi']) }}@endif
            </p>
        </div>

        {{-- Bottom Bar: Login + Menu Desa + Layanan Mandiri --}}
        @php
            $heroMenuItems = [
                ['url' => 'pemerintah', 'label' => 'Identitas', 'fa' => 'fas fa-landmark'],
                ['url' => 'struktur-organisasi-dan-tata-kerja', 'label' => 'Aparatur', 'fa' => 'fas fa-user-tie'],
                ['url' => 'data-wilayah', 'label' => 'Statistik', 'fa' => 'fas fa-chart-pie'],
                ['url' => 'arsip', 'label' => 'Arsip', 'fa' => 'fas fa-archive'],
                ['url' => 'galeri', 'label' => 'Galeri', 'fa' => 'fas fa-images'],
                ['url' => 'pengaduan', 'label' => 'Pengaduan', 'fa' => 'fas fa-bullhorn'],
                ['url' => 'peta', 'label' => 'Peta', 'fa' => 'fas fa-map-marked-alt'],
                ['url' => 'data-kesehatan/stunting', 'label' => 'Stunting', 'fa' => 'fas fa-heartbeat'],
                ['url' => 'lapak', 'label' => 'Lapak', 'fa' => 'fas fa-store'],
            ];
        @endphp
        <div class="hero-bottom-bar">
            <a href="{{ site_url('siteman') }}" class="hero-btn hero-btn-primary">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <div class="hero-menu-row">
                @foreach ($heroMenuItems as $mi)
                    <a href="{{ site_url($mi['url']) }}" class="hero-menu-item" title="{{ $mi['label'] }}">
                        <div class="hero-menu-icon"><i class="{{ $mi['fa'] }}"></i></div>
                        <span>{{ $mi['label'] }}</span>
                    </a>
                @endforeach
            </div>
            @if (setting('layanan_mandiri') == 1)
            <a href="{{ site_url('layanan-mandiri') }}" class="hero-btn hero-btn-outline">
                <i class="fas fa-user"></i> Layanan Mandiri
            </a>
            @endif
        </div>

        {{-- Search Bar — Transparent, di bawah menu --}}
        <form action="{{ site_url('/') }}" role="search" class="hero-search-bar">
            <input type="text" name="cari" placeholder="Cari artikel, informasi, atau layanan desa...">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
</section>
@endif

{{-- Ticker — Full Width Info Berjalan --}}
@if ($teks_berjalan)
    <div class="ticker-bar ticker-fullwidth">
        <div class="ticker-inner-full">
            <span class="ticker-label"><i class="fas fa-bullhorn"></i> Info</span>
            <div class="ticker-track-wrap">
                <div class="ticker-track">
                    @foreach ($teks_berjalan as $marquee)
                        <span class="ticker-track-item">
                            {{ $marquee['teks'] }}
                            @if (trim($marquee['tautan']) && $marquee['judul_tautan'])
                                <a href="{{ $marquee['tautan'] }}">{{ $marquee['judul_tautan'] }}</a>
                            @endif
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
