@php
    $url = $post->url_slug;
    $abstract = potong_teks(strip_tags($post['isi']), 120);
    $hasImage = $post['gambar'] && is_file(LOKASI_FOTO_ARTIKEL . 'sedang_' . $post['gambar']);
    $image = $hasImage ? AmbilFotoArtikel($post['gambar'], 'sedang') : null;
@endphp

<article class="article-list-item">
    <a href="{{ $url }}" class="article-list-thumb">
        @if ($hasImage)
            <img src="{{ $image }}" alt="{{ $post['judul'] }}" loading="lazy">
        @else
            <div class="article-list-thumb-placeholder">
                <i class="fas fa-image"></i>
            </div>
        @endif
    </a>
    <div class="article-list-body">
        <div class="article-list-meta">
            @if ($post['kategori'])
                <span class="article-list-cat">{{ $post['category']['kategori'] }}</span>
            @endif
            <span><i class="fas fa-calendar-alt"></i> {{ tgl_indo($post['tgl_upload']) }}</span>
            <span><i class="fas fa-eye"></i> {{ hit($post['hit']) }}</span>
        </div>
        <a href="{{ $url }}" class="article-list-title">{{ potong_teks($post['judul'], 80) }}</a>
        <p class="article-list-excerpt">{{ $abstract }}</p>
    </div>
</article>
