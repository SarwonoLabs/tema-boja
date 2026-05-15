@php
    $daftar_statistik = daftar_statistik();
    $slug_aktif = str_replace('_', '-', $slug_aktif);
    $s_links = [
        [
            'target' => 'statistikPenduduk',
            'label' => 'Statistik Penduduk',
            'icon' => 'fa-users',
            'submenu' => $daftar_statistik['penduduk'],
        ],
        [
            'target' => 'statistikKeluarga',
            'label' => 'Statistik Keluarga',
            'icon' => 'fa-home',
            'submenu' => $daftar_statistik['keluarga'],
        ],
        [
            'target' => 'statistikBantuan',
            'label' => 'Statistik Bantuan',
            'icon' => 'fa-hand-holding-heart',
            'submenu' => $daftar_statistik['bantuan'],
        ],
        [
            'target' => 'statistikLainnya',
            'label' => 'Statistik Lainnya',
            'icon' => 'fa-chart-area',
            'submenu' => $daftar_statistik['lainnya'],
        ],
    ];
@endphp

<div class="stat-nav-box">
    <div class="stat-nav-header">
        <i class="fas fa-chart-pie"></i>
        <span>Navigasi Statistik</span>
    </div>
    <div class="stat-nav-body">
        @foreach ($s_links as $idx => $statistik)
            @php $is_active = in_array($slug_aktif, array_column($statistik['submenu'], 'slug')) @endphp
            <div class="stat-nav-group {{ $is_active ? 'is-open' : '' }}">
                <button type="button" class="stat-nav-group-btn" data-target="{{ $statistik['target'] }}">
                    <span class="stat-nav-group-icon"><i class="fas {{ $statistik['icon'] }}"></i></span>
                    <span class="stat-nav-group-label">{{ $statistik['label'] }}</span>
                    <i class="fas fa-chevron-down stat-nav-arrow"></i>
                </button>
                <ul class="stat-nav-sub" id="{{ $statistik['target'] }}" {!! $is_active ? 'style="display:block"' : '' !!}>
                    @foreach ($statistik['submenu'] as $submenu)
                        @php
                            $stat_slug = in_array($statistik['target'], ['statistikBantuan', 'statistikLainnya']) ? str_replace('first/', '', $submenu['url']) : 'statistik/' . $submenu['key'];
                            if ($stat_slug == 'data-dpt') {
                                $stat_slug = 'dpt';
                            }
                        @endphp
                        @if (isset($statistik_aktif[$stat_slug]))
                            <li>
                                <a href="{{ site_url($submenu['url']) }}" class="{{ $submenu['slug'] == $slug_aktif ? 'active' : '' }}">
                                    <i class="fas fa-angle-right"></i>
                                    <span>{{ $submenu['label'] }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
$(function(){
    // Accordion toggle for sidenav groups
    $('.stat-nav-group-btn').on('click', function(){
        var $group = $(this).closest('.stat-nav-group');
        var $sub = $group.find('.stat-nav-sub');
        var isOpen = $group.hasClass('is-open');

        // Close all others
        $('.stat-nav-group').not($group).removeClass('is-open').find('.stat-nav-sub').slideUp(250);
        // Toggle current
        if (isOpen) {
            $group.removeClass('is-open');
            $sub.slideUp(250);
        } else {
            $group.addClass('is-open');
            $sub.slideDown(250);
        }
    });
});
</script>
@endpush
