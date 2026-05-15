{{-- Mobile Navigation Panel --}}
@php
    // Re-use variabel dari main_menu jika tersedia, otherwise compute ulang
    if (!isset($menus) || !isset($checkUrl) || !isset($beranda_active)) {
        $current_url = current_url();
        $uri_seg     = uri_string();
        $base_url    = rtrim(site_url('/'), '/');

        $is_homepage = (empty($uri_seg) || $uri_seg === '/' || $uri_seg === 'index.php');
        $current_clean = rtrim($current_url, '/');
        if (!$is_homepage) {
            $is_homepage = ($current_clean === $base_url || $current_clean === rtrim(site_url(), '/'));
        }

        $getPath = function($url) use ($base_url) {
            if (empty($url) || $url === '#!' || $url === '#') return false;
            $clean = rtrim($url, '/');
            if ($clean === $base_url || $clean === $base_url . '/index.php') return false;
            $path = '';
            if (str_starts_with($clean, $base_url)) {
                $path = ltrim(substr($clean, strlen($base_url)), '/');
            }
            return $path ?: false;
        };

        $currentPath = $getPath($current_url) ?: $uri_seg;

        $checkUrl = function($url) use ($current_url, $base_url, $getPath, $currentPath) {
            $menuPath = $getPath($url);
            if ($menuPath === false) return false;
            if (empty($currentPath)) return false;
            return $currentPath === $menuPath || str_starts_with($currentPath, $menuPath . '/');
        };

        $any_menu_active = false;
        $menus = menu_tema() ? array_slice(menu_tema(), 0, 6) : [];
        foreach ($menus as $m) {
            $has_dd = count($m['childrens'] ?? []) > 0;
            if (!$has_dd) {
                if ($checkUrl($m['link_url'])) { $any_menu_active = true; break; }
            } else {
                if ($checkUrl($m['link_url'])) { $any_menu_active = true; break; }
                foreach ($m['childrens'] as $ch) {
                    if ($checkUrl($ch['link_url'])) { $any_menu_active = true; break 2; }
                    foreach ($ch['childrens'] ?? [] as $gc) {
                        if ($checkUrl($gc['link_url'])) { $any_menu_active = true; break 3; }
                    }
                }
            }
        }
        $beranda_active = !$any_menu_active && $is_homepage;
    }
@endphp
<div class="mobile-panel" id="mobilePanel" x-data="{ menuOpen: false }" style="display:none;">
    <div class="mobile-panel-backdrop" @click="document.getElementById('mobilePanel').style.display='none'"></div>
    <div class="mobile-panel-body">
        <div class="mobile-panel-header">
            <a href="{{ site_url('/') }}" class="mobile-panel-brand">
                <img src="{{ gambar_desa($desa['logo']) }}" alt="{{ $desa['nama_desa'] }}">
                <span>{{ $desa['nama_desa'] }}</span>
            </a>
            <button type="button" class="mobile-panel-close" onclick="document.getElementById('mobilePanel').style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Search --}}
        <form action="{{ site_url('/') }}" class="mobile-search" role="search">
            <input type="text" name="cari" placeholder="Cari artikel...">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>

        <ul class="mobile-menu-list">
            <li class="{{ $beranda_active ? 'mob-active' : '' }}">
                <a href="{{ site_url('/') }}"><i class="fas fa-home"></i> Beranda</a>
            </li>

            @foreach ($menus as $menu)
                @php
                    $has_dropdown = count($menu['childrens'] ?? []) > 0;
                    $mob_active = false;
                    if (!$has_dropdown) {
                        $mob_active = $checkUrl($menu['link_url']);
                    } else {
                        if ($checkUrl($menu['link_url'])) { $mob_active = true; }
                        if (!$mob_active) {
                            foreach ($menu['childrens'] as $ch) {
                                if ($checkUrl($ch['link_url'])) { $mob_active = true; break; }
                                foreach ($ch['childrens'] ?? [] as $gc) {
                                    if ($checkUrl($gc['link_url'])) { $mob_active = true; break 2; }
                                }
                            }
                        }
                    }
                @endphp
                    <li class="{{ $mob_active ? 'mob-active' : '' }}" @if ($has_dropdown) x-data="{open: {{ $mob_active ? 'true' : 'false' }}}" @endif>
                        <a href="{{ $has_dropdown ? '#!' : $menu['link_url'] }}"
                           @if ($has_dropdown) @click.prevent="open = !open" @endif>
                            <span>{!! $menu['nama'] !!}</span>
                            @if ($has_dropdown)
                                <i class="fas fa-chevron-down" :class="{ 'rotate-180': open }" style="transition: transform 0.3s;"></i>
                            @endif
                        </a>

                        @if ($has_dropdown)
                            <ul x-show="open" x-transition class="mobile-submenu">
                                @foreach ($menu['childrens'] as $child)
                                    @php $child_has = count($child['childrens'] ?? []) > 0 @endphp
                                    <li @if ($child_has) x-data="{sub: false}" @endif>
                                        <a href="{{ $child_has ? '#!' : $child['link_url'] }}"
                                           @if ($child_has) @click.prevent="sub = !sub" @endif>
                                            <span>{!! $child['nama'] !!}</span>
                                            @if ($child_has)
                                                <i class="fas fa-chevron-down" :class="{ 'rotate-180': sub }" style="transition: transform 0.3s;"></i>
                                            @endif
                                        </a>

                                        @if ($child_has)
                                            <ul x-show="sub" x-transition class="mobile-submenu level-2">
                                                @foreach ($child['childrens'] as $grandchild)
                                                    <li>
                                                        <a href="{{ $grandchild['link_url'] }}">{!! $grandchild['nama'] !!}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
        </ul>

        <div class="mobile-panel-footer">
            @if (setting('layanan_mandiri') == 1)
                <a href="{{ site_url('layanan-mandiri') }}" class="btn btn-primary btn-block">Layanan Mandiri</a>
            @endif
            <a href="{{ site_url('siteman') }}" class="btn btn-outline btn-block">Login Admin</a>
        </div>
    </div>
</div>
