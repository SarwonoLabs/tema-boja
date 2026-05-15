@php
    $post = $single_artikel;
    $alt_slug = PREMIUM ? 'artikel' : 'first';
@endphp

<nav class="breadcrumb-boja" aria-label="breadcrumb">
    <ol>
        <li><a href="{{ ci_route() }}"><i class="fas fa-home"></i> Beranda</a></li>
        <li>{!! $post['kategori'] ? '<a href="' . ci_route("{$alt_slug}.kategori.{$post['kat_slug']}") . '">' . $post['kategori'] . '</a>' : 'Artikel' !!}</li>
    </ol>
</nav>

<article>
    <h1 class="article-detail-title">{{ $post['judul'] }}</h1>
    <div class="article-detail-meta">
        <span><i class="fas fa-user"></i> {{ $post['owner'] }}</span>
        <span><i class="fas fa-calendar-alt"></i> {{ tgl_indo($post['tgl_upload']) }}</span>
        <span><i class="fas fa-eye"></i> Dibaca {{ hit($post['hit']) }}</span>
    </div>
</article>

<div class="content">
    @if ($post['gambar'] && is_file(LOKASI_FOTO_ARTIKEL . 'sedang_' . $post['gambar']))
        <a href="{{ AmbilFotoArtikel($post['gambar'], 'sedang') }}" data-fancybox="images" class="article-featured-image">
            <img src="{{ AmbilFotoArtikel($post['gambar'], 'sedang') }}" alt="{{ $post['judul'] }}">
        </a>
    @endif
    {!! $post['isi'] !!}
</div>

@for ($i = 1; $i <= 3; $i++)
    @if ($post['gambar' . $i] && is_file(LOKASI_FOTO_ARTIKEL . 'sedang_' . $post['gambar' . $i]))
        <a href="{{ AmbilFotoArtikel($post['gambar' . $i], 'sedang') }}" data-fancybox="images" style="display: block; margin-bottom: 1rem;">
            <img src="{{ AmbilFotoArtikel($post['gambar' . $i], 'sedang') }}" alt="{{ $post['nama'] }}" style="width: 100%;">
        </a>
    @endif
@endfor

@if ($post['dokumen'])
    <div class="alert alert-info">
        <h4><i class="fas fa-paperclip"></i> Dokumen Lampiran</h4>
        <a href="{{ ci_route('first.unduh_dokumen_artikel', $post['id']) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;">
            <i class="fas fa-download"></i> {{ $post['dokumen'] }}
        </a>
    </div>
@endif
