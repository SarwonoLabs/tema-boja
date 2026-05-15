<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

{{-- Widget Arsip Artikel — Elegant (boja) --}}
<div class="warsip-box">
    {{-- Header gradient (konsisten dengan wcat & kehadiran) --}}
    <div class="warsip-header">
        <div class="warsip-header-icon">
            <i class="fas fa-newspaper"></i>
        </div>
        <div class="warsip-header-text">
            <h3>{{ $judul_widget }}</h3>
            <p>Artikel populer &amp; terkini</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="warsip-tabs">
        <button class="warsip-tab is-active" data-target="warsip-terkini">
            <i class="fas fa-clock"></i> Terbaru
        </button>
        <button class="warsip-tab" data-target="warsip-populer">
            <i class="fas fa-fire"></i> Populer
        </button>
        <button class="warsip-tab" data-target="warsip-acak">
            <i class="fas fa-random"></i> Acak
        </button>
    </div>

    {{-- Content panels --}}
    <div class="warsip-body">
        @foreach (['terkini' => 'arsip_terkini', 'populer' => 'arsip_populer', 'acak' => 'arsip_acak'] as $jenis => $jenis_arsip)
            <div class="warsip-panel {{ $jenis === 'terkini' ? 'is-active' : '' }}" id="warsip-{{ $jenis }}">
                @if (count($$jenis_arsip ?? []) > 0)
                    <ul class="warsip-list">
                        @foreach ($$jenis_arsip as $idx => $arsip)
                            <li>
                                <a href="{{ site_url('artikel/' . buat_slug($arsip)) }}" class="warsip-item" style="animation-delay:{{ $idx * 0.05 }}s">
                                    {{-- Thumbnail --}}
                                    <div class="warsip-thumb">
                                        @if (is_file(LOKASI_FOTO_ARTIKEL . "kecil_$arsip[gambar]"))
                                            <img src="{{ base_url(LOKASI_FOTO_ARTIKEL . "kecil_$arsip[gambar]") }}" alt="{{ $arsip['judul'] }}" loading="lazy">
                                        @else
                                            <div class="warsip-thumb-placeholder">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                    </div>
                                    {{-- Info --}}
                                    <div class="warsip-info">
                                        @if ($jenis === 'populer' && !empty($arsip['hit']))
                                            <span class="warsip-hit">
                                                <i class="fas fa-eye"></i> {{ number_format($arsip['hit']) }} Kali
                                            </span>
                                        @else
                                            <span class="warsip-date">
                                                <i class="fas fa-calendar-alt"></i> {{ tgl_indo($arsip['tgl_upload']) }}
                                            </span>
                                        @endif
                                        <span class="warsip-title">{{ $arsip['judul'] }}</span>
                                    </div>
                                    {{-- Arrow --}}
                                    <span class="warsip-arrow"><i class="fas fa-chevron-right"></i></span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="warsip-empty">
                        <i class="fas fa-inbox"></i>
                        <p>Belum ada artikel</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

{{-- Tab switching script (jQuery) --}}
<script>
jQuery(function($){
    $('.warsip-tab').on('click', function(){
        var target = $(this).data('target');
        // Toggle active tab
        $(this).addClass('is-active').siblings().removeClass('is-active');
        // Toggle active panel
        var box = $(this).closest('.warsip-box');
        box.find('.warsip-panel').removeClass('is-active');
        box.find('#' + target).addClass('is-active');
    });
});
</script>
