@if ($headline)
    @php
        $abstrak_headline = potong_teks(strip_tags($headline['isi']), 200);
        $image = $headline['gambar'] && is_file(LOKASI_FOTO_ARTIKEL . 'sedang_' . $headline['gambar']) ? AmbilFotoArtikel($headline['gambar'], 'sedang') : theme_asset('images/artikel-pilihan.jpg');
    @endphp
    <a href="{{ $headline->url_slug }}" class="headline-hero">
        <div class="headline-hero-img">
            <img src="{{ $image }}" alt="{{ $headline['judul'] }}" loading="eager">
        </div>
        <div class="headline-hero-overlay"></div>
        <div class="headline-hero-content">
            <span class="headline-hero-badge"><i class="fas fa-star"></i> Artikel Pilihan</span>
            <h2 class="headline-hero-title">{{ $headline['judul'] }}</h2>
            <p class="headline-hero-excerpt">{!! $abstrak_headline !!}</p>
            <div class="headline-hero-meta">
                <span><i class="fas fa-calendar-alt"></i> {{ tgl_indo($headline['tgl_upload']) }}</span>
                <span><i class="fas fa-eye"></i> {{ hit($headline['hit']) }} kali dibaca</span>
                @if (!empty($headline['category']['kategori']))
                    <span><i class="fas fa-folder"></i> {{ $headline['category']['kategori'] }}</span>
                @endif
            </div>
            <span class="headline-hero-btn">
                Baca Selengkapnya <i class="fas fa-arrow-right"></i>
            </span>
        </div>
    </a>
@endif
