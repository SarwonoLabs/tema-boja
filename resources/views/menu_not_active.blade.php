@extends('theme::layouts.full-content')

@section('title', 'Menu Tidak Aktif')

@section('content')
<div class="err404-wrap">
    <div class="err404-shapes">
        <span class="err404-shape err404-shape-1"></span>
        <span class="err404-shape err404-shape-2"></span>
        <span class="err404-shape err404-shape-3"></span>
        <span class="err404-shape err404-shape-4"></span>
        <span class="err404-shape err404-shape-5"></span>
    </div>

    <div class="err404-content">
        <div class="err404-number-wrap">
            <span class="err404-digit-icon" style="width:80px;height:80px;">
                <span style="color:#fff;font-size:2.2rem;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-ban"></i>
                </span>
            </span>
        </div>

        <div class="err404-subtitle">
            <span class="err404-line"></span>
            <span class="err404-subtitle-text">Menu Tidak Aktif</span>
            <span class="err404-line"></span>
        </div>

        <p class="err404-desc">
            Menu yang Anda cari belum tersedia atau sedang tidak aktif. 
            Silakan kembali ke halaman utama atau coba menu lainnya.
        </p>

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

        <div class="err404-hint">
            <i class="fas fa-lightbulb"></i>
            <span>Gunakan menu navigasi di atas untuk menemukan halaman yang tersedia.</span>
        </div>
    </div>
</div>
@endsection
