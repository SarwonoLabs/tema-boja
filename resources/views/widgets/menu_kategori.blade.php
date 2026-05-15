<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

{{-- Widget Menu Kategori — Elegant (boja) --}}
<div class="wcat-box">
    {{-- Header gradient (sama seperti kehadiran-panel-header) --}}
    <div class="wcat-header">
        <div class="wcat-header-icon">
            <i class="fas fa-layer-group"></i>
        </div>
        <div class="wcat-header-text">
            <h3>{{ $judul_widget }}</h3>
            <p>Telusuri artikel berdasarkan topik</p>
        </div>
    </div>

    {{-- Body list --}}
    <div class="wcat-body">
        @if (count($menu_kiri ?? []) > 0)
            <ul class="wcat-list">
                @foreach ($menu_kiri as $i => $data)
                    <li>
                        <a href="{{ site_url('artikel/kategori/' . $data['slug']) }}" class="wcat-item" style="animation-delay:{{ $i * 0.05 }}s">
                            <span class="wcat-icon" aria-hidden="true">
                                <i class="fas fa-folder"></i>
                            </span>
                            <span class="wcat-info">
                                <span class="wcat-name">{{ $data['kategori'] }}</span>
                                @if (!empty($data['jumlah']))
                                    <span class="wcat-count">{{ $data['jumlah'] }} artikel</span>
                                @endif
                            </span>
                            @if (!empty($data['jumlah']))
                                <span class="wcat-badge">{{ $data['jumlah'] }}</span>
                            @endif
                            <span class="wcat-arrow"><i class="fas fa-chevron-right"></i></span>
                        </a>

                        @if (count($data['submenu'] ?? []) > 0)
                            <ul class="wcat-sublist">
                                @foreach ($data['submenu'] as $sub)
                                    <li>
                                        <a href="{{ site_url('artikel/kategori/' . $sub['slug']) }}" class="wcat-subitem">
                                            <i class="fas fa-file-alt wcat-sub-icon"></i>
                                            <span>{{ $sub['kategori'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <div class="wcat-empty">
                <i class="fas fa-inbox"></i>
                <p>Belum ada kategori</p>
            </div>
        @endif
    </div>
</div>
