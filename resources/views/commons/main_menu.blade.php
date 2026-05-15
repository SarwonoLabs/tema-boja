{{-- Navbar Top — Logo kiri, Menu tengah, Icons kanan --}}
@php
    $current_url = current_url();
    $uri_seg     = uri_string();
    $base_url    = rtrim(site_url('/'), '/');

    // Apakah kita sedang di halaman utama/beranda?
    $is_homepage = (empty($uri_seg) || $uri_seg === '/' || $uri_seg === 'index.php');
    // Double-check: bandingkan juga current_url dengan base
    $current_clean = rtrim($current_url, '/');
    if (!$is_homepage) {
        $is_homepage = ($current_clean === $base_url || $current_clean === rtrim(site_url(), '/'));
    }

    // Helper: ekstrak path dari URL untuk perbandingan yang lebih akurat
    $getPath = function($url) use ($base_url) {
        if (empty($url) || $url === '#!' || $url === '#') return false;
        $clean = rtrim($url, '/');
        // Jangan cocokkan homepage URL
        if ($clean === $base_url || $clean === $base_url . '/index.php') return false;
        // Ambil path setelah base_url
        $path = '';
        if (str_starts_with($clean, $base_url)) {
            $path = ltrim(substr($clean, strlen($base_url)), '/');
        }
        return $path ?: false;
    };

    $currentPath = $getPath($current_url) ?: $uri_seg;

    // Helper: cek apakah URL cocok dengan halaman saat ini
    $checkUrl = function($url) use ($current_url, $base_url, $getPath, $currentPath) {
        $menuPath = $getPath($url);
        if ($menuPath === false) return false;
        if (empty($currentPath)) return false;
        // Exact match atau current path dimulai dengan menu path + /
        return $currentPath === $menuPath || str_starts_with($currentPath, $menuPath . '/');
    };

    // Pre-compute: cek apakah ada menu SELAIN Beranda yang aktif
    $any_menu_active = false;
    $menus = menu_tema() ? array_slice(menu_tema(), 0, 6) : [];
    foreach ($menus as $m) {
        $has_dd = count($m['childrens'] ?? []) > 0;
        if (!$has_dd) {
            if ($checkUrl($m['link_url'])) { $any_menu_active = true; break; }
        } else {
            // Untuk dropdown: cek parent URL + semua children
            if ($checkUrl($m['link_url'])) { $any_menu_active = true; break; }
            foreach ($m['childrens'] as $ch) {
                if ($checkUrl($ch['link_url'])) { $any_menu_active = true; break 2; }
                foreach ($ch['childrens'] ?? [] as $gc) {
                    if ($checkUrl($gc['link_url'])) { $any_menu_active = true; break 3; }
                }
            }
        }
    }

    // Beranda aktif HANYA jika benar-benar di halaman utama DAN tidak ada menu lain yang cocok
    $beranda_active = !$any_menu_active && $is_homepage;
@endphp

<nav class="navbar-boja" role="navigation">
    <div class="container">
        <div class="navbar-inner">
            {{-- Brand / Logo + Nama Desa --}}
            <a href="{{ site_url('/') }}" class="navbar-brand">
                <img src="{{ gambar_desa($desa['logo']) }}" alt="{{ $desa['nama_desa'] }}" class="navbar-logo">
                <span class="navbar-brand-text">{{ ucfirst(setting('sebutan_desa')) }} {{ $desa['nama_desa'] }}</span>
            </a>

            {{-- Desktop Menu --}}
            <ul class="navbar-menu">
                <li class="{{ $beranda_active ? 'nav-active' : '' }}">
                    <a href="{{ site_url('/') }}" class="navbar-link">BERANDA</a>
                </li>
                @foreach ($menus as $menu)
                    @php
                        $has_dropdown = count($menu['childrens'] ?? []) > 0;
                        $menu_active = false;

                        if (!$has_dropdown) {
                            $menu_active = $checkUrl($menu['link_url']);
                        } else {
                            // Cek parent URL dulu (jika bukan #! / #)
                            if ($checkUrl($menu['link_url'])) { $menu_active = true; }
                            // Lalu cek semua children & grandchildren
                            if (!$menu_active) {
                                foreach ($menu['childrens'] as $ch) {
                                    if ($checkUrl($ch['link_url'])) { $menu_active = true; break; }
                                    foreach ($ch['childrens'] ?? [] as $gc) {
                                        if ($checkUrl($gc['link_url'])) { $menu_active = true; break 2; }
                                    }
                                }
                            }
                        }
                    @endphp
                    <li class="{{ $has_dropdown ? 'has-dropdown' : '' }}{{ $menu_active ? ' nav-active' : '' }}">
                        <a href="{{ $has_dropdown ? '#!' : $menu['link_url'] }}" class="navbar-link">
                            {!! strtoupper($menu['nama']) !!}
                            @if ($has_dropdown)
                                <i class="fas fa-chevron-down"></i>
                            @endif
                        </a>

                        @if ($has_dropdown)
                            <ul class="dropdown-menu">
                                @foreach ($menu['childrens'] as $child)
                                    @php $child_has = count($child['childrens'] ?? []) > 0 @endphp
                                    <li class="{{ $child_has ? 'has-dropdown' : '' }}">
                                        <a href="{{ $child_has ? '#!' : $child['link_url'] }}">
                                            <span>{!! $child['nama'] !!}</span>
                                            @if ($child_has)
                                                <i class="fas fa-chevron-right"></i>
                                            @endif
                                        </a>

                                        @if ($child_has)
                                            <ul class="dropdown-menu">
                                                @foreach ($child['childrens'] as $grandchild)
                                                    <li>
                                                        <a href="{{ $grandchild['link_url'] }}">
                                                            <span>{!! $grandchild['nama'] !!}</span>
                                                        </a>
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

            {{-- Right Icons --}}
            <div class="navbar-icons">
                @if (setting('layanan_mandiri') == 1)
                    <a href="{{ site_url('layanan-mandiri') }}" class="navbar-icon-btn" title="Layanan Mandiri">
                        <i class="fas fa-user"></i>
                    </a>
                @endif
                <a href="{{ site_url('siteman') }}" class="navbar-icon-btn" title="Login Admin">
                    <i class="fas fa-sign-in-alt"></i>
                </a>
            </div>

            {{-- Mobile toggle --}}
            <button type="button" class="navbar-toggle" id="mobileMenuToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</nav>
