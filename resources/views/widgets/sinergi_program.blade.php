<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

{{-- Widget Sinergi Program — Elegant (boja) --}}
@php $sinergi = sinergi_program(); @endphp

@if (count($sinergi) > 0)
<div class="wsinergi-box">
    {{-- Header gradient (konsisten dengan wcat & warsip) --}}
    <div class="wsinergi-header">
        <div class="wsinergi-header-icon">
            <i class="fas fa-link"></i>
        </div>
        <div class="wsinergi-header-text">
            <h3>{{ $judul_widget }}</h3>
            <p>Tautan mitra &amp; instansi terkait</p>
        </div>
    </div>

    {{-- Body — Simple list --}}
    <div class="wsinergi-body">
        <div class="wsinergi-list">
            @foreach ($sinergi as $i => $item)
                @php
                    $hasImage = !empty(trim($item['gambar_url'])) && !str_contains($item['gambar_url'], '404-image-not-found');
                @endphp
                <a href="{{ $item['tautan'] }}" target="_blank" rel="noopener noreferrer" class="wsinergi-list-item" title="{{ $item['judul'] }}">
                    <span class="wsinergi-list-logo">
                        @if ($hasImage)
                            <img src="{{ $item['gambar_url'] }}" alt="{{ $item['judul'] }}" loading="lazy"
                                 onerror="this.onerror=null;this.parentNode.innerHTML='<i class=\'fas fa-link wsinergi-fa-icon\'></i>'">
                        @else
                            <i class="fas fa-link wsinergi-fa-icon"></i>
                        @endif
                    </span>
                    <span class="wsinergi-list-name">{{ $item['judul'] }}</span>
                    <span class="wsinergi-list-ext"><i class="fas fa-external-link-alt"></i></span>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif
