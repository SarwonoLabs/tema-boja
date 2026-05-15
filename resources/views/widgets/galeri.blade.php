{{-- Widget Galeri — Elegant Carousel (boja) --}}
<div class="wgaleri-box">
    {{-- Header gradient --}}
    <div class="wgaleri-header">
        <div class="wgaleri-header-icon"><i class="fas fa-camera-retro"></i></div>
        <div class="wgaleri-header-text">
            <h3>{{ $judul_widget }}</h3>
            <p>Dokumentasi kegiatan {{ setting('sebutan_desa') }}</p>
        </div>
        <a href="{{ site_url('galeri') }}" class="wgaleri-header-link" title="Lihat Semua Galeri">
            <i class="fas fa-external-link-alt"></i>
        </a>
    </div>

    {{-- Body — Carousel --}}
    <div class="wgaleri-body">
        @php
            $galeriValid = collect($w_gal)->filter(fn($d) => is_file(LOKASI_GALERI . 'sedang_' . $d['gambar']));
        @endphp

        @if ($galeriValid->count() > 0)
            <div class="wgaleri-carousel owl-carousel owl-theme">
                @foreach ($galeriValid as $data)
                    <a href="{{ route('web.galeri.detail', $data['id']) }}" class="wgaleri-slide" title="Album : {{ $data['nama'] }}">
                        <img src="{{ AmbilGaleri($data['gambar'], 'sedang') }}" alt="Album : {{ $data['nama'] }}" loading="lazy">
                        <div class="wgaleri-slide-overlay">
                            <span class="wgaleri-slide-title"><i class="fas fa-images"></i> {{ $data['nama'] }}</span>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Dots & counter --}}
            <div class="wgaleri-footer">
                <span class="wgaleri-count"><i class="fas fa-layer-group"></i> {{ $galeriValid->count() }} Album</span>
                <a href="{{ site_url('galeri') }}" class="wgaleri-viewall">Lihat Semua <i class="fas fa-arrow-right"></i></a>
            </div>
        @else
            {{-- Empty state --}}
            <div class="wgaleri-empty">
                <img src="{{ theme_asset('images/no-galery.png') }}" alt="Belum ada galeri" class="wgaleri-empty-img">
                <p>Belum ada dokumentasi galeri</p>
            </div>
        @endif
    </div>
</div>
