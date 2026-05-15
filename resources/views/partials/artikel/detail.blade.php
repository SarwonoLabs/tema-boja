@extends('theme::layouts.' . $layout)
@php
    $post = $single_artikel;
    $alt_slug = PREMIUM ? 'artikel' : 'first';
    $isStatis = ($post['tipe'] ?? '') === 'statis';
@endphp
@include('theme::commons.asset_highcharts')
@section('content')
    @if ($post)
        <nav class="breadcrumb-boja" aria-label="breadcrumb">
            <ol>
                <li><a href="{{ ci_route() }}"><i class="fas fa-home"></i> Beranda</a></li>
                @if ($isStatis)
                    <li>{{ $post['judul'] }}</li>
                @else
                    <li>
                        @if ($post['kategori'])
                            <a href="{{ ci_route("{$alt_slug}.kategori.{$post['kat_slug']}") }}">{{ $post['kategori'] }}</a>
                        @else
                            Artikel
                        @endif
                    </li>
                @endif
            </ol>
        </nav>

        @if ($isStatis)
            {{-- Static page: elegant boja-page-header --}}
            <div class="boja-page-header">
                <div class="boja-page-header-text">
                    <h1 class="boja-page-title">
                        @php
                            $slug = $post['slug'] ?? '';
                            $icon = 'fas fa-file-alt';
                            if (str_contains($slug, 'visi')) $icon = 'fas fa-eye';
                            elseif (str_contains($slug, 'misi')) $icon = 'fas fa-bullseye';
                            elseif (str_contains($slug, 'sejarah')) $icon = 'fas fa-landmark';
                            elseif (str_contains($slug, 'profil') || str_contains($slug, 'sambutan')) $icon = 'fas fa-address-card';
                            elseif (str_contains($slug, 'wilayah')) $icon = 'fas fa-map-marked-alt';
                        @endphp
                        <i class="{{ $icon }}"></i> {{ $post['judul'] }}
                    </h1>
                    <p class="boja-page-subtitle">{{ ucwords(strtolower(setting('sebutan_desa') . ' ' . ($desa['nama_desa'] ?? ''))) }}</p>
                </div>
            </div>

            <div class="statis-content-card">
                @if ($post['gambar'] && is_file(LOKASI_FOTO_ARTIKEL . 'sedang_' . $post['gambar']))
                    <a href="{{ AmbilFotoArtikel($post['gambar'], 'sedang') }}" data-fancybox="images" class="statis-featured-image">
                        <img src="{{ AmbilFotoArtikel($post['gambar'], 'sedang') }}" alt="{{ $post['judul'] }}">
                    </a>
                @endif
                <div class="content statis-body">
                    {!! $post['isi'] !!}
                </div>

                @for ($i = 1; $i <= 3; $i++)
                    @if ($post['gambar' . $i] && is_file(LOKASI_FOTO_ARTIKEL . 'sedang_' . $post['gambar' . $i]))
                        <a href="{{ AmbilFotoArtikel($post['gambar' . $i], 'sedang') }}" data-fancybox="images" style="display:block;margin-bottom:1rem">
                            <img src="{{ AmbilFotoArtikel($post['gambar' . $i], 'sedang') }}" alt="{{ $post['judul'] }}" style="width:100%;border-radius:10px">
                        </a>
                    @endif
                @endfor

                @if ($post['dokumen'])
                    <div class="statis-dokumen">
                        <i class="fas fa-paperclip"></i>
                        <div>
                            <strong>Dokumen Lampiran</strong>
                            <a href="{{ ci_route('first.unduh_dokumen_artikel', $post['id']) }}">
                                <i class="fas fa-download"></i> {{ $post['dokumen'] }}
                            </a>
                        </div>
                    </div>
                @endif

                <div class="statis-meta-footer">
                    <span><i class="fas fa-user"></i> {{ $post['owner'] }}</span>
                    <span><i class="fas fa-calendar-alt"></i> {{ $post['tgl_upload_local'] }}</span>
                    <span><i class="fas fa-eye"></i> Dibaca {{ hit($post['hit']) }}</span>
                </div>
            </div>

            @include('theme::commons.share')
        @else
            {{-- Regular article: existing layout --}}
            <article>
                <h1 class="article-detail-title">{{ $post['judul'] }}</h1>
                <div class="article-detail-meta">
                    <span><i class="fas fa-user"></i> {{ $post['owner'] }}</span>
                    <span><i class="fas fa-calendar-alt"></i> {{ $post['tgl_upload_local'] }}</span>
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

            @include('theme::commons.share')
            @include('theme::partials.artikel.comment')
        @endif
    @else
        @include('theme::commons._404_content')
    @endif
@endsection
