<div class="err404-wrap">
    {{-- Decorative floating shapes --}}
    <div class="err404-shapes">
        <span class="err404-shape err404-shape-1"></span>
        <span class="err404-shape err404-shape-2"></span>
        <span class="err404-shape err404-shape-3"></span>
        <span class="err404-shape err404-shape-4"></span>
        <span class="err404-shape err404-shape-5"></span>
    </div>

    {{-- Main content --}}
    <div class="err404-content">
        {{-- Animated 404 number --}}
        <div class="err404-number-wrap">
            <span class="err404-digit err404-digit-1">4</span>
            <span class="err404-digit-icon">
                <span class="err404-compass">
                    <i class="fas fa-compass"></i>
                </span>
            </span>
            <span class="err404-digit err404-digit-2">4</span>
        </div>

        {{-- Subtitle with decorative lines --}}
        <div class="err404-subtitle">
            <span class="err404-line"></span>
            <span class="err404-subtitle-text">Halaman Tidak Ditemukan</span>
            <span class="err404-line"></span>
        </div>

        {{-- Description --}}
        <p class="err404-desc">
            Maaf, halaman yang Anda cari tidak tersedia, mungkin telah dipindahkan, 
            dihapus, atau alamat yang Anda ketik salah.
        </p>

        {{-- Action buttons --}}
        <div class="err404-actions">
            <a href="{{ site_url('/') }}" class="err404-btn err404-btn-primary">
                <i class="fas fa-home"></i>
                <span>Kembali ke Beranda</span>
            </a>
            <button onclick="history.back()" class="err404-btn err404-btn-outline">
                <i class="fas fa-arrow-left"></i>
                <span>Halaman Sebelumnya</span>
            </button>
        </div>

        {{-- Helpful suggestion --}}
        <div class="err404-hint">
            <i class="fas fa-lightbulb"></i>
            <span>Gunakan menu navigasi di atas untuk menemukan halaman yang Anda cari.</span>
        </div>
    </div>
</div>
