{{-- Agenda Card — reusable partial --}}
@php
    $tglAgenda = $agenda['tgl_agenda'] ?? '';
    $tglParts = $tglAgenda ? explode(' ', tgl_indo2($tglAgenda)) : [];
    $hari = $tglParts[0] ?? '';
    $tglNum = $tglParts[1] ?? '';
    $bulan = $tglParts[2] ?? '';
    $tahun = $tglParts[3] ?? '';
@endphp

<a href="{{ site_url('artikel/' . buat_slug($agenda)) }}" class="wagenda-card">
    {{-- Date badge kiri --}}
    <div class="wagenda-date-badge">
        <span class="wagenda-date-day">{{ $tglNum }}</span>
        <span class="wagenda-date-month">{{ $bulan }}</span>
    </div>

    {{-- Content --}}
    <div class="wagenda-card-body">
        <span class="{{ $badgeClass }} wagenda-badge">{{ $badge }}</span>
        <h4 class="wagenda-card-title">{{ $agenda['judul'] }}</h4>
        <div class="wagenda-card-meta">
            @if (!empty($agenda['lokasi_kegiatan']))
                <span><i class="fas fa-map-marker-alt"></i> {{ $agenda['lokasi_kegiatan'] }}</span>
            @endif
            @if (!empty($agenda['koordinator_kegiatan']))
                <span><i class="fas fa-user-tie"></i> {{ $agenda['koordinator_kegiatan'] }}</span>
            @endif
        </div>
    </div>

    {{-- Arrow --}}
    <span class="wagenda-arrow"><i class="fas fa-chevron-right"></i></span>
</a>
